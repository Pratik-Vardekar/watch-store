@extends('layouts.admin')

@section('content')

<div class="container-fluid px-4 py-4" style="max-width:1100px">

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-2 mb-3">
            <i class="ti ti-arrow-left"></i> Back
        </a>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <p class="text-muted mb-1" style="font-size:13px">Order Details</p>
                <h3 class="fw-bold mb-0">Order #{{ $order->order_id }}</h3>
                <p class="text-muted mb-0 mt-1" style="font-size:13px">
                    Placed on {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('h:i A') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <a href="{{ route('order.pdf', $order->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="ti ti-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    @include('admin.order.partials.customer-requests', ['order' => $order])

    <div class="row g-3 mb-3">
        {{-- Column 1 --}}
        <div class="col-lg-4 d-flex flex-column gap-3">
            {{-- Delivery Address --}}
            @php
            $shippingAddress = $order->addresses
                ->where('type', 'shipping')
                ->first();
            @endphp
            @if($shippingAddress)
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-map-pin text-muted"></i> Delivery Address
                    </h6>
                </div>
                <div class="card-body px-4 py-3">
                    <p class="fw-semibold mb-1">{{ $shippingAddress->name }}</p>
                    <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->phone }}</p>
                    <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->address }}</p>
                    @if($shippingAddress->landmark)
                        <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->landmark }}</p>
                    @endif
                    <p class="text-muted mb-0" style="font-size:13px">
                        {{ $shippingAddress->city }}, {{ $shippingAddress->state }} — {{ $shippingAddress->pincode }}
                    </p>
                </div>
            </div>
            @endif
            {{-- Payment Method & Status --}}
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-credit-card text-muted"></i> Payment &amp; Status
                    </h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="mb-2">
                        <span class="text-muted" style="font-size:12px;">Method:</span>
                        <span class="badge bg-secondary" style="font-size:12px;padding:5px 10px">
                            {{ strtoupper($order->payment_method ?? '-') }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted" style="font-size:12px;">Order Status:</span>
                        <form action="{{ route('order.status', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            @php
                                // Make "processing" the default selected if current status is empty or null for first time view
                                $currentStatus = $order->order_status ?? null;
                            @endphp
                            <select name="order_status"
                                    class="form-select form-select-sm d-inline w-auto"
                                    style="min-width:120px"
                                    onchange="this.form.submit()">
                                <option value="processing" {{ ($currentStatus === null || $currentStatus === '') ? 'selected' : ($currentStatus == 'processing' ? 'selected' : '') }}>Processing</option>
                                <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $currentStatus == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="shipped" {{ $currentStatus == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $currentStatus == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="returns" {{ $currentStatus == 'returns' ? 'selected' : '' }}>Returns</option>
                                <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size:12px;">Payment Status:</span>
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success" style="font-size:12px;padding:5px 14px">
                                Fully Paid
                            </span>
                        @elseif($order->payment_status == 'partial')
                            <span class="badge bg-warning text-dark" style="font-size:12px;padding:5px 14px">
                                Partial
                            </span>
                        @else
                            <span class="badge bg-secondary" style="font-size:12px;padding:5px 14px">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        {{-- Column 2 --}}
        <div class="col-lg-8 d-flex flex-column gap-3">

                        {{-- Ordered Products --}}
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-shopping-bag text-muted"></i> Ordered Products
                                </h6>
                                <span class="badge bg-light text-muted" style="font-size:12px">
                                    {{ $order->items->count() }} item(s)
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280" colspan="2">Product</th>
                                            <th class="text-center py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Qty</th>
                                            <th class="text-end py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Price</th>
                                            <th class="text-end px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $item)
                                        <tr>
                                            {{-- Product Image --}}
                                            <td class="ps-4 py-3" style="width:64px">
                                               @php $img = $item->product->thumbnail ?? null; @endphp
                                                @if($img)
                                                    <img src="{{ asset('backend/assets/images/product/' . $img) }}"
                                                        alt="{{ $item->product->name }}"
                                                        style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb">
                                                @else
                                                    <div style="width:48px;height:48px;border-radius:8px;background:#f3f4f6;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center">
                                                        <i class="ti ti-photo" style="font-size:18px;color:#9ca3af"></i>
                                                    </div>
                                                @endif
                                                                                </td>
                                            {{-- Product Name --}}
                                            <td class="py-3 fw-semibold" style="font-size:14px">
                                                {{ $item->product->name ?? '-' }}
                                                @if($item->product->sku ?? null)
                                                    <br><span class="text-muted fw-normal" style="font-size:11px">SKU: {{ $item->product->sku }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center py-3">{{ $item->quantity }}</td>
                                            <td class="text-end py-3 text-muted">₹{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end px-4 py-3 fw-semibold">₹{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="ti ti-inbox d-block mb-2" style="font-size:28px;opacity:.4"></i>
                                                No products found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
{{-- Payment Summary --}}
<div class="card border-0 shadow-sm h-100" style="background: #fafbfc;">
    <div class="card-header bg-white border-bottom py-2 px-4"
         style="border-radius:10px 10px 0 0;">

        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2"
            style="font-size:13px; color: #42464a;">

            <i class="ti ti-currency-rupee text-muted"></i>
            Payment Summary

        </h6>
    </div>

    <div class="card-body px-4 py-3"
         style="padding-top: 18px; padding-bottom: 18px;">

        <div class="table-responsive">

            <table class="table mb-0"
                   style="max-width:400px; min-width:260px; margin:auto; background:transparent;">

                <tbody>

                    {{-- Subtotal --}}
                    <tr>
                        <td class="text-muted border-0"
                            style="font-size:13px;
                                   padding:7px 12px 7px 7px;
                                   background:transparent;">
                            Subtotal
                        </td>

                        <td class="text-end border-0"
                            style="font-size:13px;
                                   padding:7px 7px 7px 12px;
                                   background:transparent;">

                            ₹{{ number_format($order->subtotal ?? 0, 2) }}

                        </td>
                    </tr>

                    {{-- Discount --}}
                    <tr>
                        <td class="text-muted border-0"
                            style="font-size:13px;
                                   padding:7px 12px 7px 7px;
                                   background:transparent;">
                            Discount
                        </td>

                        <td class="text-end border-0"
                            style="font-size:13px;
                                   padding:7px 7px 7px 12px;
                                   background:transparent;">

                            ₹{{ number_format($order->discount ?? 0, 2) }}

                        </td>
                    </tr>

                    {{-- GST using gstBreakdown --}}
                    <tr>
                        <td class="text-muted border-0"
                            style="font-size:13px;
                                   padding:7px 12px 7px 7px;
                                   background:transparent;">
                            GST (18% inclusive)
                        </td>
                    
                        <td class="text-end border-0"
                            style="font-size:13px;
                                   padding:7px 7px 7px 12px;
                                   background:transparent;">
                            ₹{{ number_format($gstBreakdown['gst_total'] ?? 0, 2) }}
                        </td>
                    </tr>

                    {{-- Grand Total --}}
                    <tr style="background:#f3f4f6;">
                        <td class="fw-bold border-0"
                            style="font-size:15px;
                                   padding:11px 12px 11px 7px;
                                   background:#f3f4f6;">
                            Grand Total
                        </td>
                    
                        <td class="fw-bold text-end border-0"
                            style="font-size:19px;
                                   padding:11px 7px 11px 12px;
                                   background:#f3f4f6;">
                            ₹{{ number_format($order->grand_total ?? 0, 2) }}
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</div>
       
        </div>
    </div>

    {{-- Transaction History --}}
    @if($order->orderPayments->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-history text-muted"></i> Transaction History
            </h6>
            <span class="badge bg-light text-muted" style="font-size:12px">
                {{ $order->orderPayments->count() }} transaction(s)
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Amount</th>
                        <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Method</th>
                        <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Stage</th>
                        <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Status</th>
                        <th class="text-end px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderPayments as $payment)
                    <tr>
                        <td class="px-4 py-3 fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border" style="font-size:11px;font-weight:600;letter-spacing:.3px">
                                {{ strtoupper($payment->payment_method ?? '-') }}
                            </span>
                        </td>
                        <td class="py-3 text-muted">{{ ucfirst($payment->payment_stage ?? '-') }}</td>
                        <td class="py-3">
                            @if($payment->status == 'success')
                                <span class="badge bg-success" style="font-size:12px;padding:5px 12px">Success</span>
                            @elseif($payment->status == 'failed')
                                <span class="badge bg-danger" style="font-size:12px;padding:5px 12px">Failed</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size:12px;padding:5px 12px">Pending</span>
                            @endif
                        </td>
                        <td class="text-end px-4 py-3 text-muted" style="font-size:13px">
                            {{ $payment->created_at->format('d M Y') }}<br>
                            <span style="font-size:12px">{{ $payment->created_at->format('h:i A') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Invoice Preview --}}
    <div class="collapse" id="orderInvoiceSection">
        <div class="card border-0 shadow-sm mb-4" id="order-invoice-preview">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-file-invoice text-muted"></i> Invoice
                </h6>
                <span class="text-muted" style="font-size:12px">
                    Invoice #{{ $order->invoice_no ?? 'INV-' . $order->order_id }}
                </span>
            </div>
            <div class="card-body px-4 py-4 invoice-preview-body">
                @include('admin.order.partials.invoice-content')
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
.card { border-radius: 12px; }
.card-header { border-radius: 12px 12px 0 0 !important; }
.table-hover tbody tr:hover { background: #f8fafc; }

.invoice-preview-body {
    font-size: 13px;
    color: #111;
    background: #fff;
}
.invoice-preview-body .invoice-document { width: 100%; }
.invoice-preview-body .invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
}
.invoice-preview-body .invoice-table th,
.invoice-preview-body .invoice-table td {
    border: 1px solid #d1d5db;
    padding: 8px;
    vertical-align: top;
}
.invoice-preview-body .text-right { text-align: right; }
.invoice-preview-body .text-center { text-align: center; }
.invoice-preview-body .align-top { vertical-align: top; }
.invoice-preview-body .heading { background: #f8fafc; font-weight: 600; }
.invoice-preview-body .company-logo {
    width: 130px;
    max-width: 100%;
    height: auto;
    margin-bottom: 10px;
    display: block;
}
.invoice-preview-body .invoice-title {
    margin: 0 0 10px;
    font-size: 20px;
}
.invoice-preview-body .invoice-subtitle {
    margin: 0 0 8px;
    font-size: 14px;
    font-weight: 600;
}
.invoice-preview-body .grand-total { background: #f8fafc; font-weight: 700; }
.invoice-preview-body .invoice-footer-note {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #d1d5db;
    text-align: center;
    color: #6b7280;
    font-size: 11px;
}

@media print {
    body * { visibility: hidden; }
    #order-invoice-preview,
    #order-invoice-preview * { visibility: visible; }
    #order-invoice-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
    #order-invoice-preview .card-header { display: none; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('printInvoiceBtn');
    const invoiceSection = document.getElementById('orderInvoiceSection');
    const invoiceCollapse = invoiceSection ? bootstrap.Collapse.getOrCreateInstance(invoiceSection, { toggle: false }) : null;

    printBtn?.addEventListener('click', function() {
        if (invoiceSection && !invoiceSection.classList.contains('show') && invoiceCollapse) {
            invoiceCollapse.show();
            invoiceSection.addEventListener('shown.bs.collapse', function handleShown() {
                invoiceSection.removeEventListener('shown.bs.collapse', handleShown);
                window.print();
            });
            return;
        }

        window.print();
    });
});
</script>
@endpush