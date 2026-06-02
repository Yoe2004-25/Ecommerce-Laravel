<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'price_formatted' => '$' . number_format($this->price, 2),
            'subtotal' => $this->quantity * $this->price,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
