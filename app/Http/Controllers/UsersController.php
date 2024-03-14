<?php

namespace App\Http\Controllers;

use App\Http\Requests\Base\LoginRequest;
use App\Http\Requests\Base\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;


class UsersController extends Controller
{

    public function login(LoginRequest $request)
    {

        $user = User::where('email', '=', $request->email)->first();
        if ($user != null && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $token = $user->createToken($user->name)->plainTextToken;
            return response()->json(['status' => 200, 'message' => 'خوش آمدید', 'Token' => $token, 'user' => $user]);
        } else {
            return response()->json(['status' => 203, 'message' => 'چنین کاربری وجود ندارد']);
        }
    }


    function register(RegisterRequest $request)
    {
        $userid = DB::table('users')->insertGetId([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);


        if ($userid) {
            $user = User::find($userid);
            $token = $user->createToken($user->name)->plainTextToken;
            return response()->json(['status' => 200, 'messages' => 'کاربر با موفقیت ثبت شد', 'token' => $token, 'user' => $user]);
        } else {
            return response()->json(['status' => 203, 'message' => 'مشکلی در ثبت کاربر بوجود آمده است']);
        }
    }
}
