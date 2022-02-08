<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all user
        $users = User::all();
        return response()->json([
            'data' => $users,
            'status' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // find user by id and user not admin
        $user = User::find($id);
        if (!$user || $user->status == 'inactive') {
            return response()->json([
                'message' => 'User not found',
                'status' => 404,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'data' => $user,
            'status' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::find($id);

        if (!$user || $user->status == 'inactive') {
            return response()->json([
                'message' => 'User not found',
                'status' => 404,
            ], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User updated',
            'data' => $user,
            'status' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::find($id);

        if (!$user || $user->status == 'inactive' || $user->status == 'blocked') {
            return response()->json([
                'message' => 'User not found',
                'status' => 404,
            ], Response::HTTP_NOT_FOUND);
        }

        // change current user status to blocked
        // $user->status = 'blocked';
        // $user->save();
        $user->delete();

        return response()->json([
            'message' => 'User deleted',
            'data' => $user,
            'status' => 200,
        ], Response::HTTP_OK);
    }



    public function update_password(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        $user = User::find($id);
        if ((Hash::check($request->old_password, $user->password) && $user)) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            return response()->json([
                'user' => $user,
                'message' => 'Update password success',
                'status' => 200
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Password is incorrect',
                'status' => 401
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    // search user by name or phone
    public function search(Request $request)
    {
        // $currentUser = auth()->user();
        // if ($currentUser->role == 'admin') {
        if ($request->name != NULL && $request->phone != NULL) {
            $users = User::where('name', 'like', '%' . $request->name . '%')
                ->where('phone', 'like', '%' . $request->phone . '%')
                ->get();
        } else if ($request->name == NULL) {
            $users = User::where('phone', 'like', '%' . $request->phone . '%')->get();
        } else if ($request->phone == NULL) {
            $users = User::where('name', 'like', '%' . $request->name . '%')->get();
        }
        // $users = User::where('name', 'like', '%' . $request->name . '%')
        //     ->where('phone', 'like', '%' . $request->phone . '%')
        //     ->get();

        return response([
            'data' => $users,
            'message' => 'Search user success',
            'status' => 200
        ], Response::HTTP_OK);
    }

    public function getCustomerID($phone)
    {
        // validate phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) != 10) {
            return response()->json([
                'message' => 'Invalid phone number.',
                'status' => 400
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $user = User::create([
                'phone' => $phone,
                'role' => 'customer',
                'status' => 'active'
            ]);
            return response()->json([
                'data' => $user->id,
                'status' => 200
            ], Response::HTTP_OK);
        }
        else {
            return response()->json([
                'data' => $user->id,
                'status' => 200
            ], Response::HTTP_OK);
        }

    }
}
