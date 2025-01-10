<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        $cart[$validated['product_id']] = [
            'quantity' => $validated['quantity']
        ];

        session()->put('cart', $cart);

        return response()->json(['message' => 'Item added to cart']);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);

        return response()->json($cart);
    }
}
