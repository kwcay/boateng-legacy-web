<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * The search page should not crash.
     */
    public function testSearchPage()
    {
        $this->call('GET', route('search'), ['q' => 'hello'])
            ->assertSuccessful();
    }
}
