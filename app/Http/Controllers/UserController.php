<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 *
 */
class UserController extends Controller {

    
    /**
     * Login form
     */
    public function __TO_BE_RE_WORKED__showLoginForm()
    {
        // Check if user is already logged in
        if (false) {
        
        }
        
        return view('forms.login');
    }
}
