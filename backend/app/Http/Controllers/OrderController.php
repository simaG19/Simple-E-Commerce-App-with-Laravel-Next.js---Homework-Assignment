<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $validated['product_ids'])->get();
        $totalPrice = $products->sum('price');

        $order = Order::create([
            'user_id' => auth()->id(),
            'product_ids' => json_encode($validated['product_ids']),
            'total_price' => $totalPrice,
        ]);

        return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
    }
}
