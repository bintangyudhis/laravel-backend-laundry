<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function readAll(){

        $users = User::all();

        return response()->json([ // return JSON response
            'data' => $users // data: $users
        ], 200); // 200 OK

    }

    function register(Request $request){

        $this->validate($request, [
            'username' => 'required|min:4|unique:users|max:255', // unique di table users
            'email' => 'required|email|max:255|unique:users',// unique di table users
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'username' => $request->username, //data dari request
            'email' => $request->email,
            'password' => Hash::make($request->password), // hashing password
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User registered successfully'
        ], 201); // 201 Created
    }

    function login(Request $request){

        // kalau gagal login
        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401); // 401 Unauthorized
        }

        $user = User::where('email', $request->email)->firstOrFail(); // cari user berdasarkan email

        $token = $user->createToken('auth_token')->plainTextToken; // create token

        return response()->json([
            'data' => $user,
            'token' => $token,
            'message' => 'User login successfully'
        ], 200); // 200 OK
    }

}
