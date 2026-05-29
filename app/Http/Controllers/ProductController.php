<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Display a specific product by ID.
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    /**
     * Display a specific product by slug.
     */
    public function showBySlug($slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products',
            'price_m2' => 'required|numeric',
            'dimension' => 'nullable|string',
            'type' => 'nullable|string',
            'finition' => 'nullable|string',
            'thickness' => 'nullable|string',
            'usage' => 'nullable|string',
            'epaisseur' => 'nullable|string',
            'images' => 'nullable|json',
            'popular' => 'nullable|boolean',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:products,slug,' . $id,
            'price_m2' => 'numeric',
            'dimension' => 'nullable|string',
            'type' => 'nullable|string',
            'finition' => 'nullable|string',
            'thickness' => 'nullable|string',
            'usage' => 'nullable|string',
            'epaisseur' => 'nullable|string',
            'images' => 'nullable|json',
            'popular' => 'nullable|boolean',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
