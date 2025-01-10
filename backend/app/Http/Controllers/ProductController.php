<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Get all products with pagination
   public function index(Request $request)
        {
            $products = Product::all(); // Fetch all products without pagination
            return response()->json($products);
        }

    // Get product details by ID
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // Create a new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        // Store the uploaded image
        $imagePath = $request->file('image')->store('product_images', 'public');

        // Create the product
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'image_path' => $imagePath,
        ]);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    // Update product details
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'quantity' => 'integer|min:0',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        // Update the image if a new one is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('product_images', 'public');
            $product->image_path = $imagePath;
        }

        // Update other fields
        $product->update($validated);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the image
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
