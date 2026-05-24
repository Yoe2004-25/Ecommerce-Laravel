<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem; 
use App\Models\Category; 
class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 
        'stock_quantity', 'image_url', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function decreaseStock($quantity)
    {
        $this->stock_quantity -= $quantity;
        $this->save();
    }

    public function increaseStock($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }
}