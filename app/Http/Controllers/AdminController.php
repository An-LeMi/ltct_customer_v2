<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // show all admin
        $users = User::where('role', 'admin')->get();
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
        $field = $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|confirmed',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users|digits:10',
        ]);

        // check user is admin and exist with phone
        $user = User::where('phone', $request->phone)->where('role', 'admin')->first();

        if ($user) {
            return response()->json([
                'message' => 'Admin already exist',
                'status' => 400
            ], Response::HTTP_BAD_REQUEST);
        }

        // create new admin
        $field['role'] = 'admin';
        $field['status'] = 'active';
        $user = User::create($field);

        return response()->json([
            'data' => $user,
            'status' => 200,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::where('id', $id)->where('role', 'admin')->first();
        if (!$user) {
            return response()->json([
                'message' => 'Admin not found',
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
        $user = User::where('id', $id)->where('role', 'admin')->first();

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
        $user = User::where('id', $id)->where('role', 'admin')->first();

        if (!$user) {
            return response()->json([
                'message' => 'Admin not found',
                'status' => 404,
            ], Response::HTTP_NOT_FOUND);
        }

        $user->delete();
    }
}
