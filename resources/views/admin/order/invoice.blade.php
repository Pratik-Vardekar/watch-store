<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->invoice_no ?? $order->order_id }}</title>
    @include('admin.order.partials.invoice-styles')
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 16px;
            text-align: left;
        }
        @page {
            margin: 12mm;
        }
    </style>
</head>
<body>
    @include('admin.order.partials.invoice-content')
</body>
</html>
