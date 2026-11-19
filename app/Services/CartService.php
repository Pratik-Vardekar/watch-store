<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public const SESSION_KEY = 'guest_cart';

    public function getCartCount(): int
    {
        if (Auth::check()) {
            return (int) Cart::where('user_id', Auth::id())->whereHas('product')->count();
        }

        return count($this->getGuestCart());
    }

    public function getCartProductIds(): array
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())
                ->whereHas('product')
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return array_map('intval', array_keys($this->getGuestCart()));
    }

    public function isInCart(int $productId): bool
    {
        return in_array($productId, $this->getCartProductIds(), true);
    }

    public function getGuestCart(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function addItem(int $productId, int $qty = 1): int
    {
        $product = Product::findOrFail($productId);
        if ((int) ($product->quantity ?? 0) <= 0) {
            return $this->getCartCount();
        }

        $qty = max(1, $qty);

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $qty);
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => $qty,
                ]);
            }

            return $this->getCartCount();
        }

        $guestCart = $this->getGuestCart();
        $guestCart[$productId] = ($guestCart[$productId] ?? 0) + $qty;
        session([self::SESSION_KEY => $guestCart]);
        session()->save();

        return $this->getCartCount();
    }

    public function getCartItems(): Collection
    {
        if (Auth::check()) {
            return Cart::with(['product.gstSetting'])
                ->where('user_id', Auth::id())
                ->whereHas('product')
                ->latest()
                ->get();
        }

        $guestCart = $this->getGuestCart();
        if (empty($guestCart)) {
            return collect();
        }

        $products = Product::with('gstSetting')
            ->whereIn('id', array_keys($guestCart))
            ->get()
            ->keyBy('id');

        return collect($guestCart)->map(function ($qty, $productId) use ($products) {
            $product = $products->get((int) $productId);
            if (!$product) {
                return null;
            }

            return (object) [
                'id' => 'guest_' . $productId,
                'product_id' => (int) $productId,
                'quantity' => (int) $qty,
                'product' => $product,
                'is_guest' => true,
            ];
        })->filter()->values();
    }

    public function mergeGuestCartToUser(int $userId): void
    {
        $guestCart = $this->getGuestCart();
        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $productId => $qty) {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', (int) $qty);
            } else {
                Cart::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => (int) $qty,
                ]);
            }
        }

        session()->forget(self::SESSION_KEY);
    }

    public function updateGuestItem(int $productId, int $qty): void
    {
        $guestCart = $this->getGuestCart();

        if ($qty < 1) {
            unset($guestCart[$productId]);
        } else {
            $guestCart[$productId] = $qty;
        }

        session([self::SESSION_KEY => $guestCart]);
    }

    public function removeGuestItem(int $productId): void
    {
        $guestCart = $this->getGuestCart();
        unset($guestCart[$productId]);
        session([self::SESSION_KEY => $guestCart]);
    }
}
