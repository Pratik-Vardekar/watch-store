<div class="col-md-3">
    <label class="form-label">GST Slab</label>
    <select name="gst_setting_id" id="gst_setting_id" class="form-select">
        <option value="" data-rate="18" {{ empty(old('gst_setting_id', $selectedGstId ?? '')) ? 'selected' : '' }}>
            18% GST (Default Included)
        </option>
        @if(isset($gstSettings) && count($gstSettings) > 0)
            @foreach($gstSettings as $gst)
                <option value="{{ $gst->id }}"
                    data-rate="{{ $gst->gst_percentage }}"
                    {{ (string) old('gst_setting_id', $selectedGstId ?? '') === (string) $gst->id ? 'selected' : '' }}>
                    {{ $gst->gst_percentage }}% GST (Included)
                </option>
            @endforeach
        @endif
    </select>
    <small class="text-muted">Selling price is tax-inclusive (18% GST default).</small>
</div>

<div class="col-12">
    <div class="p-3 bg-light rounded border" id="pricingBreakdownBox">
        <div class="row g-2 align-items-center">
            <div class="col-md-3 col-6">
                <div class="small text-muted">MRP / Regular Price</div>
                <div class="fw-bold fs-6" id="previewMrp">₹0.00</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="small text-muted">Selling Price (Incl. GST)</div>
                <div class="fw-bold fs-6 text-primary" id="previewSellingPrice">₹0.00</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="small text-muted">Discount</div>
                <div class="fw-bold fs-6 text-success" id="previewDiscount">0% (Save ₹0.00)</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="small text-muted" id="previewGstRateLabel">GST (18% Included)</div>
                <div class="fw-bold fs-6 text-dark" id="previewGstAmount">₹0.00 <span class="text-muted fw-normal small">(Base: ₹0.00)</span></div>
            </div>
        </div>
    </div>
</div>
