<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function getCustomerID($phone)
    {
        // validate phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) != 10) {
            return response()->json([
                'message' => 'Invalid phone number.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $user = User::create([
                'phone' => $phone,
            ]);
        }

        return response()->json([
            'id' => $user->id,
        ], Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|confirmed',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users|digits:10',
        ]);

        // check user exist with phone
        $user = User::where('phone', $request->phone)->first();
        if ($user && $user->status == 'inactive') {
            $user->update([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'active',
            ]);
        } else if ($user && $user->status == 'active') {
            return response()->json([
                'message' => 'User already exist',
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        // generate token
        $token = $user->createToken('Laravel Password Grant Client')->plainTextToken;

        return response([
            'token' => $token,
            'data' => $user,
            'message' => 'User Created',
            'status' => 201
        ], Response::HTTP_CREATED);
    }

    // logout
    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response([
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }

    // login
    public function login(Request $request)
    {
        $field = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $field['username'])->first();
        if (!$user || !Hash::check($field['password'], $user->password)) {
            return response()->json([
                'message' => 'Username or password is incorrect',
                'status' => 401,
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status == 'inactive') {
            return response()->json([
                'message' => 'Unregistered users cannot login',
                'status' => 401,
            ], Response::HTTP_UNAUTHORIZED);
        } else if ($user->status == 'blocked') {
            return response()->json([
                'message' => 'User is blocked',
                'status' => 401,
            ], Response::HTTP_UNAUTHORIZED);
        }

        // generate token
        $token = $user->createToken('Laravel Password Grant Client')->plainTextToken;

        return response([
            'token' => $token,
            'data' => $user,
            'message' => 'Successfully logged in'
        ], Response::HTTP_OK);
    }

    // get user
    public function getUser()
    {
        $user = Auth::user();

        return response([
            'user' => $user,
            'message' => 'Successfully logged in'
        ], Response::HTTP_OK);
    }
}
