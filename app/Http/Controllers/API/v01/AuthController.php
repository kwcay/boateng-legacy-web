<?php
/**
 * AuthController - API v0.1
 */
namespace App\Http\Controllers\API\v01;

use Auth;
use Crypt;
use Request;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     *
     */
    public function login()
    {
        // Performance check.
        $credentials = Request::only(['email', 'password']);
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return response('Unauthorized.', 401);
        }

        // TODO: count login attempts.
        // ...

        // Authenticate.
        if (!Auth::attempt($credentials, false)) {
            return response('Unauthorized.', 401);
        }

        // Return encrypted token.
        return [
            'token' => Crypt::encrypt(csrf_token())
        ];
    }

    /**
     *
     */
    public function sendToService($service)
    {
        // ...
        switch (strtolower($service))
        {
            case static::SERVICE_FACEBOOK:
            case static::SERVICE_GOODLE:
            case static::SERVICE_LINKEDIN:
            case static::SERVICE_TWITTER:
                return response('Not Implemented.', 501);
                break;
        }

        return response('Unsuported Service.', 400);
    }
}
