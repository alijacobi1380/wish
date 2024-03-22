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
        $users = User::get();
        return response()->json(['UsersData' => $users], 200);
    }


    function gettickets()
    {
        $tickets = DB::table('tickets')->get();
        $tickets->map(function ($item) {
            $item->Files = unserialize($item->Files);
        });
        return response()->json(['Tickets' => $tickets], 200);
    }
}
