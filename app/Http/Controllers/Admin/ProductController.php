<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Js;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::all();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse 
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'type' => 'required|in:pizza,drink',
        ]); 

        $product = Product::create($validatedData);
    
        return response()->json([
            'message' => 'Товар успешно создан!',
            'product' => $product,
        ], 201); // статус 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric',
            // 'image' => 'nullable|image', // 
            'type' => 'required|in:pizza,drink',
        ]);    

        $product->update($validatedData);
    
        return response()->json([
            'message' => 'Товар успешно обновлен!',
            'product' => $product,
        ]); //  200 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse 
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'For administrators only!'
             ]); 
        }
        $product->delete();

        return response()->json([
           'message' => 'The product has been successfully removed.'
        ]); 
    }
}