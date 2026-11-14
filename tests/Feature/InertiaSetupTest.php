<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaSetupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the home page returns a successful Inertia response.
     */
    public function test_home_page_renders_with_inertia(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('profile')
        );
    }

    /**
     * Test health check endpoint.
     */
    public function test_health_check_endpoint_is_accessible(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }
}

