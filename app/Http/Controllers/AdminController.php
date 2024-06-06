<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendCategory;
use App\Http\Requests\SendTicket;
use App\Models\tickets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Ticket;

use function App\Providers\manysr;
use function App\Providers\onesr;

class AdminController extends Controller
{
    function Getusers()
    {
        $users = DB::table('users')->orderBy('id', 'DESC')->get();
        return response()->json(['Status' => 200, 'UsersData' => $users], 200);
    }


    function sendcategorie(SendCategory $request)
    {
        $categorie = DB::table('categories')->insertGetId([
            'Title' => $request->title,
            'Icon' => $request->icon,
            'SubCat' => $request->subcat,
            'Type' => $request->type,
            'Desc' => $request->desc,
        ]);


        if ($categorie) {
            return response()->json(['status' => 200, 'messages' => 'categorie Added', 'CategoryID' => $categorie]);
        } else {
            return response()->json(['status' => 203, 'message' => 'categorie Added Faild']);
        }
    }

    function updatecategorie(SendCategory $request, $id)
    {
        $categorie = DB::table('categories')->where('id', '=', $id)->update([
            'Title' => $request->title,
            'Icon' => $request->icon,
            'SubCat' => $request->subcat
        ]);


        if ($categorie) {
            return response()->json(['status' => 200, 'messages' => 'Categorie Updated', 'CategoryID' => $categorie]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Categorie Updated Faild']);
        }
    }

    function deletecategorie($id)
    {
        $categorie = DB::table('categories')->where('id', '=', $id)->delete();


        if ($categorie) {
            return response()->json(['status' => 200, 'messages' => 'Categorie Deleted', 'CategoryID' => $categorie]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Categorie Deleted Faild']);
        }
    }

    function replaylists($id)
    {
        $ticket = tickets::where('id', '=', $id)->first();
        if ($ticket) {
            onesr($ticket);
            $replays = $ticket->replays()->orderBy('id', 'DESC')->get();
            manysr($replays);
            return response()->json(['Status' => 200, 'Ticket' => $ticket, 'Replays' => $replays]);
        } else {
            return response()->json(['Status' => 400, 'Ticket' => null]);
        }
    }

    function sendreplay($id, ReplayTicket $request)
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



        $ticket = DB::table('tickets')->where('id', '=', $id)->update(
            [
                'Status' => $request->status,
            ]
        );

        $ticket = DB::table('replays')->insertGetId(
            [
                'Title' => $request->title,
                'TicketID' => $id,
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


    function gettickets()
    {
        $tickets = DB::table('tickets')->where(function ($query) {
            $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReciverID', '=', Auth::user()->id);
        })->orderBy('id', 'DESC')->get();
        manysr($tickets);
        return response()->json(['Status' => 200, 'Tickets' => $tickets], 200);
    }

    function requestlist()
    {
        $requests = DB::table('requests')->orderBy('id', 'DESC')->get();
        foreach ($requests as $key => $r) {
            $requests[$key]->SenderUser = User::where('id', '=', $r->SenderID)->first();
            $requests[$key]->ReceiverUser = User::where('id', '=', $r->ReceiverID)->first();
            $requests[$key]->Dates = DB::table('requestdates')->where('RequestID', '=', $r->id)->first();
            switch ($r->Type) {
                case 'wish':
                    $rd = DB::table('wishs')->where('id', '=', $r->RID)->first();
                    $rd->Files = unserialize($rd->Files);
                    $requests[$key]->RequestDetail = $rd;
                    break;
                case 'product':
                    $rd = DB::table('products')->where('id', '=', $r->RID)->first();
                    $rd->Files = unserialize($rd->Files);
                    $requests[$key]->RequestDetail = $rd;
                    break;
                case 'service':
                    $rd = DB::table('services')->where('id', '=', $r->RID)->first();
                    $rd->Pics = unserialize($rd->Pics);
                    $requests[$key]->RequestDetail = $rd;
                    break;
            }
        }
        return response()->json(['Status' => 200, 'Requests' => $requests], 200);
    }

    function updaterequest(Request $request)
    {
        $this->validate($request, [
            'RID' => 'required',
            'date1' => 'required',
            'date2' => 'required',
            'date3' => 'required',
        ]);

        $rdate = DB::table('requestdates')->where('RequestID', '=', $request->RID)->first();
        if ($rdate == null) {
            $up = DB::table('requestdates')->insert([
                'RequestID' => $request->RID,
                'date1' => $request->date1,
                'date2' => $request->date2,
                'date3' => $request->date3,
            ]);


            if ($up) {
                DB::table('requests')->where('RID', '=', $request->RID)->update([
                    'status' => 2,
                ]);
                return response()->json(['status' => 200, 'messages' => 'Update Requested']);
            } else {
                return response()->json(['status' => 203, 'message' => 'Update Requested Faild']);
            }
        } else {
            return response()->json(['status' => 203, 'message' => 'Request Already Has Date']);
        }
    }



    function getrequest($id)
    {
        $requests = DB::table('requests')->where('id', '=', $id)->first();
        $senderuser = DB::table('users')->where('id', '=', $requests->SenderID)->first();
        $receiveruser = DB::table('users')->where('id', '=', $requests->ReceiverID)->first();
        return response()->json(['Status' => 200, 'Requests' => $requests], 200);
    }
}
