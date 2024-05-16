<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\OrdersItems;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Orders;

class OrdersController extends Controller
{
    //
    public function index()
    {
        $orders = Orders::orderBy("created_at", "desc")->with('user')->paginate(10);
        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $product = Products::where('id', $item->product_id)->first(['name']);
                    $item->product_name = $product ? $product->name : 'Unknown';
                }

            }
            return response()->json($orders);
        }
        return response()->json(['status' => false, 'message' => 'no orders']);
    }
    public function show($id)
    {
        $order = Orders::find($id);
        if ($order) {
            return response()->json([
                'status' => true,
                'order' => $order,
                'message' => 'order found'
            ]);
        }
        return response()->json(['status' => false, 'message' => 'order not found'], 404);
    }
    public function store(Request $request)
    {
        try {
            $location = Locations::where('user_id', auth()->user()->id)->first();
            $validate = \Validator::make($request->all(), [
                'order_items' => 'required|array',
                'order_items.*.product_id' => 'required|exists:products,id',
                'order_items.*.quantity' => 'required|integer|min:1',
                'order_items.*.price' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'date' => 'required|date',
                'quantity' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(['status' => false, 'errors' => $validate->errors()], 400);
            }
            $order = Orders::create(array_merge($validate->validate(), [
                'user_id' => auth()->user()->id,
                'location_id' => $location->id
            ]));
            foreach ($validate->validated()['order_items'] as $item) {
                $items = new OrdersItems();
                $items->order_id = $order->id;
                $items->price = $item['price'];
                $items->product_id = $item['product_id'];
                $items->quantity = $item['quantity'];
                $items->save();
                $product = Products::where('id', $item['product_id'])->first();
                $product->quantity -= $item['quantity'];
                $product->save();
            }
            return response()->json(['status' => true, 'order' => $order, 'message' => 'order created successfully'], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function get_order_items($id)
    {
        $order_items = OrdersItems::where('order_id', $id)->get();

        if (!$order_items->isEmpty()) {
            foreach ($order_items as $item) {
                $product = Products::where('id', $item->product_id)->first(['name']);
                $item->product_name = $product ? $product->name : 'Unknown';
            }
            return response()->json(['status' => true, 'products' => $order_items, 'message' => 'Order items found'], 200);
        }

        return response()->json(['status' => false, 'message' => 'No items found'], 404);

    }


    public function get_user_orders($id)
    {
        $orders = Orders::where('user_id', $id)->first()::with('items', function ($query) {
            $query->orderBy('created_at', 'desc');
        })->get();
        if ($orders) {
            foreach ($orders->items as $order) {
                $product = Products::where('id', $order->product_id)->first(['name']);
                $order->product_name = $product ? $product->name : 'Unknown';
            }
            return response()->json(['status' => true, 'products' => $orders, 'message' => 'orders found'], 200);
        }
        return response()->json(['status' => false, 'message' => 'No orders found'], 404);
    }
    public function change_order_status(Request $request, $id)
    {
        try {
            $order = Orders::find($id);
            $order->status = $request->status;
            return response()->json(['status' => true, 'message' => 'order status updated'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'api exception'], 500);
        }
    }
}
