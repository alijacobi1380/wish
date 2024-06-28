<?php

namespace App\Http\Controllers;

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
        ]);

        $who = DB::table('whomakefilm')->where('RID', '=', $request->RID)->first();
        if ($who) {
            if ($who->UserID != Auth::user()->id) {
                return response()->json(['status' => 200, 'messages' => 'You Cant Add Date']);
            }


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
        $requests = DB::table('whomakefilm')->where('AdminStatus', '=', 0)->where('CompanyStatus', '=', 0)->get();
        return response()->json(['status' => 200, 'Requests' => $requests]);
    }
}
