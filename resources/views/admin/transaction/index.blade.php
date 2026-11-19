@extends('layouts.admin')

@section('title', 'Transactions')

@section('content')

<div class="container-fluid my-2">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Paid Transactions</h4>
        </div>

        <div class="card-body">

            {{-- ================= FILTER FORM ================= --}}
            <form method="GET" class="row g-2 mb-3">

                {{-- Search Order ID --}}
                <div class="col-md-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search Order ID"
                           value="{{ request('search') }}">
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">Paid Transactions</option>
                    </select>
                </div>

                {{-- Payment Method --}}
                <div class="col-md-2">
                    <select name="payment_method" class="form-control">
                        <option value="">All Methods</option>
                        <option value="razorpay" {{ request('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                        <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>

                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>

            {{-- ================= TABLE ================= --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>

                                <td>
                                    <a href="{{ route('order.show', $transaction->order_id) }}">
                                        {{ $transaction->order->order_id ?? '-' }}
                                    </a>
                                </td>

                                <td>
                                    {{ $transaction->order->user->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $transaction->transaction_id ?? '-' }}
                                </td>

                                <td>
                                    ₹{{ number_format($transaction->amount, 2) }}
                                </td>

                                <td>
                                    {{ ucfirst($transaction->payment_method) }}
                                </td>

                                <td>
                                    {{ ucfirst($transaction->payment_stage) }}
                                </td>

                                <td>
                                    @if($transaction->status == 'success')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($transaction->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $transaction->paid_at
                                        ? \Carbon\Carbon::parse($transaction->paid_at)->format('d M Y h:i A')
                                        : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    No Transactions Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- ================= PAGINATION ================= --}}
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $transactions->firstItem() ?? 0 }}
                        to {{ $transactions->lastItem() ?? 0 }}
                        of {{ $transactions->total() }} results
                    </div>
            
                    <div>
                        {{ $transactions->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection