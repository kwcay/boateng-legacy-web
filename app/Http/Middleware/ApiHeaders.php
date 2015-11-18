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

        // Respond to pre-flighted requests.
        if ($request->method() == 'OPTIONS')
        {
            // Allow the requested method.
            if (in_array($request->header('Access-Control-Request-Method'), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $response->header('Access-Control-Allow-Methods', $request->header('Access-Control-Request-Method'), true);
            }

            // Allow requested header.
            if ($request->header('Access-Control-Request-Headers')) {
                $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'), true);
            }
        }

        // Current API version.
        $response->header('X-API-Version', '0.1', true);

        return $response;
    }
}
