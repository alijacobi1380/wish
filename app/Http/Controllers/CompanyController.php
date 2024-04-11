<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplayTicket;
use App\Http\Requests\SendProduct;
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
                'Pics' => serialize($data),
                'Rate' => $request->rate
            ]
        );

        if ($product) {
            return response()->json(['status' => 200, 'messages' => 'Product Added', 'ProductID' => $product]);
        } else {
            return response()->json(['status' => 203, 'message' => 'Product Added Faild']);
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
}
