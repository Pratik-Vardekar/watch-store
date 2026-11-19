<div class="order-table-root" data-count="{{ $orders->total() }}">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 50px">#</th>
                        <th style="min-width: 130px">Order</th>
                        <th style="min-width: 220px">Customer</th>
                        <th style="min-width: 120px">Contact</th>
                        <th style="min-width: 120px">Status</th>
                        <th style="min-width: 150px">Payment</th>
                        <th style="min-width: 120px" class="text-end">Total</th>
                        <th style="min-width: 110px">Date</th>
                        <th style="min-width: 100px" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $status = strtolower($order->order_status ?? '');

                            // If COD and status is "pending" - override to "processing"
                            $showStatus = $status;
                            if (
                                (
                                    isset($order->payment_method) &&
                                    strtolower($order->payment_method) === 'cod'
                                ) &&
                                $status === 'pending'
                            ) {
                                $showStatus = 'processing';
                            }

                            $statusClass = match ($showStatus) {
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-info text-dark',
                                'processing' => 'bg-info text-dark',
                                'shipped' => 'bg-primary',
                                'delivered', 'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                'returns' => 'bg-secondary',
                                default => 'bg-secondary',
                            };

                            $shippingAddress = $order->addresses->firstWhere('type', 'shipping');
                            $contact = optional($order->user)->phone
                                ?? optional($shippingAddress)->phone
                                ?? '-';
                            $cancelPending = $order->cancel_requested_at && ! $order->cancel_approved_at && ! $order->cancel_rejected_at;
                            $returnPending = $order->return_requested_at && ! $order->return_approved_at && ! $order->return_rejected_at;
                        @endphp
                        <tr>
                            <td>
                                {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <a href="{{ route('order.show', $order) }}"
                                   class="text-primary text-decoration-none fw-bold">
                                    #{{ $order->order_id }}
                                </a>
                                @if($order->invoice_no)
                                    <div class="small text-muted">{{ $order->invoice_no }}</div>
                                @endif
                            </td>
                            <td>
                                @if($order->user)
                                    <div class="fw-semibold">{{ $order->user->name }}</div>
                                    <div class="text-muted small">{{ $order->user->email }}</div>
                                @else
                                    <em class="text-muted">Guest</em>
                                @endif
                            </td>
                            <td>{{ $contact }}</td>
                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $showStatus ? ucfirst($showStatus) : '-' }}
                                </span>
                                @if($cancelPending)
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark">Cancel Requested</span>
                                    </div>
                                @endif
                                @if($returnPending)
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark">{{ ucfirst($order->return_request_type ?? 'Return') }} Requested</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    @if($order->payment_type == 'partial')
                                        <span class="badge bg-success">Partial Completed</span>
                                    @else
                                        <span class="badge bg-success">Fully Paid</span>
                                    @endif
                                @elseif($order->payment_status == 'partial')
                                    <span class="badge bg-warning text-dark">Partially Paid</span>
                                    <div class="small text-danger mt-1">
                                        Due ₹{{ number_format($order->due_amount, 2) }}
                                    </div>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($order->payment_status ?? 'pending') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">
                                ₹{{ number_format($order->grand_total ?? 0, 2) }}
                            </td>
                            <td>
                                {{ optional($order->created_at)->format('d M Y') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('order.show', $order) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('order.pdf', $order) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Download Invoice PDF"
                                   download="invoice-{{ $order->order_id }}.pdf">
                                    <i class="ti ti-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-package fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No orders found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-2 border-top px-3 pb-3">
                <div class="text-muted small mb-3 mb-md-0">
                    Showing
                    <span class="fw-semibold">{{ $orders->firstItem() }}</span>
                    to
                    <span class="fw-semibold">{{ $orders->lastItem() }}</span>
                    of
                    <span class="fw-semibold">{{ $orders->total() }}</span>
                    orders
                </div>

                <nav aria-label="Order pagination">
                    <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-1">
                        @if ($orders->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link rounded-3">
                                    <i class="ti ti-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-3 order-pagination-link"
                                   href="{{ $orders->previousPageUrl() }}"
                                   rel="prev">
                                    <i class="ti ti-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        @php
                            $currentPage = $orders->currentPage();
                            $lastPage = $orders->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($start > 1)
                            <li class="page-item d-none d-sm-block">
                                <a class="page-link rounded-3 order-pagination-link"
                                   href="{{ $orders->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled d-none d-sm-block">
                                    <span class="page-link rounded-3">...</span>
                                </li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $currentPage)
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link rounded-3">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item d-none d-sm-block">
                                    <a class="page-link rounded-3 order-pagination-link"
                                       href="{{ $orders->url($i) }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <li class="page-item disabled d-none d-sm-block">
                                    <span class="page-link rounded-3">...</span>
                                </li>
                            @endif
                            <li class="page-item d-none d-sm-block">
                                <a class="page-link rounded-3 order-pagination-link"
                                   href="{{ $orders->url($lastPage) }}">{{ $lastPage }}</a>
                            </li>
                        @endif

                        @if ($orders->hasMorePages())
                            <li class="page-item">
                                <a class="page-link rounded-3 order-pagination-link"
                                   href="{{ $orders->nextPageUrl() }}"
                                   rel="next">
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link rounded-3">
                                    <i class="ti ti-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>

            <div class="d-flex d-sm-none justify-content-center pb-3">
                <div class="btn-group" role="group">
                    @if (!$orders->onFirstPage())
                        <a href="{{ $orders->previousPageUrl() }}"
                           class="btn btn-outline-primary btn-sm order-pagination-link">
                            <i class="ti ti-chevron-left"></i> Prev
                        </a>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="ti ti-chevron-left"></i> Prev
                        </button>
                    @endif

                    <span class="btn btn-light btn-sm border disabled">
                        Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                    </span>

                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}"
                           class="btn btn-outline-primary btn-sm order-pagination-link">
                            Next <i class="ti ti-chevron-right"></i>
                        </a>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            Next <i class="ti ti-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
