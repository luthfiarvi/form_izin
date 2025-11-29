<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login_when_hitting_root(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_to_create_form(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('izin.create'));
    }
}
