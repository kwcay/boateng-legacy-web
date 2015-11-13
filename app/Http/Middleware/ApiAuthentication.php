<?php
/**
 *
 */
namespace App\Http\Middleware;

use Closure;

class ApiAuthentication
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
        // Authenticate app. For now, we only check the origin header.
        if (!in_array($request->header('Origin'), ['http://dinkomo.vagrant', 'http://dinkomo.frnk.ca']))
        {
            // Allow dev access.
            if (!$request->has('dev'))
            {
                return response('Unauthorized.', 401);
            }
        }

        return $next($request);
    }
}
