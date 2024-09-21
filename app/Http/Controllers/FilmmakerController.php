<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FilmmakerController extends Controller
{
    function addrequestdate(Request $request)
    {
        $this->validate($request, [
            'RID' => 'required',
            'date1' => 'required',
            'date2' => 'required',
            'date3' => 'required',
            'Time' => Carbon::now()->format('Y-m-d'),
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
                ]);


                if ($up) {
                    DB::table('requests')->where('RID', '=', $request->RID)->update([
                        'status' => 2,
                    ]);

                    $rn = DB::table('requests')->where('RID', '=', $request->RID)->first();
                    addnotif($rn->SenderID, Auth::user()->name . ' Added Date To Your Request. Request ID :' . $request->RID, $request->RID);
                    addnotif($rn->ReceiverID, Auth::user()->name . ' Added Date To Your Request. Request ID :' . $request->RID, $request->RID);

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

            $w = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('FilmmakerStatus', '=', 1)->first();

            if (!isset($w)) {
                $who = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('UserID', '=', Auth::user()->id)->first();
                if (!$who) {
                    $q = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('CompanyStatus', '=', 0)->update([
                        'RID' => $request->RID,
                        'FilmmakerStatus' => $request->status,
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
                    $q = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('FilmmakerStatus', '=', 0)->where('UserID', '=', Auth::user()->id)->update([
                        'FilmmakerStatus' => $request->status,
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
                        $rn = DB::table('requests')->where('RID', '=', $request->RID)->first();
                        addnotif($rn->SenderID, Auth::user()->name . ' Accepted To Make Film. Request ID :' . $request->RID, $request->RID);
                        addnotif($rn->ReceiverID, Auth::user()->name . ' Accepted To Make Film. Request ID :' . $request->RID, $request->RID);

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

    function requestlist()
    {
        $whos = DB::table('whomakefilm')->where('AdminStatus', '=', 0)->where('CompanyStatus', '=', 0)->orWhere('FilmmakerStatus', '=', 1)->where('UserID', '=', Auth::user()->id)->orderBy('id', 'DESC')->get();






        // return $whos;
        // $requests = [];



        foreach ($whos as $key => $w) {
            $requests[$key] = DB::table('requests')->where('id', '=', $w->RID)->orderBy('id', 'DESC')->first();





            $r = DB::table('requests')->where('id', '=', $w->RID)->orderBy('id', 'DESC')->first();
            if ($r) {

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
        }

        if (isset($requests)) {
            return response()->json(['Status' => 200, 'Requests' => $requests], 200);
        } else {
            return response()->json(['Status' => 200, 'Requests' => []], 200);
        }
    }

    function accepttrack($id)
    {
        $q = DB::table('tracklists')->where('id', '=', $id)->update([
            'FilmmakerID' => Auth::user()->id,
            'Status' => 1,
        ]);

        $q2 = DB::table('tracklists')->where('id', '=', $id)->first();

        $r = DB::table('requests')->where('id', '=', $q2->RID)->update([
            'Status' => 4,
        ]);
        $rid = DB::table('requests')->where('id', '=', $q2->RID)->first();


        if (isset($q) && isset($r)) {
            addnotif($q2->SenderID, Auth::user()->name . ' Accepted The Track. Track ID :' . $id, $rid->RID);

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

    function getnotifications()
    {
        $notifications = DB::table('notifications')->where('UserID', '=', Auth::user()->id)->orderBy('id', 'DESC')->get();
        DB::table('notifications')->where('UserID', '=', Auth::user()->id)->where('Seen', '=', 0)->update([
            'Seen' => 1,
        ]);
        return response()->json(['Status' => 200, 'Notifications' => $notifications], 200);
    }

    function getnotificationscount()
    {
        $notificationcount = DB::table('notifications')->where('UserID', '=', Auth::user()->id)->where('Seen', '=', 0)->count();

        return response()->json(['Status' => 200, 'Notificationcount' => $notificationcount], 200);
    }
}
