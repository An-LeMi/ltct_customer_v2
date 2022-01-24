<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class StateController extends Controller
{
    //
    public function blockUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'status' => 'blocked',
            ]);
            return response()->json([
                'message' => 'User blocked',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function activeUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'status' => 'active',
            ]);
            return response()->json([
                'message' => 'User active',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    // get all active user
    public function active()
    {
        $users = User::where('status', 'active')->get();

        return response([
            'data' => $users,
            'message' => 'Active user',
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // get all inactive user
    public function inactive()
    {
        $users = User::where('status', 'inactive')->get();

        return response([
            'data' => $users,
            'message' => 'Inactive user',
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // get all blocked user
    public function blocked()
    {
        $users = User::where('status', 'blocked')->get();

        return response([
            'data' => $users,
            'message' => 'Blocked user',
            'status' => 200,
        ], Response::HTTP_OK);
    }
}
