<?php

namespace App\Http\Controllers;

use App\Http\Requests\Base\LoginRequest;
use App\Http\Requests\Base\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
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
            return response()->json(['status' => 200, 'message' => 'Welcome', 'Token' => $token, 'user' => $user]);
        } else {
            return response()->json(['status' => 203, 'message' => 'This user not exists']);
        }
    }


    function register(RegisterRequest $request)
    {

        $userid = DB::table('users')->insertGetId([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'companycode' => $request->companycode,
            'companyname' => $request->companyname,
            'role' => $request->role,
            'created_at' => Carbon::now(),
            'password' => Hash::make($request->password),
        ]);


        if ($userid) {
            $user = User::find($userid);
            $token = $user->createToken($user->name)->plainTextToken;
            return response()->json(['status' => 200, 'messages' => 'User Created', 'token' => $token, 'user' => $user]);
        } else {
            return response()->json(['status' => 203, 'message' => 'User Create Faild']);
        }
    }

    function categorielist()
    {
        $categorys = DB::table('categories')->get();
        return response()->json(['Status' => 200, 'categories' => $categorys], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 200, 'message' => 'Your Logout Success'], 200);
    }
}
