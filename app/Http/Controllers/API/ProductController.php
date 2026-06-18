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
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $cacheKey = 'products_page_' . $page;

        $products = Cache::tags(['products'])->remember($cacheKey, 3600, function () {
            return Product::with('category')->paginate(15);
        });

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url|max:2048'
        ]);

        $slug = $this->generateUniqueSlug($validated['name']);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'category_id' => $validated['category_id'],
            'image_url' => $validated['image_url'] ?? null
        ]);

        Cache::tags(['products'])->flush();

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'image_url' => 'nullable|url|max:2048'
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $product->update($validated);

        Cache::tags(['products'])->flush();

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        Cache::tags(['products'])->flush();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

   
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);

        $query = Product::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if (!$query->exists()) {
            return $slug;
        }

        $count = Product::where('slug', 'like', "{$slug}-%")->count();
        return "{$slug}-" . ($count + 1);
    }
}