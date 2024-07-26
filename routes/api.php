<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Client;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FilmmakerController;
use App\Http\Controllers\UsersController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {


    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('auth:sanctum')->post('/editprofile', [UsersController::class, 'editprofile'])->name('editprofile');

    // Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    //     $request->fulfill();

    //     return redirect('/home');
    // })->middleware(['auth', 'signed'])->name('verification.verify');


    Route::get('/email/verify', [UsersController::class, 'verifyemail'])->middleware('auth:sanctum');
    Route::get('/email/verify/{code}', [UsersController::class, 'accepctemail'])->name('accepctemail');

    Route::post('/forgot-password', [UsersController::class, 'forgetpassword'])->name('forgetpassword');
    Route::post('/changepassword', [UsersController::class, 'changepassword'])->name('changepassword');

    // Auth Routes
    Route::delete('/logout', [UsersController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/register', [UsersController::class, 'register']);

    // Global Routes
    Route::get('categorielist', [UsersController::class, 'categorielist'])->name('categorielist');
    Route::get('productlist', [UsersController::class, 'productlist'])->name('productlist');
    Route::get('servicelist', [UsersController::class, 'servicelist'])->name('servicelist');
    Route::get('wishlist', [UsersController::class, 'wishlist'])->name('wishlist');
    Route::get('getsinglewish/{id}', [UsersController::class, 'getsinglewish'])->name('getsinglewish');
    Route::get('getsingleproduct/{id}', [UsersController::class, 'getsingleproduct'])->name('getsingleproduct');
    Route::get('getsingleservice/{id}', [UsersController::class, 'getsingleservice'])->name('getsingleservice');



    // Admin Routes
    Route::name('admin.')->prefix('admin')->controller(AdminController::class)->middleware(['Admin', 'auth:sanctum', 'verified'])->group(function () {


        Route::get('userslist', 'Getusers')->name('getusers');

        // Tickets
        Route::get('ticketlist', 'gettickets')->name('gettickets');
        Route::get('replaylists/{id}', 'replaylists')->name('replaylists');
        Route::post('sendreplay/{id}', 'sendreplay')->name('sendreplay');

        // Category
        Route::post('sendcategorie', 'sendcategorie')->name('sendcategorie');
        Route::get('deletecategorie/{id}', 'deletecategorie')->name('deletecategorie');
        Route::post('updatecategorie/{id}', 'updatecategorie')->name('updatecategorie');

        // Requests
        Route::get('requestlist', 'requestlist')->name('requestlist');
        Route::get('getrequest/{id}', 'getrequest')->name('getrequest');
        Route::post('updaterequest', 'updaterequest')->name('updaterequest');
        Route::post('addrequestdate', 'addrequestdate')->name('addrequestdate');

        // Post Track
        Route::get('accepttrack/{id}', 'accepttrack')->name('accepttrack');
        Route::get('tracklist', 'TrackList')->name('TrackList');

        // Accept Film
        Route::post('acceptfilm', 'acceptfilm')->name('acceptfilm');
    });


    // Company Routes
    Route::name('company.')->prefix('company')->controller(CompanyController::class)->middleware(['Company', 'auth:sanctum', 'verified'])->group(function () {

        // Tickets
        Route::get('adminlist', 'getadminlist')->name('getadmins');
        Route::post('sendticket', 'sendticket')->name('sendticket');
        Route::get('ticketlists', 'ticketlists')->name('ticketlists');
        Route::post('sendreplay', 'sendreplay')->name('sendreplay');
        Route::get('replaylists/{id}', 'replaylists')->name('replaylists');

        // Products
        Route::post('addproduct', 'addproduct')->name('addproduct');
        Route::post('updateproduct/{id}', 'updateproduct')->name('updateproduct');
        Route::get('deleteproduct/{id}', 'deleteproduct')->name('deleteproduct');
        Route::get('productlist', 'productlist')->name('productlist');


        // Services
        Route::post('addservice', 'addservice')->name('addservice');
        Route::post('updateservice/{id}', 'updateservice')->name('updateservice');
        Route::get('deleteservice/{id}', 'deleteservice')->name('deleteservice');
        Route::get('servicelist', 'servicelist')->name('servicelist');

        // Requests
        Route::post('addrequest', 'addrequest')->name('addrequest');
        Route::get('requestlist', 'requestlist')->name('requestlist');
        Route::post('addrequestdate', 'addrequestdate')->name('addrequestdate');
        Route::post('acceptdate', 'acceptdate')->name('acceptdate');

        // TrackPost
        Route::post('addtrackpostcode', 'addtrackpostCode')->name('addtrackpostCode');

        // Accept Film
        Route::post('acceptfilm', 'acceptfilm')->name('acceptfilm');
    });


    // Client Routes
    Route::name('client.')->prefix('client')->controller(ClientController::class)->middleware(['Client', 'auth:sanctum', 'verified'])->group(function () {

        // Tickets
        Route::get('adminlist', 'getadminlist')->name('getadmins');
        Route::post('sendticket', 'sendticket')->name('sendticket');
        Route::get('ticketlists', 'ticketlists')->name('ticketlists');
        Route::post('sendreplay', 'sendreplay')->name('sendreplay');
        Route::get('replaylists/{id}', 'replaylists')->name('replaylists');

        // Wish
        Route::post('sendwish', 'sendwish')->name('sendwish');
        Route::get('wishlist', 'wishlist')->name('wishlist');
        Route::get('deletewish/{id}', 'deletewish')->name('wishlist');

        // Requests
        Route::post('addrequest', 'addrequest')->name('addrequest');
        Route::get('requestlist', 'requestlist')->name('requestlist');
        Route::post('acceptdate', 'acceptdate')->name('acceptdate');

        // Route::post('addrequestdate', 'addrequestdate')->name('addrequestdate');

        // Accept Film
        Route::post('acceptfilm', 'acceptfilm')->name('acceptfilm');
    });

    Route::name('filmmaker.')->prefix('filmmaker')->controller(FilmmakerController::class)->middleware(['Filmmaker', 'auth:sanctum', 'verified'])->group(function () {


        // Requests
        Route::post('addrequestdate', 'addrequestdate')->name('addrequestdate');

        // Accept Film
        Route::post('acceptfilm', 'acceptfilm')->name('acceptfilm');
        Route::get('requestlist', 'requestlist')->name('requestlist');

        // Post Track
        Route::get('accepttrack/{id}', 'accepttrack')->name('accepttrack');
        Route::get('tracklist', 'TrackList')->name('TrackList');
    });
});
