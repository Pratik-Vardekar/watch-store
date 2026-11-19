@php
    $shippingAddress = $order->addresses->where('type', 'shipping')->first();
    $gstBreakdown = $order->gstBreakdown();

    $company = [
        'name' => 'WATCH STORE',
        'address' => 'Mira Road, Mumbai 401107, Maharashtra, India',
        'city' => 'Mira Road',
        'state' => 'Maharashtra',
        'phone' => '+91 9987171694',
        'gstin' => 'U32409MH2023PTC411642',
    ];

    $logoCandidates = [
        public_path('frontend/assets/images/tws_logo.png'),
        public_path('frontend/assets/0images/tws_logo.png'),
        public_path('frontend/assets/images/tws_logo.png'),
    ];

    $logoSrc = null;
    foreach ($logoCandidates as $logoPath) {
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            break;
        }
    }

    $customerEmail = $order->user->email ?? '';
    $showCustomerEmail = $customerEmail
        && !str_contains($customerEmail, '@temp.com')
        && !preg_match('/^user_[a-f0-9]+@/i', $customerEmail);

    $customerPhone = $order->user->phone ?? ($shippingAddress?->phone ?? '');
@endphp

<div class="invoice-document">
    <table class="invoice-table invoice-header-table">
        <tr class="heading">
            <td colspan="2">Tax Invoice</td>
        </tr>
        <tr>
            <td width="55%" class="align-top">
                <div class="company-block">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="company-logo" alt="{{ $company['name'] }}">
                    @endif
                    <div class="company-name">
                        WATCH Store
                    </div>
                    <div class="company-line">{{ $company['address'] }}</div>
                    <div class="company-line">{{ $company['city'] }}, {{ $company['state'] }}</div>
                    <div class="company-line">Phone: {{ $company['phone'] }}</div>
                    <div class="company-line">GSTIN: {{ $company['gstin'] }}</div>
                </div>
            </td>
            <td width="45%" class="align-top invoice-details-cell">
                <div class="invoice-detail-line"><strong>Invoice No:</strong> {{ $order->invoice_no ?? 'INV-' . $order->order_id }}</div>
                <div class="invoice-detail-line"><strong>Order ID:</strong> {{ $order->order_id }}</div>
                <div class="invoice-detail-line"><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</div>
                <div class="invoice-detail-line"><strong>Payment:</strong> {{ strtoupper($order->payment_status ?? '-') }}</div>
            </td>
        </tr>
    </table>

    <table class="invoice-table">
        <tr class="heading">
            <td width="50%">Bill To</td>
            <td width="50%">Ship To</td>
        </tr>
        <tr>
            <td class="compact-cell">
                <strong>{{ $order->user->name ?? 'Guest' }}</strong>
                @if($customerPhone)
                    <br><span class="contact-line">Phone: {{ $customerPhone }}</span>
                @endif
                @if($showCustomerEmail)
                    <br><span class="contact-line">Email: {{ $customerEmail }}</span>
                @endif
            </td>
            <td class="compact-cell">
                @if($shippingAddress)
                    <strong>{{ $shippingAddress->name }}</strong><br>
                    {{ $shippingAddress->address }}@if($shippingAddress->landmark), {{ $shippingAddress->landmark }}@endif<br>
                    {{ $shippingAddress->city }}, {{ $shippingAddress->state }} - {{ $shippingAddress->pincode }}<br>
                    Phone: {{ $shippingAddress->phone }}
                @endif
            </td>
        </tr>
    </table>

    <table class="invoice-table invoice-items-table">
        <thead>
            <tr class="heading">
                <th width="4%">#</th>
                <th width="46%">Product</th>
                <th width="8%">Qty</th>
                <th width="21%">Price</th>
                <th width="21%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{ $item->product->name ?? '-' }}
                    @if($item->product->sku ?? false)
                        <small> ({{ $item->product->sku }})</small>
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="invoice-table invoice-summary-table">
        <tr>
            <td width="62%" rowspan="6" class="compact-cell" style="vertical-align:top">
                <strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? '-') }}<br>
                <strong>Payment Status:</strong> {{ strtoupper($order->payment_status ?? '-') }}<br>
                <strong>Order Status:</strong> {{ strtoupper($order->order_status ?? '-') }}
            </td>
            <td width="19%">{{ $gstBreakdown['gst_rate_label'] ?? 'GST (18% inclusive)' }}</td>
            <td width="19%">₹{{ number_format($gstBreakdown['gst_total'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td>₹{{ number_format($order->discount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Paid</td>
            <td>₹{{ number_format($order->paid_amount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ $gstBreakdown['gst_rate_label'] }}</td>
            <td>₹{{ number_format($gstBreakdown['gst_total'], 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total</td>
            <td>₹{{ number_format($order->grand_total, 2) }}</td>
        </tr>
    </table>

    @if($order->orderPayments->count())
    <table class="invoice-table invoice-payments-table">
        <thead>
            <tr class="heading">
                <th colspan="4">Transaction History</th>
            </tr>
            <tr class="heading">
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderPayments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                <td>₹{{ number_format($payment->amount, 2) }}</td>
                <td>{{ strtoupper($payment->payment_method ?? '-') }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="invoice-footer-note">
        This is a computer-generated invoice and does not require a signature.
    </div>
</div>
