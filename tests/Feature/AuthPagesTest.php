<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_auth_pages_are_accessible(): void
    {
        $this->get('/register')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/admin/login')->assertOk();
    }

    public function test_registration_validation_errors_are_returned(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '456',
            'city' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'password_confirmation', 'city']);
        $response->assertRedirect('/register');
    }

    public function test_admin_dashboard_redirects_to_admin_login_for_guests_and_web_users(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }
}
