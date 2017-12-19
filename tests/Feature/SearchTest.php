<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * The web app should redirect to a localized route.
     */
    public function testLocalizedRoute()
    {
        $this->get('/')->assertStatus(302);
    }

    /**
     * The search page should not crash.
     */
    public function testSearchPage()
    {
        $response = $this->call('GET', route('search'), ['q' => 'hello']);

        $response->assertStatus(200);
    }
}
