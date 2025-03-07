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
        dd('test-index');
        $products = Product::all();
        // dd($request);
        // dd($products);
        // return ($products);  // так нельзя!
        return response()->json($products);
        // return view('products.index', compact('products'));

        // if ($request->is('admin/*')) {
        //     return view('admin.products.index', compact('products')); // Админка
        // } else {
        //     return view('products.index', compact('products')); // Главная страница
        // }


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View  // для  api не надо
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse 
    {
        dd('test-store');
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
            'image' => 'nullable|image', // 
            'type' => 'required|in:pizza,drink',
        ]);    

        // dd('test-update');
 
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
        dd('test-destroy');
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Только для администраторов'
             ]); 
        }
        $product->delete();

        return response()->json([
           'message' => 'Товар успешно удален'
        ]); 
    }
}