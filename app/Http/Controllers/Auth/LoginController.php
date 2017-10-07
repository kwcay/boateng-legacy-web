<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function boot()
    {
        $this->setRedirectUrl();
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function setRedirectUrl()
    {
        if (app()->request->route() && app()->request->route()->getName() !== 'login') {
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
