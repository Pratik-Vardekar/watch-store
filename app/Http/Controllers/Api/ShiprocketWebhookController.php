<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiprocketWebhookController extends Controller
{
    public function __invoke(Request $request, ShiprocketService $shiprocket)
    {
        $expectedToken = (string) config('services.shiprocket.webhook_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, (string) $request->header('x-api-key'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $awb = (string) $request->input('awb', '');
        $channelOrderId = (string) $request->input('order_id', '');
        $shiprocketOrderId = $request->input('sr_order_id');
        $status = (string) ($request->input('current_status') ?: $request->input('shipment_status', ''));

        if ($status === '' || ($awb === '' && ! $shiprocketOrderId && $channelOrderId === '')) {
            return response()->json(['message' => 'Ignored']);
        }

        $order = Order::query()
            ->when($awb !== '', fn ($query) => $query->orWhere('tracking_id', $awb))
            ->when($shiprocketOrderId, fn ($query) => $query->orWhere('shiprocket_order_id', (int) $shiprocketOrderId))
            ->when($channelOrderId !== '', fn ($query) => $query->orWhere('order_id', $channelOrderId))
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Ignored']);
        }

        // Return (reverse) shipments must not move the forward order status.
        if ($request->boolean('is_return')) {
            $order->update(['shiprocket_status' => 'RETURN: ' . strtoupper($status), 'shiprocket_synced_at' => now()]);

            return response()->json(['message' => 'OK']);
        }

        try {
            $shiprocket->applyCourierStatus($order, $status, [
                'awb' => $awb,
                'courier_name' => $request->input('courier_name'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Shiprocket webhook failed: ' . $e->getMessage(), ['order_id' => $order->id, 'payload' => $request->all()]);
        }

        return response()->json(['message' => 'OK']);
    }
}
