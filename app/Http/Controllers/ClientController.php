<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendTicket;
use App\Http\Requests\SendWish;
use App\Models\tickets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use function App\Providers\manysr;
use function App\Providers\onesr;

class ClientController extends Controller
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

    function sendwish(SendWish $request)
    {

        $data = [];
        if ($request->hasfile('wishfiles') && is_array($request->wishfiles)) {
            foreach ($request->file('wishfiles') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/wishs/', $name);
                $data[$key] = asset('files/wishs/') . '/' . $name;
            }
        } elseif ($request->hasfile('wishfiles')) {
            $file = $request->file('wishfiles');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/wishs/', $name);
            $data = asset('files/wishs') . '/' . $name;
        }



        $wish = DB::table('wishs')->insertGetId(
            [
                'Title' => $request->title,
                'UserID' => Auth::user()->id,
                'Desc' => $request->desc,
                'Files' => serialize($data),
                'Category' => $request->category,
                'Importance' => $request->importance,
                'MiniDesc' => $request->minidesc,
            ]
        );

        if ($wish) {
            return response()->json(['status' => 200, 'messages' => 'Wish Created', 'WishID' => $wish]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Wish Create Faild']);
        }
    }

    function wishlist()
    {
        $wishs = DB::table('wishs')->orderBy('id', 'DESC')->get();
        manysr($wishs);

        return response()->json(['Status' => 200, 'Wishs' => $wishs], 200);
    }

    function deletewish($id)
    {
        $wish = DB::table('wishs')->where('id', '=', $id)->first();
        if ($wish && $wish->UserID == Auth::user()->id) {
            if ($wish->Files && is_array(unserialize($wish->Files))) {
                foreach (unserialize($wish->Files) as $pc) {
                    $d = str_replace(URL::to('/') . '/', "", $pc);
                    unlink($d);
                }
            } else {
                unlink(str_replace(URL::to('/') . '/', "", unserialize($wish->Files)));
            }
            DB::table('wishs')->where('id', '=', $id)->delete();
            return response()->json(['status' => 200, 'messages' => 'Wish Deleted', 'WishID' => $id]);
        } else {
            return response()->json(['status' => 404, 'messages' => 'Wish Not Exists', 'WishID' => $id]);
        }
    }

    function addrequest(Request $request)
    {
        $request->validate([
            'requestid' => 'required',
            'type' => 'required',
        ]);

        $requestcheck = DB::table('requests')->where('Type', '=', $request->type)->where('SenderID', '=', Auth::user()->id)->where('RID', '=', $request->requestid)->first();
        if ($requestcheck) {
            return response()->json(['status' => 203, 'message' => 'You Have Already Submitted This Request']);
        } else {
            if ($request->type == 'service') {
                $data = DB::table('services')->where('id', '=', $request->requestid)->first();
            } elseif ($request->type == 'product') {
                $data = DB::table('products')->where('id', '=', $request->requestid)->first();
            }

            if ($data) {
                $user = DB::table('users')->where('id', '=', $data->UserID)->first();
                $r = DB::table('requests')->insertGetId([
                    'Type' => $request->type,
                    'RID' => $request->requestid,
                    'SenderID' => Auth::user()->id,
                    'ReceiverID' => $user->id,
                ]);
                return response()->json(['status' => 200, 'message' => 'Request Added Successful', 'requestID' => $r]);
            } else {
                return response()->json(['status' => 203, 'message' => 'This Service Or Product ID Is Not Existed']);
            }
        }
    }
}
