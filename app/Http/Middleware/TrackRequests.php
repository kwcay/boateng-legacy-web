<?php

namespace App\Http\Middleware;

use Closure;
use App\Tracker;

class TrackRequests
{
    /**
     * @var \App\Tracker
     */
    private $tracker;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @param \App\Tracker $tracker
     */
    public function __construct(Tracker $tracker)
    {
        $this->tracker   = $tracker;
        $this->startTime = microtime(true);
    }

    /**
     * Track every incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Gather defaults to track every request
        $path = substr($request->path(), strpos($request->path(), '/'));
        $params = $request->toArray();
        $fingerprint = null;

        // Try to retrieve route information
        if ($request->route()) {
            $params += $request->route()->parameters();
            $fingerprint = $request->fingerprint();
        }

        $this->tracker->addEvent('request', [
            'method'        => $request->method(),
            'host'          => $request->root(),
            'path'          => $path,
            'params'        => $params,
            'fingerprint'   => $fingerprint,
            'ip'            => $request->ip(),
            'user-agent'    => $request->headers->get('user-agent'),
            'response'      => [
                'time' => microtime(true) - $this->startTime,
                'code' => $response->getStatusCode(),
            ],

            // Keen add-ons
            'keen' => [
                'addons' => [
                    [
                        'name'  => 'keen:ip_to_geo',
                        'output' => 'geo_data',
                        'input' => [
                            'ip' => $request->ip(),
                        ],
                    ]
                ]
            ]
        ]);

        return $response;
    }

    /**
     * @param  \Illuminate\Http\Request     $request
     * @param  \Illuminate\Http\Response    $response
     */
    public function terminate($request, $response)
    {
        // Store the session data
        $this->tracker->persist();
    }
}
