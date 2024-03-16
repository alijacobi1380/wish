<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    function Getusers()
    {
        $users = User::get();
        return response()->json(['UsersData' => $users], 200);
    }
}
