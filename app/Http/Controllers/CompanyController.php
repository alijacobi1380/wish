<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendTicket;
use App\Models\tickets;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function App\Providers\manysr;
use function App\Providers\onesr;

class CompanyController extends Controller
{
    function getadminlist()
    {
        return response()->json(['admins' => User::where('Role', '=', 'Admin')->get()]);
    }


    function ticketlists()
    {
        $tickets = DB::table('tickets')->where(function ($query) {
            $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReciverID', '=', Auth::user()->id);
        })->orderBy('id', 'DESC')->get();
        manysr($tickets);



        return response()->json(['Status' => 200, 'Tickets' => $tickets], 200);
    }

    function replaylists($id)
    {

        // $ticket = DB::table('tickets')->where('id', '=', $id)->where(function ($query) {
        //     $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReciverID', '=', Auth::user()->id);
        // })->first();
        $ticket = tickets::where('id', '=', $id)->where(function ($query) {
            $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReciverID', '=', Auth::user()->id);
        })->first();
        if ($ticket) {
            onesr($ticket);
            $replays = $ticket->replays()->orderBy('id', 'DESC')->get();
            manysr($replays);
            return response()->json(['Status' => 200, 'Ticket' => $ticket, 'Replays' => $replays]);
        } else {
            return response()->json(['Status' => 400, 'Ticket' => null]);
        }
    }

    function sendreplay(ReplayTicket $request)
    {

        $data = [];
        if ($request->hasfile('ticketfiles') && is_array($request->ticketfiles)) {
            foreach ($request->file('ticketfiles') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/tickets/', $name);
                $data[$key] = asset('files/tickets/') . '/' . $name;
            }
        } elseif ($request->hasfile('ticketfiles')) {
            $file = $request->file('ticketfiles');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/tickets/', $name);
            $data = asset('files/tickets') . '/' . $name;
        }


        $ticket = DB::table('replays')->insertGetId(
            [
                'Title' => $request->title,
                'TicketID' => $request->ticketid,
                'SenderID' => Auth::user()->id,
                'SenderName' => Auth::user()->name,
                'Desc' => $request->desc,
                'Files' => serialize($data),
            ]
        );

        if ($ticket) {
            return response()->json(['status' => 200, 'messages' => 'Replay Added', 'TicketID' => $ticket]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Replay Added Faild']);
        }
    }
    function sendticket(SendTicket $request)
    {

        $data = [];
        if ($request->hasfile('ticketfiles') && is_array($request->ticketfiles)) {
            foreach ($request->file('ticketfiles') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/tickets/', $name);
                $data[$key] = asset('files/tickets/') . '/' . $name;
            }
        } elseif ($request->hasfile('ticketfiles')) {
            $file = $request->file('ticketfiles');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/tickets/', $name);
            $data = asset('files/tickets') . '/' . $name;
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
