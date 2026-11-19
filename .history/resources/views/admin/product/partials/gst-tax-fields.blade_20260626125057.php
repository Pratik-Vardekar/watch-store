<div class="col-md-4">
    <label class="form-label">GST Slab</label>
    <select name="gst_setting_id" id="gst_setting_id" class="form-select">
        <option value="">No GST / Not Taxable</option>
        @foreach($gstSettings as $gst)
            <option value="{{ $gst->id }}"
                data-rate="{{ $gst->gst_percentage }}"
                {{ (string) old('gst_setting_id', $selectedGstId ?? '') === (string) $gst->id ? 'selected' : '' }}>
                {{ $gst->gst_percentage }}% GST
            </option>
        @endforeach
    </select>
    <small class="text-muted">Selling price is tax-inclusive. GST is shown on frontend only.</small>
</div>

<div class="col-12">
    <div class="alert alert-light border mb-0 py-2 px-3" id="taxPreviewBox" style="display:none;">
        <small class="text-muted d-block mb-1">Tax preview (from selling price)</small>
        <strong id="taxPreviewText"></strong>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sellingPriceInput = document.querySelector('input[name="selling_price"]');
    const gstSelect = document.getElementById('gst_setting_id');
    const taxPreviewBox = document.getElementById('taxPreviewBox');
    const taxPreviewText = document.getElementById('taxPreviewText');

    function updateTaxPreview() {
        if (!sellingPriceInput || !gstSelect || !taxPreviewBox || !taxPreviewText) {
            return;
        }

        const price = parseFloat(sellingPriceInput.value);
        const selected = gstSelect.options[gstSelect.selectedIndex];
        const rate = parseFloat(selected?.dataset?.rate || 0);

        if (!price || price <= 0 || !rate || rate <= 0) {
            taxPreviewBox.style.display = 'none';
            return;
        }

        const taxableAmount = price / (1 + (rate / 100));
        const taxAmount = price - taxableAmount;

        taxPreviewText.textContent =
            'Selling price ₹' + price.toFixed(2) +
            ' includes ₹' + taxAmount.toFixed(2) +
            ' tax (' + rate + '% GST). Taxable value: ₹' + taxableAmount.toFixed(2);

        taxPreviewBox.style.display = 'block';
    }

    sellingPriceInput?.addEventListener('input', updateTaxPreview);
    gstSelect?.addEventListener('change', updateTaxPreview);
    updateTaxPreview();
});
</script>
@endpush
