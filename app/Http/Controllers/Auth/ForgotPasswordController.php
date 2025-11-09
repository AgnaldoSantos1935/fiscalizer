<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

protected function throttleKey(Request $request)
{
    // ⚙️ Aqui definimos um throttle diferente (ex: 10 segundos)
    return strtolower($request->input($this->username())).'|'.$request->ip();
}

protected function sendResetLinkResponse(Request $request, $response)
{
    return back()->with('status', trans($response))
        ->with('cooldown', 60); // opcional: informa tempo de espera
}

}
