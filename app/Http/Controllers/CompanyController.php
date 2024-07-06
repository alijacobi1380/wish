<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendProduct;
use App\Http\Requests\SendService;
use App\Http\Requests\SendTicket;
use App\Models\tickets;
use App\Models\User;
use Dotenv\Validator;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use function App\Providers\manysr;
use function App\Providers\onesr;

class CompanyController extends Controller
{
    function getadminlist()
    {
        return response()->json(['admins' => User::where('Role', '=', 'Admin')->get()]);
    }

    function productlist()
    {
        $products = DB::table('products')->where('UserID', '=', Auth::user()->id)->get();
        $products->map(function ($item) {
            $item->Pics = unserialize($item->Pics);
        });
        return response()->json(['status' => 200, 'Products' => $products]);
    }

    function deleteproduct($id)
    {
        $product = DB::table('products')->where('id', '=', $id)->first();
        if ($product && $product->UserID == Auth::user()->id) {
            if ($product->Pics && is_array(unserialize($product->Pics))) {
                foreach (unserialize($product->Pics) as $pc) {
                    $d = str_replace(URL::to('/') . '/', "", $pc);
                    unlink($d);
                }
            } else {
                unlink(str_replace(URL::to('/') . '/', "", unserialize($product->Pics)));
            }
            DB::table('products')->where('id', '=', $id)->delete();
            return response()->json(['status' => 200, 'messages' => 'Product Deleted', 'ProductID' => $id]);
        } else {
            return response()->json(['status' => 404, 'messages' => 'Product Not Exists', 'ProductID' => $id]);
        }
    }

