<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        // Create regular user
        User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and publications'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create products
        $products = [
            [
                'name' => 'Smartphone X',
                'slug' => 'smartphone-x',
                'description' => 'Latest smartphone with amazing features',
                'price' => 699.99,
                'stock_quantity' => 50,
                'category_id' => 1,
            ],
            [
                'name' => 'Laptop Pro',
                'slug' => 'laptop-pro',
                'description' => 'High-performance laptop for professionals',
                'price' => 1299.99,
                'stock_quantity' => 30,
                'category_id' => 1,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'slug' => 'cotton-tshirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 19.99,
                'stock_quantity' => 100,
                'category_id' => 2,
            ],
            [
                'name' => 'Programming Book',
                'slug' => 'programming-book',
                'description' => 'Learn programming fundamentals',
                'price' => 39.99,
                'stock_quantity' => 75,
                'category_id' => 3,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}