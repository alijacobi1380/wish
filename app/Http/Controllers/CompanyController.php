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
            if ($wish) {
                $user = DB::table('users')->where('id', '=', $wish->UserID)->first();
                $r = DB::table('requests')->insertGetId([
                    'Type' => 'wish',
                    'RID' => $request->requestid,
                    'SenderID' => Auth::user()->id,
                    'ReceiverID' => $user->id,
                ]);
                return response()->json(['status' => 200, 'message' => 'Request Added Successful', 'requestID' => $r]);
            } else {
                return response()->json(['status' => 203, 'message' => 'This Wish ID Is Not Existed']);
            }
        }
    }
}
