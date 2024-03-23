<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    function Getusers()
    {
        $users = DB::table('users')->orderBy('id', 'DESC')->get();
        return response()->json(['Status' => 200, 'UsersData' => $users], 200);
    }


    function gettickets()
    {
        $tickets = DB::table('tickets')->where('ReciverID', '=', Auth::user()->id)->orWhere('SenderID', '=', Auth::user()->id)->orderBy('id', 'DESC')->get();
        $tickets->map(function ($item) {
            $item->Files = unserialize($item->Files);
        });
        return response()->json(['Status' => 200, 'Tickets' => $tickets], 200);
    }
}
