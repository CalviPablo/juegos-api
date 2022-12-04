<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Product::with('category')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request) {
        $request->validate([
            'name' => 'required|unique:products|max:255',
            'price' => 'required',
            'description' => 'required',
            'image' => 'required',
            'category_id' => 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->image = $request->image;
        $product->category_id = $request->category_id;

        $product->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product) {
        return Product::with('category')->findOrFail($product->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product) {
        $product = Product::findOrFail($product->id);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->image = $request->image;
        $product->category_id = $request->category_id;

        $product->save();

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) {
        $product->delete();

        return response()->json(null, 204);
    }

    public function showProductsByCategoryName($category) {
        $products = DB::select(DB::raw("SELECT 
        p.id,
        p.name,
        p.price,
        p.description
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id
        WHERE c.name = '$category'
        ORDER BY p.price DESC
        LIMIT 2"));
        return $products;
    }

    public function showLatestProducts($category) {
        $products = DB::select(DB::raw("SELECT 
        p.id,
        p.name,
        p.price,
        p.description,
        p.image
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id
        WHERE c.name = '$category'
        ORDER BY p.price DESC
        LIMIT 2"));
        return $products;
    }
}
