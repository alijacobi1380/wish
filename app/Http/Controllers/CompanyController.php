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


    function ticketlists()
    {
        $tickets = DB::table('tickets')->where('SenderID', '=', Auth::user()->id)->orWhere('ReciverID', '=', Auth::user()->id)->orderBy('id', 'DESC')->get();
        $tickets->map(function ($item) {
            $item->Files = unserialize($item->Files);
        });
        return response()->json(['Status' => 200, 'Tickets' => $tickets], 200);
    }

    function sendticket(SendTicket $request)
    {

        $data = [];
        if ($request->hasfile('ticketfiles') && is_array($request->ticketfiles)) {
            foreach ($request->file('ticketfiles') as $key => $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path() . '/files/tickets', time() . '_' . $name);
                $data[$key] = asset('files/tickets') . '/' . time() . '_' . $name;
            }
        } elseif ($request->hasfile('ticketfiles')) {
            $file = $request->file('ticketfiles');
            $name = $file->getClientOriginalName();
            $file->move(public_path() . '/files/tickets', time() . '_' . $file->getClientOriginalName());
            $data = asset('files/tickets') . '/' . time() . '_' . $name;
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
