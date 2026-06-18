<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->paginate(15);
        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address']
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                $product->decreaseStock($item['quantity']);
                $total += $product->price * $item['quantity'];
            }

            $order->total_amount = $total;
            $order->save();

            DB::commit();

            Cache::forget('user_orders_' . $request->user()->id);

            return new OrderResource($order->load('items.product'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return new OrderResource($order->load('items.product'));
    }

    public function updateStatus(Request $request, Order $order)
    {
       
        if ($request->user()->id !== $order->user_id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to update this order'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        if ($validated['status'] === 'cancelled' && $order->status !== 'completed') {
            DB::beginTransaction();
            try {
                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increaseStock($item->quantity);
                    }
                }
                $order->status = 'cancelled';
                $order->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['message' => 'Failed to cancel order'], 400);
            }
        } else {
            $order->update(['status' => $validated['status']]);
        }

        Cache::forget('user_orders_' . $order->user_id);

        return new OrderResource($order);
    }
}