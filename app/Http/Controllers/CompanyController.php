<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendTicket;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    function getadminlist()
    {
        return response()->json(['admins' => User::where('Role', '=', 'Admin')->get()]);
    }


    function sendticket(SendTicket $request)
    {
        $data = [];
        if ($request->hasfile('ticketfiles')) {
            foreach ($request->file('ticketfiles') as $key => $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path() . '/files/tickets', time() . '_' . $name);
                $data[$key] = asset('files/tickets') . '/' . time() . '_' . $name;
            }
        }

        $reciveruser = DB::table('users')->where('id', '=', $request->reciverid)->first();

        $ticket = DB::table('tickets')->insertGetId(
            [
                'Title' => $request->title,
                'Status' => 1,
                'SenderName' => Auth::user()->name . ' ' . Auth::user()->lastname,
                'SenderID' => Auth::user()->id,
                'ReciverName' => $reciveruser->name . ' ' . $reciveruser->lastname,
                'ReciverID' => $request->reciverid,
                'Desc' => $request->desc,
                'Files' => serialize($data),
            ]
        );

        if ($ticket) {
            return response()->json(['status' => 200, 'messages' => 'Ticket Created', 'TicketID' => $ticket]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Ticket Create Faild']);
        }
    }
}
