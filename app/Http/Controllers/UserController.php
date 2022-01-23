<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all user not admin
        $users = User::where('role', 'user')->get();
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
        $user = User::where('id', $id)->where('role', 'user')->first();
        if (!$user || $user->status == 'inactive' || $user->status == 'blocked') {
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
        $user = User::where('id', $id)->where('role', 'user')->first();

        if (!$user || $user->status == 'inactive' || $user->status == 'blocked') {
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
        $user = User::where('id', $id)->where('role', 'user')->first();

        if (!$user || $user->status == 'inactive' || $user->status == 'blocked') {
            return response()->json([
                'message' => 'User not found',
                'status' => 404,
            ], Response::HTTP_NOT_FOUND);
        }

        // change current user status to blocked
        $user->status = 'blocked';
        $user->save();

        return response()->json([
            'message' => 'User blocked',
            'data' => $user,
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // get all active user
    public function active()
    {
        $users = User::where('status', 'active')->where('role', 'user')->get();

        return response([
            'data' => $users,
            'message' => 'Active user',
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // get all inactive user
    public function inactive()
    {
        $users = User::where('status', 'inactive')->where('role', 'user')->get();

        return response([
            'data' => $users,
            'message' => 'Inactive user',
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // get all blocked user
    public function blocked()
    {
        $users = User::where('status', 'blocked')->where('role', 'user')->get();

        return response([
            'data' => $users,
            'message' => 'Blocked user',
            'status' => 200,
        ], Response::HTTP_OK);
    }

    // search user by name or phone
    public function search(Request $request)
    {
        $users = User::where('name', 'like', '%' . $request->name . '%')
            ->orWhere('phone', 'like', '%' . $request->phone . '%')
            ->where('role', 'user')
            ->get();

        return response([
            'data' => $users,
            'message' => 'Search user',
            'status' => 200,
        ], Response::HTTP_OK);
    }
}
