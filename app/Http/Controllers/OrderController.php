<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Validator;

class OrderController extends Controller
{

    public function index()
{
    return response()->json([
        'data' => Order::where('user_id', Auth::id())
            ->with('order_item')
            ->latest()
            ->get()
    ],200);
}

    /*
    |--------------------------------------------------------------------------
    | USER: PLACE ORDER
    |--------------------------------------------------------------------------
    | POST /orders
    */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'grandTotal' => 'required|numeric',
            'totalItem' => 'required|integer',
            'totalPrice' => 'required|numeric',
            'total_delivery_charge' => 'required|numeric',
            'items' => 'required|array',
            'payment_mode' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $order = Order::create([
            'user_id'               => Auth::id(),
            'grandTotal'            => $request->grandTotal,
            'totalItem'             => $request->totalItem,
            'totalPrice'            => $request->totalPrice,
            'total_delivery_charge' => $request->total_delivery_charge,
            'payment_mode'          => $request->payment_mode,
            'payment_status'        => $request->payment_status ?? 0,
            'transaction_id'        => $request->transaction_id ?? null,
            'address'               => is_array($request->address)
                                        ? json_encode($request->address)
                                        : $request->address,
            'tax'                   => $request->tax ?? 0,
            'coupon'                => $request->coupon ?? null,
            'discount'              => $request->discount ?? 0,
        ]);

        $items = array_map(function ($item) use ($order) {
            return [
                'order_id'   => $order->id,
                'item_id'    => $item['item_id'],
                'name'       => $item['name'] ?? 'Product',
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'cover'      => $item['cover'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $request->items);

        OrderItem::insert($items);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully'
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: ALL ORDERS (Dashboard)
    |--------------------------------------------------------------------------
    | GET /admin/orders
    */

    public function adminOrders()
{
    return response()->json([
        'status' => 'ADMIN ORDERS ROUTE WORKING',
        'data' => Order::with(['order_item', 'user'])->latest()->get()
    ], 200);
}

    // public function adminOrders(Request $request)
    // {
    //     // $user = $request->user();
    //     $user = Auth::guard('sanctum')->user();

    //     if (!$user || $user->role !== 'admin') {
    //         return response()->json(['message' => 'Forbidden'], 403);
    //     }

    //     $orders = Order::with([
    //         'order_item',
    //         'user:id,name,phone'
    //     ])->latest()->get();

    //     return response()->json([
    //         'data' => $orders
    //     ], 200);
    // }
}
