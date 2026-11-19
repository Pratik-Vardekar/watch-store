@php
    $shippingAddress = $order->addresses->where('type', 'shipping')->first();
    $gstBreakdown = $order->gstBreakdown();

    $company = [
        'name' => 'The Watch Store',
        'address' =>
            'Shop no 9/10, B 69/70 , Nitin Chs,Shanti Nagar Sector 1,Opp Tmt Bus Stop,Next To Bata Store,Mira Road ( East ),Thane - 401107,Maharashtra',
        'state' => 'Maharashtra',
        'phone' => '+91 8850134827',
        'gstin' => '27AHYPD6646G1ZU',
    ];

    $logoCandidates = [
        base_path('frontend/assets/images/tws_logo.png'),
        public_path('frontend/assets/images/tws_logo.png'),
        base_path('public/frontend/assets/images/tws_logo.png'),
    ];

    $logoSrc = null;
    foreach ($logoCandidates as $logoPath) {
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            break;
        }
    }

    if (!$logoSrc) {
        $logoSrc = asset('frontend/assets/images/tws_logo.png');
    }

    $customerEmail = $order->user->email ?? '';
    $showCustomerEmail =
        $customerEmail &&
        !str_contains($customerEmail, '@temp.com') &&
        !preg_match('/^user_[a-f0-9]+@/i', $customerEmail);

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
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" class="company-logo" alt="{{ $company['name'] }}">
                    @endif
                    <div class="company-name">
                        {{ $company['name'] }}
                    </div>
                    <div class="company-line">{{ $company['address'] }}</div>
                    {{-- <div class="company-line">{{ $company['city'] }}, {{ $company['state'] }}</div> --}}
                    <div class="company-line">Phone: {{ $company['phone'] }}</div>
                    <div class="company-line">GSTIN: {{ $company['gstin'] }}</div>
                </div>
            </td>
            <td width="45%" class="align-top invoice-details-cell">
                <div class="invoice-detail-line"><strong>Invoice No:</strong>
                    {{ $order->invoice_no }}</div>
                <div class="invoice-detail-line"><strong>Order ID:</strong> {{ $order->order_id }}</div>
                <div class="invoice-detail-line"><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</div>
                <div class="invoice-detail-line"><strong>Payment:</strong>
                    {{ strtoupper($order->payment_status ?? '-') }}</div>
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
                @if ($customerPhone)
                    <br><span class="contact-line">Phone: {{ $customerPhone }}</span>
                @endif
                @if ($showCustomerEmail)
                    <br><span class="contact-line">Email: {{ $customerEmail }}</span>
                @endif
            </td>
            <td class="compact-cell">
                @if ($shippingAddress)
                    <strong>{{ $shippingAddress->name }}</strong><br>
                    {{ $shippingAddress->address }}@if ($shippingAddress->landmark)
                        , {{ $shippingAddress->landmark }}
                    @endif
                    <br>
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
            @php
                $invoiceItems = $order->valid_items;
            @endphp
            @foreach ($invoiceItems as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->product->name ?? '-' }}
                        @if ($item->product->sku ?? false)
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

    @php
        $breakdown = $order->getPaymentBreakdown();
        $paidAmount = $breakdown['paid'];
        $dueAmount = $breakdown['due'];
        $isPaid = $breakdown['is_paid'];
        $isPartial = $breakdown['is_partial'];
    @endphp

    <table class="invoice-table invoice-summary-table">
        <tr>
            <td width="60%" rowspan="7" class="compact-cell" style="vertical-align:top">
                <strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? '-') }}<br>
                <strong>Payment Status:</strong> {{ strtoupper($order->payment_status ?? '-') }}<br>
                <strong>Order Status:</strong> {{ strtoupper($order->order_status ?? '-') }}<br>
                @if ($order->payment_type)
                    <strong>Payment Type:</strong> {{ ucfirst($order->payment_type) }}<br>
                @endif
                @if ($dueAmount > 0)
                    <div
                        style="margin-top: 6px; padding: 4px 7px; background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; font-weight: bold; font-size: 10px; border-radius: 3px;">
                        Remaining COD Balance: ₹{{ number_format($dueAmount, 2) }}
                    </div>
                @else
                    <div
                        style="margin-top: 6px; padding: 4px 7px; background: #dcfce7; border: 1px solid #86efac; color: #166534; font-weight: bold; font-size: 10px; border-radius: 3px;">
                        Payment Status: Fully Paid (₹0.00 Due)
                    </div>
                @endif
            </td>
            <td width="20%">Subtotal</td>
            <td width="20%">₹{{ number_format($order->subtotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td>-₹{{ number_format($order->discount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Shipping</td>
            <td>₹{{ number_format($order->shipping_charge ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ $gstBreakdown['gst_rate_label'] ?? 'GST (18% inclusive)' }}</td>
            <td>₹{{ number_format($gstBreakdown['gst_total'] ?? 0, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total</td>
            <td>₹{{ number_format($order->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount</strong></td>
            <td style="font-weight: bold; color: #047857;">₹{{ number_format($paidAmount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Due Amount</strong></td>
            <td style="font-weight: bold; color: {{ $dueAmount > 0 ? '#b91c1c' : '#111827' }};">
                ₹{{ number_format($dueAmount, 2) }}</td>
        </tr>
    </table>

    <div class="invoice-footer-note text-center">
        This is a computer-generated invoice and does not require a signature.
    </div>
</div>