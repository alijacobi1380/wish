<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user->email_verified_at) {
            return '';
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return 'Your Account Verifyed'; // The deep link
    }
}
