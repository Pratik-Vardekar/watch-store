@forelse(($availableCoupons ?? collect()) as $offer)
    @php
        $offerCoupon = $offer['coupon'];
        $isApplied = !empty($couponCode) && strtoupper($couponCode) === strtoupper($offerCoupon->code);
    @endphp

    <div class="border rounded-3 p-3 mb-3 {{ $offer['can_apply'] ? '' : 'opacity-75' }}">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <strong class="fs-5">{{ $offerCoupon->code }}</strong>
                    @if($offerCoupon->type === 'percent')
                        <span class="badge text-bg-orange">
                            {{ rtrim(rtrim(number_format($offerCoupon->value, 2), '0'), '.') }}% OFF
                        </span>
                    @else
                        <span class="badge text-bg-orange">
                            ₹{{ number_format($offerCoupon->value, 2) }} OFF
                        </span>
                    @endif
                    @if($isApplied)
                        <span class="badge text-bg-orange">Applied</span>
                    @endif
                </div>
                <small class="text-secondary d-block">
                    {{ $offer['applicability']['label'] ?? 'Valid on selected items' }}
                </small>
            </div>

            <div class="text-end">
                @if($offer['can_apply'])
                    <div class="txtorange fw-semibold mb-1">
                        You save ₹{{ number_format($offer['discount'], 2) }}
                    </div>
                @elseif(!empty($offer['message']))
                    <div class="text-danger small mb-1">{{ $offer['message'] }}</div>
                @endif

                @if($isApplied)
                    <button type="button" class="btn btn-sm btn-purple rounded-pill px-3" disabled>Applied</button>
                @elseif($offer['can_apply'])
                    <button type="button"
                            class="btn btn-sm btn-purple rounded-pill px-3 coupon-apply-btn"
                            data-code="{{ $offerCoupon->code }}">
                        Apply
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-purple rounded-pill px-3" disabled>Not applicable</button>
                @endif
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 small mb-3">
            <div>
                <span class="text-secondary">Min. order:</span>
                @if($offer['minimum_amount'])
                    <strong>₹{{ number_format($offer['minimum_amount'], 2) }}</strong>
                    @if($offer['meets_minimum'])
                        <span class="txtgreen">(met)</span>
                    @else
                        <span class="text-danger">(need ₹{{ number_format(max(0, $offer['minimum_amount'] - ($offer['eligible_subtotal'] ?? 0)), 2) }} more)</span>
                    @endif
                @else
                    <strong>None</strong>
                @endif
            </div>
            <div>
                <span class="text-secondary">Max. discount:</span>
                @if(!empty($offer['maximum_discount']))
                    <strong>₹{{ number_format($offer['maximum_discount'], 2) }}</strong>
                @else
                    <strong>No cap</strong>
                @endif
            </div>
            <div>
                <span class="text-secondary">Eligible price:</span>
                <strong>₹{{ number_format($offer['eligible_subtotal'], 2) }}</strong>
            </div>
        </div>

        {{-- <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr class="text-secondary small">
                        <th>Product</th>
                        <th class="text-end">MRP</th>
                        <th class="text-end">Selling</th>
                        <th class="text-end">Product save</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offer['eligible_items'] as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['name'] }}</strong>
                                <small class="d-block text-secondary">Qty {{ $item['quantity'] }}</small>
                            </td>
                            <td class="text-end">
                                @if($item['price'] > $item['selling_price'])
                                    <del class="text-secondary">₹{{ number_format($item['line_mrp'], 2) }}</del>
                                @else
                                    ₹{{ number_format($item['line_mrp'], 2) }}
                                @endif
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($item['line_selling'], 2) }}</td>
                            <td class="text-end txtgreen">
                                @if($item['product_save'] > 0)
                                    ₹{{ number_format($item['product_save'], 2) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
    </div>
@empty
    <div class="text-center py-4 text-secondary">
        {{ $emptyMessage ?? 'No coupons available for this order.' }}
    </div>
@endforelse
