<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        
    }

    public function index(Request $request)
    {
        // Simple cache implementation
        $cacheKey = 'products_page_' . ($request->get('page', 1));
        
        $products = Cache::remember($cacheKey, 3600, function () {
            return Product::with('category')->paginate(15);
        });

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id,
            'image_url' => $request->image_url
        ]);

        // Clear cache
        Cache::flush();

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'category_id' => 'exists:categories,id'
        ]);

        if ($request->has('name')) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $product->update($request->only([
            'name', 'slug', 'description', 'price', 
            'stock_quantity', 'category_id', 'image_url'
        ]));

        Cache::flush();

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        Cache::flush();
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}