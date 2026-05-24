<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::with('products')->get();
        });

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        Cache::forget('all_categories');

        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->load('products'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        if ($request->has('name')) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $category->update($request->only(['name', 'slug', 'description']));
        
        Cache::forget('all_categories');

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Cache::forget('all_categories');
        
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}