<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class verifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next)
    {   
        if (Auth::Check()) {
            if ( Auth::user()->isActivated() == false ) {
               
               flash("Please, check your email inbox to verify your account <a class='btn btn-danger mr-5 btn-sm float-right' href='".url('/')."/resend/activationlink'>Resend activation link</a>",'danger');
                // \Session::flash('flash_message', "Please, check your email inbox to verify your account <a class='btn btn-danger mr-5 btn-sm float-right' href='".url('/')."/register/resend/activationlink'>Resend activation link</a>");
                // \Session::flash('flash_message_level', 'danger');
                return back(); 
            }
        }
        return $next($request);
    }
}
