<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setRedirectUrl();
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function setRedirectUrl()
    {
        if (app()->request->route()->getName() !== 'login') {
            return;
        }

        $tld        = env('APP_ENV') === 'local' ? 'local' : 'com';
        $components = parse_url(app()->request->server->get('HTTP_REFERER', ''));
        if ($components
            && isset($components['host'])
            && isset($components['path'])
            && strtolower($components['host']) === 'www.doraboateng.'.$tld
        ) {
            session(['login-redirect' => $components['path']]);
        }
    }

    protected function redirectTo()
    {
        return session('login-redirect', route('home'));
    }
}
