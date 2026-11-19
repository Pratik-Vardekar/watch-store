@php
    if (!isset($discount)) {
        $discount = (int) ($product->discount ?? 0);
        $mrp = (float) ($product->price ?? 0);
        $sell = (float) ($product->selling_price ?? 0);
        if ($sell <= 0) {
            $sell = $mrp;
        }

        if ($sell == $mrp) {
            $discount = 0;
        } elseif ($discount <= 0 && $mrp > $sell && $mrp > 0) {
            $discount = (int) round((($mrp - $sell) / $mrp) * 100);
        }
    }
    $discount = (int) $discount;
    $discountOnly = (bool) ($discountOnly ?? false);
@endphp

<div class="pibLabels">
    @unless($discountOnly)
        @if($product->is_new_arrival || $product->is_new)
            <div class="pibTag new">NEW</div>
        @endif
    @endunless

    @if($discount > 0)
        <div class="pibTag offer">
            <span>{{ $discount }}%</span>
            <small class="d-block">Off</small>
        </div>
    @endif
</div>
