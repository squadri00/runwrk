<?php

namespace Tests\Feature;

use App\Rules\AvailableSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReservedPathsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reserved_words_cannot_be_slugs(): void
    {
        foreach (['admin', 'login', 'api', 'dashboard', 'pricing', 'assets'] as $word) {
            $this->assertTrue(Validator::make(['slug' => $word], ['slug' => new AvailableSlug])->fails(), $word);
        }
    }

    public function test_slug_format_and_uniqueness(): void
    {
        $this->assertTrue(Validator::make(['slug' => 'joes-barber'], ['slug' => new AvailableSlug])->passes());
        $this->assertTrue(Validator::make(['slug' => 'Joes Barber'], ['slug' => new AvailableSlug])->fails());
        $this->assertTrue(Validator::make(['slug' => '-bad-'], ['slug' => new AvailableSlug])->fails());

        \App\Models\Business::factory()->create(['slug' => 'taken']);
        $this->assertTrue(Validator::make(['slug' => 'taken'], ['slug' => new AvailableSlug])->fails());
    }

    public function test_every_registered_route_lives_under_a_reserved_first_segment(): void
    {
        $reserved = config('runwrk.reserved_paths');

        foreach (Route::getRoutes() as $route) {
            $first = explode('/', trim($route->uri(), '/'))[0];

            if ($first === '' || str_starts_with($first, '{')) {
                continue;
            }

            $this->assertContains($first, $reserved, "Route /{$route->uri()} uses an unreserved first segment.");
        }
    }
}
