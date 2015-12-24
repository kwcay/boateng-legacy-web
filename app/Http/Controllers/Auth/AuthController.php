<?php
/**
 *
 */
namespace App\Http\Controllers\Auth;

use Request;
use Session;
use Validator;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
	use AuthenticatesAndRegistersUsers, ThrottlesLogins {
        // AuthenticatesAndRegistersUsers::postLogin as loginUser;
    }

	/**
	 * Create a new authentication controller instance.
	 */
	public function __construct()
	{
        $this->loginPath = route('auth.login');

        // Let API know if we're coming from the app.

        // Define rediretion paths (used internally).
        switch (Request::input('next'))
        {
            case 'app':
                Session::put('next', 'app');
                $this->redirectPath = 'http://dinkomo.frnk.ca/#/token';
                $this->redirectAfterLogout = 'http://dinkomo.frnk.ca/';
                break;

            case 'app.vagrant':
                Session::put('next', 'app.vagrant');
                $this->redirectPath = 'http://dinkomo.vagrant/#/token';
                $this->redirectAfterLogout = 'http://dinkomo.vagrant/';
                break;

            default:
                $this->redirectAfterLogout = $this->redirectPath = route('home');
        }

        // Enable the guest middleware.
		$this->middleware('guest', ['except' => 'getLogout']);
	}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
