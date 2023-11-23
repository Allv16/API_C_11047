<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registationData = $request->all();
        $validate = Validator::make($registationData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'no_telp' => 'required|numeric|digits_between:11,13',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $registationData['status'] = 0;
        $registationData['password'] = bcrypt($request->password);

        //upload image
        $image_path = $request->file('image')->store('image', 'public');
        $registationData['image'] = $image_path;

        //create ID logic
        $year = date('y');
        $month = date('m');
        $query = "$year.$month%";
        //check last id in this month. if not found return null
        $lastId = DB::table('users')->where('id', 'like', $query)->orderBy('id', 'desc')->first()->id ?? null;
        if ($lastId) {
            $parts = explode('.', $lastId);
            $idToIncement = end($parts);
            $index = intval($idToIncement) + 1;
            $registationData['id'] = "$year.$month.$index";
        } else {
            $registationData['id'] = "$year.$month.1";
        }

        $user = User::create($registationData);

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }
    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $token = $user->createToken('Authentiucation Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ], 200);
    }
}
