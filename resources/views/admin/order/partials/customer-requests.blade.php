@if($order->cancel_requested_at || $order->return_requested_at)
<div class="card border-0 shadow-sm mb-3 {{ ($order->cancel_requested_at && !$order->cancel_approved_at && !$order->cancel_rejected_at) || ($order->return_requested_at && !$order->return_approved_at && !$order->return_rejected_at) ? 'border-warning' : '' }}" style="{{ ($order->cancel_requested_at && !$order->cancel_approved_at && !$order->cancel_rejected_at) || ($order->return_requested_at && !$order->return_approved_at && !$order->return_rejected_at) ? 'border:2px solid #ffc107 !important;' : '' }}">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
            <i class="ti ti-bell-ringing text-warning"></i> Customer Requests
        </h6>
        @if(($order->cancel_requested_at && !$order->cancel_approved_at && !$order->cancel_rejected_at) || ($order->return_requested_at && !$order->return_approved_at && !$order->return_rejected_at))
            <span class="badge bg-warning text-dark">Action Required</span>
        @endif
    </div>
    <div class="card-body px-4 py-3">
        @if($order->cancel_requested_at)
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <div class="fw-semibold">Cancel Request</div>
                        <div class="text-muted" style="font-size:12px">
                            Requested on {{ $order->cancel_requested_at->format('d M Y h:i A') }}
                        </div>
                    </div>
                    @if($order->cancel_approved_at)
                        <span class="badge bg-success">Approved</span>
                    @elseif($order->cancel_rejected_at)
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </div>
                <div class="mb-2" style="font-size:13px"><strong>Reason:</strong> {{ $order->cancel_request_reason ?? '-' }}</div>
                @if($order->cancel_request_note)
                    <div class="mb-2 text-muted" style="font-size:13px">{{ $order->cancel_request_note }}</div>
                @endif
                @if($order->cancel_decision_note)
                    <div class="mb-2 text-muted" style="font-size:13px"><strong>Decision note:</strong> {{ $order->cancel_decision_note }}</div>
                @endif
                @if(! $order->cancel_approved_at && ! $order->cancel_rejected_at)
                    <form action="{{ route('order.cancel_request.approve', $order->id) }}" method="POST" class="mb-2">
                        @csrf
                        <textarea name="decision_note" class="form-control form-control-sm mb-2" rows="2" placeholder="Approval note for customer (optional)"></textarea>
                        <button type="submit" class="btn btn-sm btn-success">Approve Cancel Request</button>
                    </form>
                    <form action="{{ route('order.cancel_request.reject', $order->id) }}" method="POST">
                        @csrf
                        <textarea name="decision_note" class="form-control form-control-sm mb-2" rows="2" placeholder="Rejection reason for customer (optional)"></textarea>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject Cancel Request</button>
                    </form>
                @endif
            </div>
        @endif

        @if($order->return_requested_at)
            <div class="border rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <div class="fw-semibold">{{ ucfirst($order->return_request_type ?? 'Return') }} Request</div>
                        <div class="text-muted" style="font-size:12px">
                            Requested on {{ $order->return_requested_at->format('d M Y h:i A') }}
                        </div>
                    </div>
                    @if($order->return_approved_at)
                        <span class="badge bg-success">Approved</span>
                    @elseif($order->return_rejected_at)
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </div>
                <div class="mb-2" style="font-size:13px"><strong>Reason:</strong> {{ $order->return_reason ?? '-' }}</div>
                @if($order->return_request_note)
                    <div class="mb-2 text-muted" style="font-size:13px">{{ $order->return_request_note }}</div>
                @endif
                @if($order->pickup_date)
                    <div class="mb-2 text-muted" style="font-size:13px"><strong>Pickup date:</strong> {{ $order->pickup_date->format('d M Y') }}</div>
                @endif
                @if($order->return_decision_note)
                    <div class="mb-2 text-muted" style="font-size:13px"><strong>Decision note:</strong> {{ $order->return_decision_note }}</div>
                @endif
                @if(! $order->return_approved_at && ! $order->return_rejected_at)
                    <form action="{{ route('order.return_request.approve', $order->id) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="date" name="pickup_date" class="form-control form-control-sm mb-2">
                        <textarea name="decision_note" class="form-control form-control-sm mb-2" rows="2" placeholder="Approval note for customer (optional)"></textarea>
                        <button type="submit" class="btn btn-sm btn-success">Approve Return / Exchange</button>
                    </form>
                    <form action="{{ route('order.return_request.reject', $order->id) }}" method="POST">
                        @csrf
                        <textarea name="decision_note" class="form-control form-control-sm mb-2" rows="2" placeholder="Rejection reason for customer (optional)"></textarea>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject Return / Exchange</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
@endif
