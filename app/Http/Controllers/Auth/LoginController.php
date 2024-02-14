<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Cache;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout()
    {
        if (Auth::check()) {
            if (auth()->user()->hasRole('super admin')){
                Auth::logout();
                Cache::forget('user_' . auth()->id());
                return redirect('/login');
            }
            else if (auth()->user()->hasRole('pharmacy'))
            {
                Auth::logout();
                Cache::forget('user_' . auth()->id());
                return redirect('/pharmacy_login');
            }
            else if (auth()->user()->hasRole('doctor')){
                Auth::logout();
                Cache::forget('user_' . auth()->id());
                return redirect('/doctor/doctor_login');
            }
            else if (auth()->user()->hasRole('laboratory')){
                Auth::logout();
                Cache::forget('user_' . auth()->id());
                return redirect('/pathologist_login');
            }
            else{
                Auth::logout();
                Cache::forget('user_' . auth()->id());
                return redirect('/');
            }
        }
        Cache::forget('user_' . auth()->id());
        return redirect('/');
    }
}
