<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'phone' => 'required|min:11|max:11',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $query = User::where(function ($query) use ($request) {
            return $query->where('email', $request->email)->orWhere('phone', $request->phone);
        })->first();

        if($query){
            return response()->json(['success' => 0, 'message' => 'User already exists'], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json(['success' => 1, 'token' => $token], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            "name" => $user->name,
            "email" => $user->email,
            "phone" => $user->phone,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'you are unauthorized user'], 401);
        }

        $user = Auth::user(); // Abhi correct user mil raha hai

        // $user = $request->user();

          // Check if admin login attempt
    if($request->has('is_admin') && $request->is_admin){
        if($user->role !== 'admin'){
            return response()->json(['message' => 'You are not admin'], 403);
        }
    }



        //Delete existing tokens
        $user->tokens()->delete();

            $token = $user->createToken($request->email)->plainTextToken;

    return response()->json([
        'success' => 1,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);

        // $tokenResult = $user->createToken($request->email);
        // $token = $tokenResult->plainTextToken;

        // return response()->json(['success' => 1, 'token' => $token], 200);
    }

    public function logout(Request $request){
        $user = Auth::user();

        //Delete existing tokens for the user
        $user->tokens()->delete();

        return response()->json(['success' => 1], 200);
    }


    // public function sendResetPasswordOtp(Request $request){

    //      $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|string',
    //      ]);

    //     if($validator->fails()){
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }

    //     $user = User::where('email', $request->email)->first();
    //     $otp = rand(1000, 9999);
    //     $id = $user->id;
    //     $verify_user = User::find($id);
    //     $verify_user->reset_password_token = $otp;
    //     $verify_user->save();

    //     //send otp via email

    //     return response()->json([
    //         'success' => 1,
    //         'message' => 'An OTP has been sent to your email address. Please verify your email to proceed.'
    //     ],200);
    // }




  public function sendResetPasswordOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['success' => 0, 'message' => 'Email not registered.']);
    }

    $otp = rand(1000, 9999);

    $user->reset_password_token = $otp;
    $user->save();

    // yahan email bhejna hota hai

    return response()->json([
        'success' => 1,
        'message' => 'OTP sent successfully.'
    ]);
}

    // public function verifyResetPasswordOtp(Request $request){

    //        $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|string',
    //         'otp' => 'required',
    //      ]);

    //     if($validator->fails()){
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }


    //     $user = User::where([['email' => $request->email], ['reset_password_token' => $request->otp]])->first();

    //     if(!isset($user)){
    //         return response()->json(['success' => 0, 'message' => 'Wrong OTP. Please try again.']);
    //     }

    //     $email_Verified = User::find($user->id);
    //     $email_Verified->email_verified_at = Carbon::now();
    //     $email_Verified->save();

    //     return response()->json(['success' => 1], 200);
    // }



public function verifyResetPasswordOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|string',
        'otp' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where([
        ['email', $request->email],
        ['reset_password_token', $request->otp]
    ])->first();

    if (!$user) {
        return response()->json(['success' => 0, 'message' => 'Invalid OTP.']);
    }

    // OTP correct — clear it
    $user->reset_password_token = null;
    $user->save();

    return response()->json(['success' => 1, 'message' => 'OTP verified.']);
}



    //  public function resetPassword(Request $request){

    //      $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|string',
    //         'otp' => 'required',
    //         'password' => 'required|string'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }
    //     $password = bcrypt($request->password);

    //     $user = User::where([['email' => $request->email], ['reset_password_token' => $request->otp]])->whereNotNull('email_verified_at')->update([
    //         'password' => $password,
    //         'reset_password_token' => null,
    //         'email_verified_at' => null
    //     ]);

    //     return response()->json(['success' => 1, 'message' => 'Reset password succesful.']);


    // }

    // if(!isset($user)){
        //     return response()->json(['success' => 0, 'message' => 'Unauthorized request.']);
        // }


        public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|string',
        'password' => 'required|string|min:6'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // OTP yahan required nahi hoti
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['success' => 0, 'message' => 'Unauthorized request.']);
    }

    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json(['success' => 1, 'message' => 'Password reset successful.']);
}

}
