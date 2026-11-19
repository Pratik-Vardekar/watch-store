<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    public const SESSION_KEY = 'guest_wishlist';

    public function getCount(): int
    {
        return count($this->getProductIds());
    }

    public function getProductIds(): array
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return $this->getGuestWishlist();
    }

    public function isInWishlist(int $productId): bool
    {
        return in_array($productId, $this->getProductIds(), true);
    }

    public function getGuestWishlist(): array
    {
        return array_values(array_unique(array_map(
            'intval',
            session(self::SESSION_KEY, [])
        )));
    }

    public function getItems(): Collection
    {
        if (Auth::check()) {
            return Wishlist::with(['product.brand', 'product.category'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $ids = $this->getGuestWishlist();
        if (empty($ids)) {
            return collect();
        }

        $products = Product::with(['brand', 'category'])
            ->where('status', 1)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)->map(function ($productId) use ($products) {
            $product = $products->get($productId);
            if (! $product) {
                return null;
            }

            return (object) [
                'id' => 'guest_' . $productId,
                'product_id' => $productId,
                'product' => $product,
                'is_guest' => true,
            ];
        })->filter()->values();
    }

    public function add(int $productId): int
    {
        Product::where('status', 1)->findOrFail($productId);

        if (Auth::check()) {
            Wishlist::firstOrCreate([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);

            return $this->getCount();
        }

        $ids = $this->getGuestWishlist();
        if (! in_array($productId, $ids, true)) {
            array_unshift($ids, $productId);
            session([self::SESSION_KEY => $ids]);
        }

        return $this->getCount();
    }

    public function remove(int $productId): int
    {
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();

            return $this->getCount();
        }

        $ids = array_values(array_filter(
            $this->getGuestWishlist(),
            fn ($id) => $id !== $productId
        ));
        session([self::SESSION_KEY => $ids]);

        return $this->getCount();
    }

    public function toggle(int $productId): bool
    {
        if ($this->isInWishlist($productId)) {
            $this->remove($productId);

            return false;
        }

        $this->add($productId);

        return true;
    }

    public function mergeGuestWishlistToUser(int $userId): void
    {
        $ids = $this->getGuestWishlist();
        if (empty($ids)) {
            return;
        }

        foreach ($ids as $productId) {
            Wishlist::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        session()->forget(self::SESSION_KEY);
    }
}
