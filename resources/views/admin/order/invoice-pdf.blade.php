<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 20px;
        }
        .invoice-document { width: 100%; font-size: 12px; color: #1f2937; line-height: 1.5; }
        .inv-layout { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .inv-layout td { vertical-align: top; padding: 0; border: none; }
        .inv-header { margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb; }
        .inv-header-left { width: 55%; }
        .inv-logo { width: 120px; max-width: 120px; height: auto; display: block; }
        .inv-header-right { width: 45%; text-align: right; }
        .inv-doc-title { font-size: 26px; font-weight: 700; color: #111827; letter-spacing: 1px; margin-bottom: 12px; }
        .inv-meta-box { text-align: left; background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; width: 240px; margin-left: auto; }
        .inv-meta { width: 100%; border-collapse: collapse; }
        .inv-meta .meta-key { color: #6b7280; padding: 4px 16px 4px 0; border: none; font-size: 12px; }
        .inv-meta .meta-val { font-weight: 600; color: #111827; text-align: right; padding: 4px 0; border: none; font-size: 12px; }
        .inv-meta td { padding: 4px 0; border: none; font-size: 12px; }
        .inv-block-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; margin-bottom: 6px; }
        .inv-company-block { margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
        .inv-company-name { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px; }
        .inv-company-line, .inv-address-line { font-size: 12px; color: #4b5563; margin-bottom: 2px; }
        .inv-addresses { margin-bottom: 24px; }
        .inv-address-col { width: 50%; padding-right: 20px !important; }
        .inv-address-col-right { padding-right: 0 !important; padding-left: 20px !important; border-left: 1px solid #e5e7eb; }
        .inv-address-name { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 4px; }
        .inv-items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .inv-items th { background: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; padding: 10px 12px; border-bottom: 2px solid #e5e7eb; text-align: left; }
        .inv-items td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 12px; vertical-align: top; }
        .inv-items tbody tr.inv-last-item td { border-bottom: 2px solid #e5e7eb; }
        .col-num { width: 5%; text-align: center; }
        .col-product { width: 45%; }
        .col-qty { width: 10%; text-align: center; }
        .col-price { width: 20%; text-align: right; }
        .col-amount { width: 20%; text-align: right; font-weight: 600; }
        .inv-sku { display: block; font-size: 10px; color: #9ca3af; margin-top: 2px; }
        .inv-bottom { margin-bottom: 20px; }
        .inv-bottom-left { width: 50%; padding-right: 24px !important; }
        .inv-bottom-right { width: 50%; }
        .inv-details { width: 100%; border-collapse: collapse; }
        .inv-details .detail-key { color: #6b7280; width: 45%; padding: 5px 0; border: none; font-size: 12px; }
        .inv-details .detail-val { font-weight: 500; color: #111827; padding: 5px 0; border: none; font-size: 12px; }
        .inv-details td { padding: 5px 0; border: none; font-size: 12px; }
        .inv-totals { width: 100%; border-collapse: collapse; background: #f9fafb; border: 1px solid #e5e7eb; }
        .inv-totals td { padding: 8px 14px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .inv-totals .total-key { color: #6b7280; }
        .inv-totals .total-val { text-align: right; font-weight: 500; color: #111827; }
        .inv-totals tr.inv-grand-total td { border-bottom: none; }
        .inv-grand-total td { background: #f3f4f6; font-weight: 700 !important; font-size: 14px !important; color: #111827 !important; padding: 12px 14px !important; }
        .inv-payments-label { margin-bottom: 10px; }
        .inv-payments th, .inv-payments td { text-align: left; }
        .inv-payments td.pay-amount { text-align: right; }
        .inv-footer { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    @include('admin.order.partials.invoice-content')
</body>
</html>