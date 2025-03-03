<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; //Для Storage

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $products = Product::all();
        return view('products.index', compact('products'));

        // if ($request->is('admin/*')) {
        //     return view('admin.products.index', compact('products')); // Админка
        // } else {
        //     return view('products.index', compact('products')); // Главная страница
        // }


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'type' => 'required|in:pizza,drink',
        ]);

        if ($request->hasFile('image')) {
           $path = $request->file('image')->store('public/products'); // Сохраняем в storage/app/public/products
           $validatedData['image'] = str_replace('public/', '', $path); // Убираем 'public/' из пути для корректного отображения
        }

        Product::create($validatedData);
        return redirect('/admin/products')->with('success', 'Товар успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
       //Этот метод, возможно, не нужен
        // return view('admin.products.show', compact('product'));
        // dd('show');
        // dd($product);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'type' => 'required|in:pizza,drink',
        ]);

        if ($request->hasFile('image')) {
            // Удаляем старое изображение, если оно есть
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            $path = $request->file('image')->store('public/products');
            $validatedData['image'] = str_replace('public/', '', $path);
        }

        $product->update($validatedData); // Обновляем товар

        return redirect('/admin/products')->with('success', 'Товар успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        //Удаляем изображение
        if($product->image){
           Storage::delete('public/'.$product->image); //Используем Storage
        }
        $product->delete();
        return redirect('/admin/products')->with('success', 'Товар успешно удален');
    }
}