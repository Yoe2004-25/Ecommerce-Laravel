<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'order_id', 'product_id', 'quantity', 'price'


        
        'product_id' => $this->product_id , 
        'quantity'=> $this->quantity , 
        'price'=>$this->price,
        return parent::toArray($request);
    }
}