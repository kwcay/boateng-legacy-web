<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * The home page should not crash.
     */
    public function testHomePage()
    {
        $this->get(route('home'))
            ->assertSuccessful()
            ->assertSee(trans('branding.title'));
    }
}
