<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        if (count($users) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $users
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            return response([
                'message' => 'Retrieve User Success',
                'data' => $user
            ], 200);
        }
        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'no_telp' => 'required|numeric|digits_between:11,13',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $user->name = $updateData['name'];
        $user->email = $updateData['email'];
        $user->password = bcrypt($updateData['password']);
        $user->no_telp = $updateData['no_telp'];
        $user->image = $updateData['image'];
        if ($user->save()) {
            return response([
                'message' => 'Update User Success',
                'data' => $user
            ], 200);
        } else {
            return response([
                'message' => 'Update User Failed',
                'data' => null
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        }

        if ($user->delete()) {
            return response([
                'message' => 'Delete User Success',
                'data' => $user
            ], 200);
        } else {
            return response([
                'message' => 'Delete User Failed',
                'data' => null
            ], 400);
        }
    }
}