    function addproduct(SendProduct $request)
    {

        $data = [];
        if ($request->hasfile('pics') && is_array($request->pics)) {
            foreach ($request->file('pics') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/products/', $name);
                $data[$key] = asset('files/products/') . '/' . $name;
            }
        } elseif ($request->hasfile('pics')) {
            $file = $request->file('pics');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/products/', $name);
            $data = asset('files/products') . '/' . $name;
        }


        $product = DB::table('products')->insertGetId(
            [
                'Title' => $request->title,
                'UserID' => Auth::user()->id,
                'Price' => $request->price,
                'Category' => $request->category,
                'Desc' => $request->desc,
                'EANCode' => $request->eancode,
                'Pics' => serialize($data),
                'Status' => $request->status
            ]
        );

        if ($product) {
            return response()->json(['status' => 200, 'messages' => 'Product Added', 'ProductID' => $product]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Product Added Faild']);
        }
    }

    function updateproduct($id, Request $request)
    {

        $data = [];
        if ($request->hasfile('pics') && is_array($request->pics)) {
            foreach ($request->file('pics') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/products/', $name);
                $data[$key] = asset('files/products/') . '/' . $name;
            }
        } elseif ($request->hasfile('pics')) {
            $file = $request->file('pics');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/products/', $name);
            $data = asset('files/products') . '/' . $name;
        }

        $p = DB::table('products')->where('id', '=', $id)->first();
        if ($p->UserID == Auth::user()->id) {
            $product = DB::table('products')->where('id', '=', $id)->update(
                [
                    'Title' => $request->title,
                    'Price' => $request->price,
                    'Category' => $request->category,
                    'Desc' => $request->desc,
                    'Pics' => serialize($data),
                    'Status' => $request->status,
                    'Rate' => $request->rate,
                    'RateCount' => $request->ratecount,
                ]
            );
        }

        if ($product) {
            return response()->json(['status' => 200, 'messages' => 'Product Updated', 'ProductID' => $product]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Product Updated Faild']);
        }
    }


    function servicelist()
    {
        $services = DB::table('services')->where('UserID', '=', Auth::user()->id)->get();
        $services->map(function ($item) {
            $item->Pics = unserialize($item->Pics);
        });
        return response()->json(['status' => 200, 'services' => $services]);
    }

    function deleteservice($id)
    {
        $service = DB::table('services')->where('id', '=', $id)->first();
        if ($service && $service->UserID == Auth::user()->id) {
            if ($service->Pics && is_array(unserialize($service->Pics))) {
                foreach (unserialize($service->Pics) as $pc) {
                    $d = str_replace(URL::to('/') . '/', "", $pc);
                    unlink($d);
                }
            } else {
                unlink(str_replace(URL::to('/') . '/', "", unserialize($service->Pics)));
            }
            DB::table('services')->where('id', '=', $id)->delete();
            return response()->json(['status' => 200, 'messages' => 'Service Deleted', 'ServiceID' => $id]);
        } else {
            return response()->json(['status' => 404, 'messages' => 'Service Not Exists', 'ServiceID' => $id]);
        }
    }

    function addservice(SendService $request)
    {

        $data = [];
        if ($request->hasfile('pics') && is_array($request->pics)) {
            foreach ($request->file('pics') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/services/', $name);
                $data[$key] = asset('files/services/') . '/' . $name;
            }
        } elseif ($request->hasfile('pics')) {
            $file = $request->file('pics');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/services/', $name);
            $data = asset('files/services') . '/' . $name;
        }


        $service = DB::table('services')->insertGetId(
            [
                'Title' => $request->title,
                'UserID' => Auth::user()->id,
                'Price' => $request->price,
                'Category' => $request->category,
                'Desc' => $request->desc,
                'Pics' => serialize($data),
                'Status' => $request->status
            ]
        );

        if ($service) {
            return response()->json(['status' => 200, 'messages' => 'Service Added', 'ServiceID' => $service]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Service Added Faild']);
        }
    }

    function updateservice($id, Request $request)
    {

        $data = [];
        if ($request->hasfile('pics') && is_array($request->pics)) {
            foreach ($request->file('pics') as $key => $file) {
                $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path() . '/files/services/', $name);
                $data[$key] = asset('files/services/') . '/' . $name;
            }
        } elseif ($request->hasfile('pics')) {
            $file = $request->file('pics');
            $name = time() . rand(1, 100) . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path() . '/files/services/', $name);
            $data = asset('files/services') . '/' . $name;
        }

        $p = DB::table('services')->where('id', '=', $id)->first();
        if ($p->UserID == Auth::user()->id) {
            $service = DB::table('services')->where('id', '=', $id)->update(
                [
                    'Title' => $request->title,
                    'Price' => $request->price,
                    'Category' => $request->category,
                    'Desc' => $request->desc,
                    'Pics' => serialize($data),
                    'Status' => $request->status,
                    'Rate' => $request->rate,
                    'RateCount' => $request->ratecount,
                ]
            );
        }

        if ($service) {
            return response()->json(['status' => 200, 'messages' => 'Service Updated', 'ServiceID' => $service]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Service Updated Faild']);
        }
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

    function addrequest(Request $request)
    {
        $request->validate([
            'requestid' => 'required',
        ]);

        $requestcheck = DB::table('requests')->where('Type', '=', 'wish')->where('SenderID', '=', Auth::user()->id)->where('RID', '=', $request->requestid)->first();
        if ($requestcheck) {
            return response()->json(['status' => 203, 'message' => 'You Have Already Submitted This Request']);
        } else {
            $wish = DB::table('wishs')->where('id', '=', $request->requestid)->first();
            $cat = DB::table('categories')->where('id', '=', $wish->Category)->first();
            if ($cat->Type == 1) {
                $status = 2;
            } else {
                $status = 4;
            }
            if ($wish) {
                $user = DB::table('users')->where('id', '=', $wish->UserID)->first();
                $r = DB::table('requests')->insertGetId([
                    'Type' => 'wish',
                    'RID' => $request->requestid,
                    'SenderID' => Auth::user()->id,
                    'ReceiverID' => $user->id,
                    'Status' => $status,
                ]);
                return response()->json(['status' => 200, 'message' => 'Request Added Successful', 'requestID' => $r]);
            } else {
                return response()->json(['status' => 203, 'message' => 'This Wish ID Is Not Existed']);
            }
        }
    }


    function requestlist()
    {
        $requests = DB::table('requests')->where(function ($query) {
            $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReceiverID', '=', Auth::user()->id);
        })->orderBy('id', 'DESC')->get();
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

    function addtrackpostCode(Request $request)
    {
        $request->validate([
            'Code' => 'required',
            'RID' => 'required',
            'PostCompanyName' => 'required',
        ]);

        $r = DB::table('requests')->where('id', '=', $request->RID)->where(function ($query) {
            $query->where('SenderID', '=', Auth::user()->id)->orWhere('ReceiverID', '=', Auth::user()->id);
        })->first();

        if ($r) {
            $check = DB::table('tracklists')->where('RID', '=', $request->RID)->where('SenderID', '=', Auth::user()->id)->first();
            if ($check) {
                return response()->json(['status' => 203, 'message' => 'You Have Recently Registered The Code']);
            } else {
                if ($r->Type == 'wish') {
                    $w = DB::table('wishs')->where('id', '=', $r->RID)->first();
                    $c = DB::table('categories')->where('id', '=', $w->Category)->first();
                    if ($c->Type == 1) {
                        return $this->addpost($request->Code, $request->RID, $request->PostCompanyName);
                    } else {
                        return response()->json(['status' => 203, 'message' => 'This Request does not require a physical product']);
                    }
                } elseif ($r->Type == 'product') {
                    return $this->addpost($request->Code, $request->RID, $request->PostCompanyName);
                } else {
                    return response()->json(['status' => 203, 'message' => 'This Request does not require a physical product']);
                }
            }
        } else {
            return response()->json(['status' => 203, 'message' => 'Request Not Exists']);
        }
    }
    public function addpost($Code, $RID, $PostCompanyName)
    {
        $q = DB::table('tracklists')->insertGetId([
            'TrackCode' => $Code,
            'RID' => $RID,
            'PostCompanyName' => $PostCompanyName,
            'SenderID' => Auth::user()->id,
            'Status' => 3,
        ]);

        if ($q) {
            return response()->json(['status' => 200, 'messages' => 'Your Code Is Saved', 'TrackID' => $q]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Track Code Faild']);
        }
    }

    function acceptfilm(Request $request)
    {
        $request->validate([
            'RID' => 'required',
            'status' => 'required',
        ]);



        $r = DB::table('requests')->where('id', '=', $request->RID)->first();

        if ($r) {

            $check = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where(function ($query) {
                $query->where('AdminStatus', '=', 1)->orWhere('CompanyStatus', '=', 1)->orWhere('FilmmakerStatus', '=', 1);
            });

            if ($check->count() != 0) {
                return response()->json(['status' => 203, 'message' => 'This Request Allready Has A Film Maker']);
            }

            $w = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('CompanyStatus', '=', 1)->first();

            if (!isset($w)) {
                $who = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('UserID', '=', Auth::user()->id)->first();
                if (!$who) {
                    $q = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('AdminStatus', '=', 0)->update([
                        'RID' => $request->RID,
                        'CompanyStatus' => $request->status,
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
                    $q = DB::table('whomakefilm')->where('RID', '=', $request->RID)->where('CompanyStatus', '=', 0)->where('UserID', '=', Auth::user()->id)->update([
                        'CompanyStatus' => $request->status,
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

    function acceptdate(Request $request)
    {
        $request->validate([
            'RID' => 'required'
        ]);

        $r = DB::table('requestdates')->where('RequestID', '=', $request->RID)->first();
        if ($r && $r->WhoAddedDate != Auth::user()->id) {
            $request->validate([
                'SelectDate' => 'required'
            ]);

            $rp = DB::table('requestdates')->where('RequestID', '=', $request->RID)->update([
                'CompanyDate' => $request->SelectDate,
            ]);
            if ($rp) {
                return response()->json(['status' => 200, 'message' => 'Your Selected Date Saved']);
            } else {
                return response()->json(['status' => 200, 'message' => 'Saved Date Faild']);
            }
        } else {
            return response()->json(['status' => 203, 'message' => 'You Added Date Before']);
        }
    }
}
