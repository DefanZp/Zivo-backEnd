<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Validation\ValidationException;


class CartService
{
    public function getCart(int $userId) 
    {
        $cart = $this->getOrCreateCart($userId);

        return $cart->load('items.product.category');
    }


    public function addItem(int $userId, int $productId, int $quantity = 1)
    {
        $product = Product::findOrFail($productId);

        $cart = $this->getOrCreateCart($userId);

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->first();

        $newQuantity = $quantity;

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
        }

        $this->validateStock($product, $newQuantity);

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return $cart->load('items.product.category');
    }

    public function increaseQuantity(int $userId, int $productId)
    {
        return $this->addItem($userId, $productId, 1);
    }

    public function decreaseQuantity(int $userId, int $productId)
    {
        $cart = $this->getOrCreateCart($userId);

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return $cart->load('items.product.category');
        }

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        }

        return $cart->load('items.product.category');
    }

    public function removeItem(int $userId, int $productId)
    {
        $cart = $this->getOrCreateCart($userId);

        $cart->items()
            ->where('product_id', $productId)
            ->delete();

        return $cart->load('items.product.category');
    }

    public function clearCart(int $userId)
    {
        $cart = $this->getOrCreateCart($userId);

        $cart->items()->delete();

        return $cart->load('items.product.category');
    }
    

    private function getOrCreateCart(int $userId)
    {
        return Cart::firstOrCreate([
            'user_id' => $userId
        ]);
    }

    private function validateStock(Product $product, int $quantity)
    {
        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => [
                    "Only {$product->stock} item(s) available for {$product->name}."
                ],
            ]);
        }
    }
}