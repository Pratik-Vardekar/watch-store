<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ShiprocketService
{
    private const TOKEN_CACHE_KEY = 'shiprocket.api_token';

    private const FINAL_ORDER_STATUSES = ['delivered', 'completed', 'cancelled', 'returns'];

    public function isConfigured(): bool
    {
        return filled(config('services.shiprocket.email'))
            && filled(config('services.shiprocket.password'))
            && filled(config('services.shiprocket.pickup_location'));
    }

    public function autoCreateEnabled(): bool
    {
        return $this->isConfigured() && filter_var(config('services.shiprocket.auto_create', true), FILTER_VALIDATE_BOOLEAN);
    }

    public function trackingUrl(string $awb): string
    {
        return 'https://shiprocket.co/tracking/' . urlencode($awb);
    }

    /*
    |--------------------------------------------------------------------------
    | Serviceability
    |--------------------------------------------------------------------------
    */

    /**
     * Returns null when Shiprocket could not be reached, so callers can fall back
     * instead of blocking customers because of an API outage.
     *
     * @return array{serviceable: bool, estimated_days: ?int, couriers: array}|null
     */
    public function checkServiceability(string $deliveryPincode, bool $cod = false, ?float $weight = null): ?array
    {
        $pickupPincode = (string) config('services.shiprocket.pickup_pincode');

        if (! $this->isConfigured() || $pickupPincode === '') {
            return null;
        }

        $weight = $weight ?? (float) config('services.shiprocket.default_weight', 0.5);
        $cacheKey = sprintf('shiprocket.serviceability.v2.%s.%s.%d.%s', $pickupPincode, $deliveryPincode, (int) $cod, $weight);

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $response = $this->send('get', '/courier/serviceability/', [
                'pickup_postcode' => $pickupPincode,
                'delivery_postcode' => $deliveryPincode,
                'weight' => $weight,
                'cod' => $cod ? 1 : 0,
            ]);
        } catch (Throwable $e) {
            Log::warning('Shiprocket serviceability check failed: ' . $e->getMessage(), ['pincode' => $deliveryPincode]);

            return null;
        }

        $body = $response->json() ?? [];

        if ($response->status() === 404 || (int) ($body['status'] ?? 200) === 404) {
            $result = ['serviceable' => false, 'estimated_days' => null, 'couriers' => []];
        } elseif ($response->successful()) {
            $couriers = collect($body['data']['available_courier_companies'] ?? [])
                ->map(fn ($courier) => [
                    'id' => (int) ($courier['courier_company_id'] ?? 0),
                    'name' => (string) ($courier['courier_name'] ?? ''),
                    'rate' => (float) ($courier['rate'] ?? 0),
                    'estimated_days' => is_numeric($courier['estimated_delivery_days'] ?? null) ? (int) $courier['estimated_delivery_days'] : null,
                    'etd' => $courier['etd'] ?? null,
                    'cod' => (bool) ($courier['cod'] ?? false),
                ])
                ->values()
                ->all();

            // Use the courier Shiprocket would actually assign, not the fastest (often air) option.
            $recommendedId = (int) ($body['data']['recommended_courier_company_id'] ?? 0);
            $recommended = collect($couriers)->firstWhere('id', $recommendedId);
            $days = $recommended['estimated_days'] ?? collect($couriers)->pluck('estimated_days')->filter()->median();

            $result = [
                'serviceable' => $couriers !== [],
                'estimated_days' => $days ? (int) $days : null,
                'couriers' => $couriers,
            ];
        } else {
            Log::warning('Shiprocket serviceability returned an error.', [
                'pincode' => $deliveryPincode,
                'status' => $response->status(),
                'message' => $this->errorMessage($response),
            ]);

            return null;
        }

        Cache::put($cacheKey, $result, now()->addHours(6));

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Shipment lifecycle
    |--------------------------------------------------------------------------
    */

    public function createOrder(Order $order): Order
    {
        if ($order->shiprocket_order_id) {
            return $order;
        }

        $data = $this->request('post', '/orders/create/adhoc', $this->buildOrderPayload($order));

        if (empty($data['order_id']) || empty($data['shipment_id'])) {
            throw new RuntimeException($data['message'] ?? 'Shiprocket did not return an order/shipment ID.');
        }

        $order->update([
            'shiprocket_order_id' => (int) $data['order_id'],
            'shiprocket_shipment_id' => (int) $data['shipment_id'],
            'shiprocket_status' => strtoupper((string) ($data['status'] ?? 'NEW')),
            'shiprocket_error' => null,
            'shiprocket_synced_at' => now(),
        ]);

        return $order;
    }

    public function assignAwb(Order $order, ?int $courierId = null): Order
    {
        if (! $order->shiprocket_shipment_id) {
            throw new RuntimeException('Create the Shiprocket order before assigning a courier.');
        }

        $payload = ['shipment_id' => (int) $order->shiprocket_shipment_id];
        if ($courierId) {
            $payload['courier_id'] = $courierId;
        }

        $data = $this->request('post', '/courier/assign/awb', $payload);
        $awbData = $data['response']['data'] ?? [];

        if ((int) ($data['awb_assign_status'] ?? 0) !== 1 || empty($awbData['awb_code'])) {
            throw new RuntimeException(
                $awbData['awb_assign_error']
                    ?? $data['message']
                    ?? 'Shiprocket could not assign a courier. Check the wallet balance and courier serviceability.'
            );
        }

        $awb = (string) $awbData['awb_code'];

        $order->update([
            'tracking_id' => $awb,
            'courier_name' => $awbData['courier_name'] ?? $order->courier_name,
            'tracking_url' => $this->trackingUrl($awb),
            'shiprocket_courier_id' => isset($awbData['courier_company_id']) ? (int) $awbData['courier_company_id'] : null,
            'shiprocket_status' => 'AWB ASSIGNED',
            'shiprocket_error' => null,
            'shiprocket_synced_at' => now(),
        ]);

        return $order;
    }

    public function requestPickup(Order $order): Order
    {
        if (! $order->shiprocket_shipment_id) {
            throw new RuntimeException('Create the Shiprocket order before requesting a pickup.');
        }

        $response = $this->send('post', '/courier/generate/pickup', [
            'shipment_id' => [(int) $order->shiprocket_shipment_id],
        ]);
        $data = $response->json() ?? [];

        if (! $response->successful() || (int) ($data['pickup_status'] ?? 0) !== 1) {
            $message = $this->errorMessage($response);

            if (stripos($message, 'already') !== false) {
                return $order;
            }

            throw new RuntimeException($message);
        }

        $order->update([
            'shiprocket_pickup_scheduled_at' => $this->parseDate($data['response']['pickup_scheduled_date'] ?? null) ?? now(),
            'shiprocket_status' => 'PICKUP SCHEDULED',
            'shiprocket_synced_at' => now(),
        ]);

        return $order;
    }

    public function generateLabel(Order $order): string
    {
        if ($order->shiprocket_label_url) {
            return $order->shiprocket_label_url;
        }

        if (! $order->shiprocket_shipment_id) {
            throw new RuntimeException('This order has no Shiprocket shipment yet.');
        }

        $data = $this->request('post', '/courier/generate/label', [
            'shipment_id' => [(int) $order->shiprocket_shipment_id],
        ]);

        if (empty($data['label_url'])) {
            throw new RuntimeException($data['response'] ?? $data['message'] ?? 'Shiprocket did not return a label.');
        }

        $order->update(['shiprocket_label_url' => $data['label_url']]);

        return $data['label_url'];
    }

    public function track(Order $order): array
    {
        if (! $order->tracking_id) {
            throw new RuntimeException('This order has no AWB / tracking number yet.');
        }

        $data = $this->request('get', '/courier/track/awb/' . urlencode($order->tracking_id));
        $tracking = $data['tracking_data'] ?? [];

        $status = $tracking['shipment_track'][0]['current_status'] ?? null;
        if ($status) {
            $this->applyCourierStatus($order, $status);
        }

        if (! empty($tracking['track_url'])) {
            $order->update(['tracking_url' => $tracking['track_url']]);
        }

        return $tracking;
    }

    public function cancel(Order $order): Order
    {
        if (! $order->shiprocket_order_id) {
            throw new RuntimeException('This order was never sent to Shiprocket.');
        }

        $this->request('post', '/orders/cancel', [
            'ids' => [(int) $order->shiprocket_order_id],
        ]);

        $updates = [
            'shiprocket_order_id' => null,
            'shiprocket_shipment_id' => null,
            'shiprocket_courier_id' => null,
            'shiprocket_label_url' => null,
            'shiprocket_pickup_scheduled_at' => null,
            'shiprocket_status' => 'CANCELED',
            'shiprocket_error' => null,
            'shiprocket_synced_at' => now(),
            'tracking_id' => null,
            'courier_name' => null,
            'tracking_url' => null,
        ];

        if (strtolower((string) $order->order_status) === 'shipped') {
            $updates['order_status'] = 'processing';
            $updates['shipped_at'] = null;
        }

        $order->update($updates);

        return $order;
    }

    public function recordError(Order $order, Throwable $e): void
    {
        Log::error('Shiprocket error: ' . $e->getMessage(), ['order_id' => $order->id]);

        try {
            $order->update(['shiprocket_error' => mb_substr($e->getMessage(), 0, 1000)]);
        } catch (Throwable $ignored) {
            // Columns may not exist yet if the migration has not been run.
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Status sync (webhook + manual refresh)
    |--------------------------------------------------------------------------
    */

    public function applyCourierStatus(Order $order, string $status, array $details = []): void
    {
        $normalized = strtoupper(trim($status));

        $updates = [
            'shiprocket_status' => $normalized,
            'shiprocket_synced_at' => now(),
        ];

        if (! empty($details['awb']) && empty($order->tracking_id)) {
            $updates['tracking_id'] = (string) $details['awb'];
            $updates['tracking_url'] = $this->trackingUrl((string) $details['awb']);
        }

        if (! empty($details['courier_name']) && empty($order->courier_name)) {
            $updates['courier_name'] = (string) $details['courier_name'];
        }

        $currentStatus = strtolower((string) $order->order_status);

        if (! in_array($currentStatus, self::FINAL_ORDER_STATUSES, true)) {
            if ($normalized === 'DELIVERED') {
                $updates = array_merge($updates, $this->deliveredUpdates($order));
            } elseif ($this->isInTransitStatus($normalized)) {
                $updates['order_status'] = 'shipped';
                $updates['shipped_at'] = $order->shipped_at ?? now();
            }
        }

        $order->update($updates);
    }

    private function isInTransitStatus(string $status): bool
    {
        if (str_starts_with($status, 'RTO')) {
            return false;
        }

        return in_array($status, ['PICKED UP', 'SHIPPED', 'OUT FOR DELIVERY', 'REACHED AT DESTINATION HUB', 'DELAYED', 'MISROUTED'], true)
            || str_contains($status, 'TRANSIT');
    }

    private function deliveredUpdates(Order $order): array
    {
        $updates = [
            'order_status' => 'delivered',
            'delivered_at' => $order->delivered_at ?? now(),
            'returnable_until' => $order->returnable_until ?? now()->addDays(7)->toDateString(),
        ];

        $grandTotal = (float) $order->grand_total;
        $remainingDue = (float) ($order->due_amount > 0 ? $order->due_amount : max(0, $grandTotal - (float) ($order->paid_amount ?? 0)));

        if ($remainingDue > 0) {
            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $remainingDue,
                'payment_method' => $order->payment_method ?? 'cod',
                'status' => 'success',
                'paid_at' => now(),
                'note' => 'COD collected by courier (Shiprocket delivered)',
            ]);
        }

        if ($remainingDue > 0 || $order->payment_status !== 'paid') {
            $updates['paid_amount'] = $grandTotal;
            $updates['due_amount'] = 0;
            $updates['payment_status'] = 'paid';
        }

        return $updates;
    }

    /*
    |--------------------------------------------------------------------------
    | Payload
    |--------------------------------------------------------------------------
    */

    public function buildOrderPayload(Order $order): array
    {
        $order->loadMissing(['user', 'items.product', 'items.variant', 'addresses']);

        $address = $order->addresses->firstWhere('type', 'shipping')
            ?? $order->addresses->firstWhere('type', 'billing');

        if (! $address) {
            throw new RuntimeException('Order has no shipping address.');
        }

        $phone = substr(preg_replace('/\D+/', '', (string) $address->phone), -10);
        if (strlen($phone) !== 10) {
            throw new RuntimeException('Shipping phone number must have 10 digits.');
        }

        if (! preg_match('/^\d{6}$/', (string) $address->pincode)) {
            throw new RuntimeException('Shipping pincode must be 6 digits.');
        }

        $nameParts = preg_split('/\s+/', trim((string) ($address->name ?: $order->user?->name ?: 'Customer')), 2);

        $items = [];
        foreach ($order->valid_items as $item) {
            $units = max(1, (int) $item->quantity);
            $unitPrice = (float) $item->price > 0
                ? (float) $item->price
                : ((float) $item->total > 0 ? (float) $item->total / $units : (float) ($item->product->selling_price ?? $item->product->price ?? 0));

            $sku = $item->variant?->sku
                ?: $item->product?->sku
                ?: 'PRD-' . $item->product_id . ($item->variant_id ? '-' . $item->variant_id : '');

            if (isset($items[$sku])) {
                $items[$sku]['units'] += $units;
                continue;
            }

            $items[$sku] = [
                'name' => mb_substr((string) ($item->product->name ?? 'Watch'), 0, 200),
                'sku' => (string) $sku,
                'units' => $units,
                'selling_price' => round($unitPrice, 2),
                'discount' => 0,
                'tax' => '',
                'hsn' => '',
            ];
        }

        if ($items === []) {
            throw new RuntimeException('Order has no items.');
        }

        $itemsTotal = round(collect($items)->sum(fn ($row) => $row['selling_price'] * $row['units']), 2);
        $totalUnits = (int) collect($items)->sum('units');
        $shippingCharge = round((float) ($order->shipping_charge ?? 0), 2);

        // Shiprocket collects (items + shipping - total_discount) on COD, so the
        // advance already paid online is folded into the discount.
        $due = (float) $order->getPaymentBreakdown()['due'];
        $isCod = $due > 0;
        $amountToCollect = $isCod ? $due : (float) $order->grand_total;
        $totalDiscount = max(0, round($itemsTotal + $shippingCharge - $amountToCollect, 2));

        return [
            'order_id' => (string) ($order->order_id ?: 'ORD' . $order->id),
            'order_date' => ($order->created_at ?? now())->format('Y-m-d H:i'),
            'pickup_location' => (string) config('services.shiprocket.pickup_location'),
            'billing_customer_name' => $nameParts[0],
            'billing_last_name' => $nameParts[1] ?? '',
            'billing_address' => trim((string) $address->address),
            'billing_address_2' => trim((string) ($address->landmark ?? '')),
            'billing_city' => (string) $address->city,
            'billing_pincode' => (string) $address->pincode,
            'billing_state' => (string) $address->state,
            'billing_country' => (string) ($address->country ?: 'India'),
            'billing_email' => (string) ($order->user?->email ?? ''),
            'billing_phone' => $phone,
            'shipping_is_billing' => true,
            'order_items' => array_values($items),
            'payment_method' => $isCod ? 'COD' : 'Prepaid',
            'shipping_charges' => $shippingCharge,
            'giftwrap_charges' => 0,
            'transaction_charges' => 0,
            'total_discount' => $totalDiscount,
            'sub_total' => $itemsTotal,
            'length' => (float) config('services.shiprocket.default_length', 15),
            'breadth' => (float) config('services.shiprocket.default_breadth', 12),
            'height' => (float) config('services.shiprocket.default_height', 10),
            'weight' => round((float) config('services.shiprocket.default_weight', 0.5) * max(1, $totalUnits), 3),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    */

    private function request(string $method, string $path, array $data = []): array
    {
        $response = $this->send($method, $path, $data);

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        return $response->json() ?? [];
    }

    private function send(string $method, string $path, array $data = []): Response
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Shiprocket is not configured. Add SHIPROCKET_* values to .env.');
        }

        $response = $this->http($this->token())->{$method}($path, $data);

        if ($response->status() === 401) {
            $response = $this->http($this->token(true))->{$method}($path, $data);
        }

        return $response;
    }

    private function http(string $token)
    {
        return Http::baseUrl(rtrim((string) config('services.shiprocket.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->withToken($token);
    }

    private function token(bool $refresh = false): string
    {
        if ($refresh) {
            Cache::forget(self::TOKEN_CACHE_KEY);
        }

        // Shiprocket tokens are valid for 10 days.
        return Cache::remember(self::TOKEN_CACHE_KEY, now()->addDays(9), function () {
            $response = Http::baseUrl(rtrim((string) config('services.shiprocket.base_url'), '/'))
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post('/auth/login', [
                    'email' => config('services.shiprocket.email'),
                    'password' => config('services.shiprocket.password'),
                ]);

            $token = $response->json('token');

            if (! $response->successful() || empty($token)) {
                throw new RuntimeException('Shiprocket login failed: ' . $this->errorMessage($response));
            }

            return $token;
        });
    }

    private function errorMessage(Response $response): string
    {
        $body = $response->json();

        if (! is_array($body)) {
            return 'Shiprocket request failed (HTTP ' . $response->status() . ').';
        }

        $message = (string) ($body['message'] ?? 'Shiprocket request failed (HTTP ' . $response->status() . ').');

        if (! empty($body['errors']) && is_array($body['errors'])) {
            $details = collect($body['errors'])->flatten()->filter(fn ($value) => is_string($value))->implode(' ');
            if ($details !== '') {
                $message .= ' ' . $details;
            }
        }

        return trim($message);
    }

    private function parseDate($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable $e) {
            return null;
        }
    }
}
