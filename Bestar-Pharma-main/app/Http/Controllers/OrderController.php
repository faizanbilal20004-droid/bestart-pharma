<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $user_id = Auth::user() -> id;
    //     $orders = Order::where('user_id', $user_id)->with('order_item')->get();
    //     $server = config('app.server');

    //     return response()->json(['data' => $orders, 'server_base_url' => $server]);
    // }

    public function index(Request $request)
{
    $user = $request->user(); // ✅ SAFE

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $orders = Order::where('user_id', $user->id)
        ->with('order_item')
        ->get();

    $server = config('app.server');

    return response()->json([
        'data' => $orders,
        'server_base_url' => $server
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is logged in
    if (!Auth::check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

        $user_id = Auth::user()->id;

        $input = $request->all();

        // 1. Validation
    $validator = Validator::make($input, [
        'grandTotal' => 'required|numeric',
        'totalItem' => 'required|integer',
        'totalPrice' => 'required|numeric',
        'total_delivery_charge' => 'required|numeric',
        'items' => 'required|array',
        'payment_mode' => 'required',
        'address' => 'required'
    ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

       // 2. Prepare Data with Safety Checks (Null Coalescing Operator ??)
    $data = [
        "user_id"               => $user_id,
        "grandTotal"            => $input['grandTotal'],
        "totalItem"             => $input['totalItem'],
        "totalPrice"            => $input['totalPrice'],
        "total_delivery_charge" => $input['total_delivery_charge'],
        "payment_mode"          => $input['payment_mode'],
        "payment_status"        => $input['payment_status'] ?? 0, // Default 0 agar na aaye
        "transaction_id"        => $input['transaction_id'] ?? null,
        "address"               => is_array($input['address']) ? json_encode($input['address']) : $input['address'],
        "tax"                   => $input['tax'] ?? 0, // Default 0 agar na aaye
    ];

        if($request->has('coupon')){
            $data['coupon'] = $input['coupon'];
            $data['discount'] = $input['discount'] ?? 0;
        }

        // 3. Create Order
    try {
        $order = Order::create($data);
        $order_id = $order->id;
        $items = $input['items'];

        $newArray = array_map(function ($item) use ($order_id) {
            return [
                'order_id'   => $order_id,
                'item_id'    => $item['item_id'], // Make sure item_id exists in frontend request
                'name'       => isset($item['name']) ? $item['name'] : 'Product Name',
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'cover'      => isset($item['cover']) ? $item['cover'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $items);

        OrderItem::insert($newArray);

        return response()->json(['success' => 1, 'message' => 'Order placed successfully'], 200);

    } catch (\Exception $e) {
        // Return exact error message for debugging
        return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
    }

        // $order = Order::create($data);

        // $order_id = $order->id;
        // $items = $input['items'];

        // $newArray = array_map(function ($item) use ($order_id) {
        //     return [
        //         'order_id' => $order_id,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'item_id' => $item['item_id'],
        //         // 'name' => $item['name'],
        //         // 'description' => $item['description'],
        //         'price' => $item['price'],
        //         'quantity' => $item['quantity'],
        //         // 'cover' => $item['cover']
        //     ];
        // }, $items);

        // $orderItems = OrderItem::insert($newArray);

        // return response()->json(['success' => 1, 'message' => 'Order placed successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
