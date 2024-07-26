<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendCategory;
use App\Http\Requests\SendTicket;
use App\Models\tickets;
use App\Models\User;
use Carbon\Carbon;
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
            $requests[$key]->Whowmakefilm = DB::table('whomakefilm')->where('RID', '=', $r->id)->first();
            switch ($r->Type) {
                case 'wish':
                    $rd = DB::table('wishs')->where('id', '=', $r->RID)->first();
                    if ($rd != null) {

                        $rd->Files = unserialize($rd->Files);
                    }
                    $requests[$key]->RequestDetail = $rd;
                    break;
                case 'product':
                    $rd = DB::table('products')->where('id', '=', $r->RID)->first();
                    if ($rd != null) {

                        $rd->Pics = unserialize($rd->Pics);
                    }
                    $requests[$key]->RequestDetail = $rd;
                    break;
                case 'service':
                    $rd = DB::table('services')->where('id', '=', $r->RID)->first();
                    if ($rd != null) {

                        $rd->Pics = unserialize($rd->Pics);
                    }
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

    function accepttrack($id)
    {
        $q = DB::table('tracklists')->where('id', '=', $id)->update([
            'AdminID' => Auth::user()->id,
            'Status' => 1,
        ]);

        $q2 = DB::table('tracklists')->where('id', '=', $id)->first();

        $r = DB::table('requests')->where('id', '=', $q2->RID)->update([
            'Status' => 4,
        ]);


        if (isset($q) && isset($r)) {
            return response()->json(['status' => 200, 'messages' => 'Track Is Accepted']);
        } else {
            return response()->json(['status' => 203, 'message' => 'Track Code Faild']);
        }
    }

    function TrackList()
    {
        $tracklist = DB::table('tracklists')->get();
        $tracklist->map(function ($item) {
            $item->senderDetail = User::where('id', '=', $item->SenderID)->first();
            $item->requestDetail = DB::table('requests')->where('id', '=', $item->RID)->first();
        });
        return $tracklist;
    }

    function addrequestdate(Request $request)
    {
        $this->validate($request, [
            'RID' => 'required',
            'date1' => 'required',
            'date2' => 'required',
            'date3' => 'required',
        ]);

        $who = DB::table('whomakefilm')->where('RID', '=', $request->RID)->first();
        if ($who) {
            if ($who->UserID != Auth::user()->id) {
                return response()->json(['status' => 200, 'messages' => 'You Cant Add Date']);
            }


            $rdate = DB::table('requestdates')->where('RequestID', '=', $request->RID)->first();
            if ($rdate == null) {
                $up = DB::table('requestdates')->insert([
                    'WhoAddedDate' => Auth::user()->id,
                    'RequestID' => $request->RID,
                    'date1' => $request->date1,
                    'date2' => $request->date2,
                    'date3' => $request->date3,
                    'Note' => $request->Note,
                    'Time' => Carbon::now()->format('Y-m-d'),
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
        } else {
            return response()->json(['status' => 203, 'message' => 'Nobody Accpet The Film Yet']);
        }
    }

    function acceptfilm(Request $request)
    {
        $request->validate([
            'RID' => 'required',
            'status' => 'required',
        ]);

        if ($request->status == 1) {
            $request->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'phone' => 'required',
                'country' => 'required',
                'city' => 'required',
                'fulladdress' => 'required',
                'zipcode' => 'required',
            ]);
        }

        $r = DB::table('requests')->where('id', '=', $request->RID)->first();

        if ($r) {

            $check = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where(function ($query) {
                $query->where('AdminStatus', '=', 1)->orWhere('CompanyStatus', '=', 1)->orWhere('FilmmakerStatus', '=', 1);
            });

            if ($check->count() != 0) {
                return response()->json(['status' => 203, 'message' => 'This Request Allready Has A Film Maker']);
            }

            $w = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('AdminStatus', '=', 1)->first();

            if (!isset($w)) {
                $who = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('UserID', '=', Auth::user()->id)->first();
                if (!$who) {
                    $q = DB::table('whomakefilm')->insert([
                        'RID' => $request->RID,
                        'AdminStatus' => $request->status,
                        'UserID' => Auth::user()->id,
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'phone' => $request->phone,
                        'country' => $request->country,
                        'city' => $request->city,
                        'zipcode' => $request->zipcode,
                        'fulladdress' => $request->fulladdress,
                    ]);
                } else {
                    $q = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('AdminStatus', '=', 0)->where('UserID', '=', Auth::user()->id)->update([
                        'AdminStatus' => $request->status,
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'phone' => $request->phone,
                        'country' => $request->country,
                        'city' => $request->city,
                        'zipcode' => $request->zipcode,
                        'fulladdress' => $request->fulladdress,
                    ]);
                }



                if ($q) {
                    if ($request->status == 1) {
                        return response()->json(['status' => 200, 'messages' => 'You Accept To Make Film']);
                    } else {
                        return response()->json(['status' => 200, 'messages' => 'You Not Accept To Make Film']);
                    }
                } else {
                    return response()->json(['status' => 203, 'message' => 'Accept Film Faild']);
                }
            } else {
                return response()->json(['status' => 203, 'message' => 'This Request Allready Has A Film Maker']);
            }
        } else {
            return response()->json(['status' => 203, 'message' => 'This Request Not Exists']);
        }
    }
}
