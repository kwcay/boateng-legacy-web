<?php
/**
 *
 */
namespace App\Http\Middleware;

use Closure;

class ApiHeaders
{
    /**
     * Applies some headers to outgoing API responses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set the "Access-Control-Allow-Origin" header.
        if ($request->header('Origin') == 'http://dinkomo.vagrant') {
            $response->header('Access-Control-Allow-Origin', 'http://dinkomo.vagrant', true);
        }
        elseif ($request->header('Origin') == 'http://dinkomo.frnk.ca') {
            $response->header('Access-Control-Allow-Origin', 'http://dinkomo.frnk.ca', true);
        }

        // Set the "Content-Type" header.
        if ($request->method() == 'OPTIONS') {
            // $response->header('Content-Type', 'application/json', true);
            $response->header('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS', true);
        }

        // Current API version.
        $response->header('X-API-Version', '0.1', true);

        return $response;
    }
}
