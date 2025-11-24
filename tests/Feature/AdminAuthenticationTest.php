<?php

use App\Models\Admin;
use App\Models\User;

describe('Admin Authentication System', function () {
  it('displays admin login form', function () {
    $response = $this->get(route('admin.login'));

    $response->assertOk();
    $response->assertViewIs('admin.auth.login');
    $response->assertSee('Admin Login');
  });

  it('authenticates admin with valid credentials', function () {
    $admin = Admin::factory()->create([
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('admin.login'), [
      'email' => 'admin@example.com',
      'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin, 'admin');
  });

  it('rejects admin login with invalid credentials', function () {
    Admin::factory()->create([
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('admin.login'), [
      'email' => 'admin@example.com',
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest('admin');
  });

  it('logs out admin and invalidates session', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin');
    $this->assertAuthenticatedAs($admin, 'admin');

    $response = $this->post(route('admin.logout'));

    $response->assertRedirect(route('admin.login'));
    $this->assertGuest('admin');
  });

  it('protects admin routes from unauthenticated access', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
    $response->assertSessionHas('error');
  });

  it('protects admin routes from regular user access', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'web')
      ->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
  });

  it('maintains separate sessions for admin and user', function () {
    $admin = Admin::factory()->create();
    $user = User::factory()->create();

    // Login as user
    $this->actingAs($user, 'web');
    $this->assertAuthenticatedAs($user, 'web');

    // Login as admin in a different session context
    $this->actingAs($admin, 'admin');
    $this->assertAuthenticatedAs($admin, 'admin');

    // Both should be authenticated in their respective guards
    $this->assertTrue(auth()->guard('web')->check());
    $this->assertTrue(auth()->guard('admin')->check());
  });

  it('remembers admin when remember me is checked', function () {
    $admin = Admin::factory()->create([
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('admin.login'), [
      'email' => 'admin@example.com',
      'password' => 'password',
      'remember' => true,
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin, 'admin');

    // Check that remember token was set
    $admin->refresh();
    expect($admin->remember_token)->not->toBeNull();
  });
});
