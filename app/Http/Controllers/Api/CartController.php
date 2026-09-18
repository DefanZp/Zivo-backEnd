<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart(
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'data' => $cart
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cartService->addItem(
            $request->user()->id,
            $validatedData['product_id'],
            $validatedData['quantity']
        );

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $cart,
        ]);
    }

    public function increase(Request $request, int $productId)
    {  
        $cart = $this->cartService->increaseQuantity(
            $request->user()->id,
            $productId
        );

        return response()->json([
            'success' => true,
            'message' => 'Item quantity increased successfully',
            'data' => $cart
        ]);
    }

    public function decrease(Request $request, int $productId)
    {  
        $cart = $this->cartService->decreaseQuantity(
            $request->user()->id,
            $productId
        );

        return response()->json([
            'success' => true,
            'message' => 'Item quantity decreased successfully',
            'data' => $cart
        ]);
    }

    public function destroy(Request $request, int $productId)
    {
        $cart = $this->cartService->removeItem(
            $request->user()->id,
            $productId
        );

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
            'data' => $cart
        ]);
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->clearCart(
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'data' => $cart
        ]);
    }
}
