<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Gift;
use Config;

class GiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $server = config('app.server');
        $gifts = Gift::where('status', true);

        $query = $request->get('query');

        if(isset($query)){
            $gifts = $gifts->where(function ($qry) use ($query) {
                return $qry->where('name', 'like', '%' . $query . '%')->orWhere('description', 'like', '%' . $query . '%');

            });
        }
        $gifts = $gifts->get();
        return response()->json([
            'data' => $gifts,
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
        $input = $request->all();
        $validator = Validator::make($input, [
           'name' => 'required',
           'price' => 'required',
           'deal_price' => 'required',
           'stock' => 'required',
           'cover' => 'required | image | max:2048',
           'ad_image' => 'nullable | image | max:2048',
           'status' => 'required',
           'description' => 'required',
           'prescription' => 'nullable',
           'mfg' => 'required',
           'packSize' => 'nullable',
           'type' => 'nullable',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        if($request->hasFile('cover')){
            $filenameWithExt = $request->file('cover')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_'.time() . '.' . $extension;
            $path = $request->file('cover')->storeAs('gifts', $fileNameToStore, 'public');

            $input['cover'] = '/gifts/'. $fileNameToStore;
        }

        // 🛠️ Handle Ad Image Upload (Eye button popup image)
        if($request->hasFile('ad_image')){
            $adFilenameWithExt = $request->file('ad_image')->getClientOriginalName();
            $adFilename = pathinfo($adFilenameWithExt, PATHINFO_FILENAME);
            $adExtension = $request->file('ad_image')->getClientOriginalExtension();
            $adFileNameToStore = $adFilename . '_ad_' . time() . '.' . $adExtension;
            $adPath = $request->file('ad_image')->storeAs('gifts', $adFileNameToStore, 'public');

            $input['ad_image'] = '/gifts/' . $adFileNameToStore;
        }

        $gift = Gift::create($input);

        return response()->json(['success' => 1, 'data' => $gift]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $server = config('app.server');
        $gift = Gift::find($id);
        return response()->json([
            'data' => $gift,
            'server_base_url' => $server
        ]);
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
        $input = $request->all();
        $validator = Validator::make($input, [
            'cover' => 'image | max:2048',
            'ad_image' => 'nullable | image | max:2048'
        ]);

        if($validator->fails()){
            //Get the validation errors
            $errors = $validator->errors();

            // Append your custom error message
            $errors->add('cover', 'Maximum file size to upload is 2MB (2048 KB');
            $errors->add('ad_image', 'Maximum file size to upload is 2MB (2048 KB)');

            return response()->json(['error' => $errors], 422);
        }

        if($request->hasFile('cover')){
            $filenameWithExt = $request->file('cover')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' .time() . '.' . $extension;
            $path = $request->file('cover')->storeAs('gifts', $fileNameToStore, 'public');
            $input['cover'] = '/gifts/' . $fileNameToStore;
        }


        // 🛠️ Update Ad Image
        if($request->hasFile('ad_image')){
            $adFilenameWithExt = $request->file('ad_image')->getClientOriginalName();
            $adFilename = pathinfo($adFilenameWithExt, PATHINFO_FILENAME);
            $adExtension = $request->file('ad_image')->getClientOriginalExtension();
            $adFileNameToStore = $adFilename . '_ad_' . time() . '.' . $adExtension;
            $adPath = $request->file('ad_image')->storeAs('gifts', $adFileNameToStore, 'public');
            $input['ad_image'] = '/gifts/' . $adFileNameToStore;
        }

       $gift = Gift::find($id);
        if ($gift) {
            $gift->update($input);
            return response()->json(['success' => 1, 'data' => $gift]);
        }

        return response()->json(['success' => 0, 'message' => 'Product not found'], 404);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gift = Gift::find($id);

        if($gift && $gift->delete()){
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0]);
    }

    public function bulkInsert(Request $request){
        $input = $request->all();

        $result = Gift::insert($input);

        return response()->json(['success' => 1, 'data' => $result]);
    }
}
