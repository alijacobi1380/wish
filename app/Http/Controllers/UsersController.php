<?php

namespace App\Http\Controllers;

use App\Http\Requests\Base\LoginRequest;
use App\Http\Requests\Base\RegisterRequest;
use App\Http\Requests\changepassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

use Illuminate\Validation\ValidationException;
use PharIo\Manifest\Url;

use function App\Providers\onesr;

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

    function accepctemail($code)
    {
        $key = DB::table('resetpassword')->where('key', $code)->first();
        if ($key) {
            $u = DB::table('users')->where('email', '=', $key->email)->update(
                [
                    'email_verified_at' => Carbon::now(),
                ]
            );
            if ($u) {
                DB::table('resetpassword')->where('key', '=', $code)->delete();
                return 'Your Email Verified Successfully . Now Please Refresh Dashboard Page .';
            }
        } else {
            return 'Code Is Not Valid';
        }
    }

    function verifyemail()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user) {
            $key = md5(rand(1, 1000));
            $to_name = $user->name;
            $to_email = $user->email;
            $m = Mail::send(['html' => 'verify'], ['user' => $user, 'key' => $key], function ($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                    ->subject("WishTube");
                $message->from("support@alijacobi.ir", "WishTube");
            });
            if ($m) {
                DB::table('resetpassword')->insert([
                    'email' => $user->email,
                    'key' => $key,
                ]);
                return response()->json(['Status' => 200, 'Message' => 'Verify Link Sended To Your Email']);
            }
        } else {
            return response()->json(['Status' => 200, 'Message' => 'This Email Not Exists']);
        }
    }

    function changepassword(changepassword $request)
    {
        $key = DB::table('resetpassword')->where('key', $request->code)->first();
        if ($key) {
            $u = DB::table('users')->where('email', '=', $key->email)->update(
                [
                    'password' => Hash::make($request->password),
                ]
            );
            if ($u) {
                return response()->json(['Status' => 200, 'Message' => 'Your Password Was Changed']);
            }
        } else {
            return response()->json(['Status' => 500, throw ValidationException::withMessages(['Code' => 'Code Is Not Valid'])]);
        }
    }

    function forgetpassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $key = rand(10000000, 99999999);
            $to_name = $user->name;
            $to_email = $user->email;
            $m = Mail::send('email', ['user' => $user, 'key' => $key], function ($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                    ->subject("WishTube");
                $message->from("support@alijacobi.ir", "WishTube");
            });
            if ($m) {
                DB::table('resetpassword')->insert([
                    'email' => $request->email,
                    'key' => $key,
                ]);
                return response()->json(['Status' => 200, 'Message' => 'Code Sended To Your Email']);
            }
        } else {
            return response()->json(['Status' => 200, 'Message' => 'This Email Not Exists']);
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

    function editprofile(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        if ($request->hasfile('profilepicture')) {
            $file = $request->file('profilepicture');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/usersimg/', $name);
            $data = asset('files/usersimg') . '/' . $name;
            $requestData["profilepicture"] = $data;
        }
        
        // return $request->profilepicture;

        if (Auth::user()->role == 'Company') {
            $user = DB::table('users')->where('id', '=', Auth::user()->id)->update($requestData);
        } else {
            $user = DB::table('users')->where('id', '=', Auth::user()->id)->update($requestData);
        }


        if ($user) {
            return response()->json(['status' => 200, 'messages' => 'Profile Edited']);
        } else {
            return response()->json(['status' => 203, 'message' => 'Profile Edited Faild']);
        }
    }

    function productlist()
    {
        $products = DB::table('products')->get();
        $products->map(function ($item) {
            $item->Pics = unserialize($item->Pics);
        });
        return response()->json(['Status' => 200, 'products' => $products], 200);
    }

    function servicelist()
    {
        $services = DB::table('services')->get();
        $services->map(function ($item) {
            $item->Pics = unserialize($item->Pics);
        });
        return response()->json(['Status' => 200, 'Services' => $services], 200);
    }

    function categorielist()
    {
        $categorys = DB::table('categories')->get();
        return response()->json(['Status' => 200, 'categories' => $categorys], 200);
    }

    function wishlist()
    {
        $wishs = DB::table('wishs')->get();
        $wishs->map(function ($item) {
            $item->Files = unserialize($item->Files);
        });
        return response()->json(['Status' => 200, 'Wishs' => $wishs], 200);
    }

    function getsinglewish($id)
    {
        $wish = DB::table('wishs')->where('id', '=', $id)->first();
        if ($wish) {
            onesr($wish);
            return response()->json(['Status' => 200, 'Wish' => $wish], 200);
        } else {
            return response()->json(['Status' => 200, 'Message' => 'This Wish Not Excited'], 200);
        }
    }

    function getsingleproduct($id)
    {
        $product = DB::table('products')->where('id', '=', $id)->first();
        if ($product) {
            $product->Pics = unserialize($product->Pics);
            return response()->json(['Status' => 200, 'Product' => $product], 200);
        } else {
            return response()->json(['Status' => 200, 'Message' => 'This Product Not Excited'], 200);
        }
    }

    function getsingleservice($id)
    {
        $service = DB::table('services')->where('id', '=', $id)->first();
        if ($service) {
            $service->Pics = unserialize($service->Pics);
            return response()->json(['Status' => 200, 'Service' => $service], 200);
        } else {
            return response()->json(['Status' => 200, 'Message' => 'This Service Not Excited'], 200);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 200, 'message' => 'Your Logout Success'], 200);
    }
}
