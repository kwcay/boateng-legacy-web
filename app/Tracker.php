<?php

namespace App;

use KeenIO\Client\KeenIOClient;
use Illuminate\Support\Facades\Storage;

class Tracker
{
    /**
     * @var \KeenIO\Client\KeenIOClient
     */
    private $keen;

    /**
     * @var array
     */
    private $trackedEvents = [];

    /**
     * @param string $projectId
     * @param string $masterKey
     * @param string $writeKey
     */
    public function __construct($projectId = null, $masterKey = null, $writeKey = null)
    {
        if ($projectId && ($masterKey || $writeKey)) {
            $this->keen = KeenIOClient::factory([
                'projectId' => $projectId,
                'masterKey' => $masterKey,
                'writeKey'  => $writeKey,
            ]);
        }
    }

    /**
     * Tracks an event.
     *
     * @param  string $event
     * @param  array  $parameters
     * @return static
     */
    public function addEvent($event, array $parameters = [])
    {
        $this->trackedEvents[$event][] = $parameters;

        return $this;
    }

    /**
     * Saves event data.
     *
     * @return void
     */
    public function persist()
    {
        if (! $this->trackedEvents || ! $this->keen) {
            return;
        }

        try {
            $result = $this->keen->addEvents($this->trackedEvents);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        } catch (\Throwable $t) {
            $result = $t->getMessage();
        }

        // For debugging
        Storage::put('tracker.json', json_encode($result));
    }
}
