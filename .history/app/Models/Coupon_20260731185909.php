<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'status',

        'category_ids',
        'product_ids',
        'exclude_category_ids',
        'exclude_product_ids',
    ];

    protected $casts = [
        'category_ids'          => 'array',
        'product_ids'           => 'array',
        'exclude_category_ids'  => 'array',
        'exclude_product_ids'   => 'array',

        'start_date'            => 'date',
        'end_date'              => 'date',
    ];

    public function categories()
    {
        return Category::whereIn(
            'id',
            $this->category_ids ?? []
        );
    }

    public function products()
    {
        return Product::whereIn(
            'id',
            $this->product_ids ?? []
        );
    }

    public function excludedCategories()
    {
        return Category::whereIn(
            'id',
            $this->exclude_category_ids ?? []
        );
    }

    public function excludedProducts()
    {
        return Product::whereIn(
            'id',
            $this->exclude_product_ids ?? []
        );
    }

    public function toApiArray(array $extra = []): array
    {
        return array_merge([
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount,
            'maximum_discount' => $this->maximum_discount,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'applicability' => $this->getApplicabilityDetails(),
        ], $extra);
    }

    public function getApplicabilityDetails(): array
    {
        $categoryIds = $this->normalizeIds($this->category_ids);
        $productIds = $this->normalizeIds($this->product_ids);
        $excludeCategoryIds = $this->normalizeIds($this->exclude_category_ids);
        $excludeProductIds = $this->normalizeIds($this->exclude_product_ids);

        $categories = Category::whereIn('id', $categoryIds)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->all();

        $products = Product::whereIn('id', $productIds)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->all();

        $excludedCategories = Category::whereIn('id', $excludeCategoryIds)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->all();

        $excludedProducts = Product::whereIn('id', $excludeProductIds)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->all();

        $hasInclude = !empty($categoryIds) || !empty($productIds);
        $hasExclude = !empty($excludeCategoryIds) || !empty($excludeProductIds);

        if (!$hasInclude && !$hasExclude) {
            $type = 'all';
            $label = 'Valid on all products';
        } elseif (!empty($productIds) && empty($categoryIds)) {
            $type = 'products';
            $label = 'Valid on selected products only';
        } elseif (!empty($categoryIds) && empty($productIds)) {
            $type = 'categories';
            $label = 'Valid on selected categories only';
        } else {
            $type = 'specific';
            $label = 'Valid on selected products and categories';
        }

        return [
            'type' => $type,
            'label' => $label,
            'categories' => $categories,
            'products' => $products,
            'excluded_categories' => $excludedCategories,
            'excluded_products' => $excludedProducts,
            'category_names' => array_column($categories, 'name'),
            'product_names' => array_column($products, 'name'),
            'excluded_category_names' => array_column($excludedCategories, 'name'),
            'excluded_product_names' => array_column($excludedProducts, 'name'),
        ];
    }

    public function isCurrentlyValid(): bool
    {
        if ((int) $this->status !== 1) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->start_date && $today < $this->start_date->toDateString()) {
            return false;
        }

        if ($this->end_date && $today > $this->end_date->toDateString()) {
            return false;
        }

        if (!empty($this->usage_limit) && (int) $this->used_count >= (int) $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function appliesToProduct(?Product $product): bool
    {
        if (!$product) {
            return false;
        }

        $excludeProductIds = $this->normalizeIds($this->exclude_product_ids);
        $excludeCategoryIds = $this->normalizeIds($this->exclude_category_ids);
        $productIds = $this->normalizeIds($this->product_ids);
        $categoryIds = $this->normalizeIds($this->category_ids);

        if (in_array((int) $product->id, $excludeProductIds, true)) {
            return false;
        }

        if (!empty($excludeCategoryIds) && array_intersect($product->allCategoryIds(), $excludeCategoryIds)) {
            return false;
        }

        $hasInclude = !empty($productIds) || !empty($categoryIds);
        if (!$hasInclude) {
            return true;
        }

        if (!empty($productIds) && in_array((int) $product->id, $productIds, true)) {
            return true;
        }

        if (!empty($categoryIds) && array_intersect($product->allCategoryIds(), $categoryIds)) {
            return true;
        }

        return false;
    }

    /**
     * Calculate discount for cart line items (objects with product + quantity).
     * Discount is always based on selling_price (falls back to price).
     *
     * @return array{eligible_subtotal: float, discount: float, message: string|null}
     */
    public function calculateDiscountForItems(Collection $cartItems, float $cartSubtotal): array
    {
        if (!$this->isCurrentlyValid()) {
            return [
                'eligible_subtotal' => 0,
                'discount' => 0,
                'message' => 'This coupon is not valid.',
            ];
        }

        $eligibleSubtotal = 0.0;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product ?? null;
            if (!$product || !$this->appliesToProduct($product)) {
                continue;
            }

            $sellingPrice = (float) ($product->selling_price ?? $product->price ?? 0);
            $eligibleSubtotal += $sellingPrice * (int) $cartItem->quantity;
        }

        if ($eligibleSubtotal <= 0) {
            return [
                'eligible_subtotal' => 0,
                'discount' => 0,
                'message' => 'This coupon does not apply to items in your cart.',
            ];
        }

        if (!empty($this->minimum_amount) && $cartSubtotal < (float) $this->minimum_amount) {
            return [
                'eligible_subtotal' => $eligibleSubtotal,
                'discount' => 0,
                'message' => 'Minimum order amount of ₹' . number_format((float) $this->minimum_amount, 2) . ' required.',
            ];
        }

        $discount = 0.0;

        if ($this->type === 'percent') {
            $discount = ($eligibleSubtotal * (float) $this->value) / 100;
            if (!empty($this->maximum_discount)) {
                $discount = min($discount, (float) $this->maximum_discount);
            }
        } else {
            $discount = (float) $this->value;
        }

        $discount = min(round($discount, 2), round($eligibleSubtotal, 2));

        return [
            'eligible_subtotal' => round($eligibleSubtotal, 2),
            'discount' => $discount,
            'message' => null,
        ];
    }

    /**
     * Build a cart-facing offer preview (eligible products with MRP / selling price).
     * Returns null when the coupon does not apply to any cart product.
     */
    public function buildAvailableOfferForCart(Collection $cartItems, float $cartSubtotal): ?array
    {
        if (!$this->isCurrentlyValid()) {
            return null;
        }

        $eligibleItems = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product ?? null;
            if (!$product || !$this->appliesToProduct($product)) {
                continue;
            }

            $mrp = (float) ($product->price ?? 0);
            $sellingPrice = (float) ($product->selling_price ?? $product->price ?? 0);
            $quantity = (int) $cartItem->quantity;

            $eligibleItems[] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $mrp,
                'selling_price' => $sellingPrice,
                'line_mrp' => round($mrp * $quantity, 2),
                'line_selling' => round($sellingPrice * $quantity, 2),
                'product_save' => round(max(0, ($mrp - $sellingPrice) * $quantity), 2),
            ];
        }

        if (empty($eligibleItems)) {
            return null;
        }

        $result = $this->calculateDiscountForItems($cartItems, $cartSubtotal);
        $minimumAmount = !empty($this->minimum_amount) ? (float) $this->minimum_amount : null;
        $maximumDiscount = !empty($this->maximum_discount) ? (float) $this->maximum_discount : null;
        $meetsMinimum = $minimumAmount === null || $cartSubtotal >= $minimumAmount;

        return [
            'coupon' => $this,
            'eligible_items' => $eligibleItems,
            'eligible_subtotal' => $result['eligible_subtotal'],
            'discount' => $result['discount'],
            'message' => $result['message'],
            'can_apply' => $result['discount'] > 0,
            'meets_minimum' => $meetsMinimum,
            'minimum_amount' => $minimumAmount,
            'maximum_discount' => $maximumDiscount,
            'applicability' => $this->getApplicabilityDetails(),
        ];
    }

    private function normalizeIds($ids): array
    {
        if (empty($ids)) {
            return [];
        }

        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }

        return array_values(array_filter(array_map('intval', (array) $ids)));
    }
}
