<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

describe('User Registration', function () {
  it('shows registration form', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertViewIs('auth.register');
  });

  it('allows user to register with valid data', function () {
    Event::fake();

    $userData = [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
      'name' => 'John Doe',
      'email' => 'john@example.com',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
    expect($user->role)->toBe('user'); // Default role

    Event::assertDispatched(Registered::class);
  });

  it('validates required fields during registration', function () {
    $response = $this->post(route('register'), []);

    $response->assertSessionHasErrors([
      'name',
      'email',
      'password',
    ]);
  });

  it('validates email format during registration', function () {
    $userData = [
      'name' => 'John Doe',
      'email' => 'invalid-email',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertSessionHasErrors(['email']);
  });

  it('validates password confirmation during registration', function () {
    $userData = [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => 'password123',
      'password_confirmation' => 'different-password',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertSessionHasErrors(['password']);
  });

  it('prevents duplicate email registration', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $userData = [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertSessionHasErrors(['email']);
  });

  it('validates minimum password length', function () {
    $userData = [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => '123',
      'password_confirmation' => '123',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertSessionHasErrors(['password']);
  });

  it('redirects authenticated users away from registration', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
      ->get(route('register'));

    $response->assertRedirect(route('dashboard'));
  });
});

describe('User Login', function () {
  beforeEach(function () {
    $this->user = User::factory()->create([
      'email' => 'john@example.com',
      'password' => Hash::make('password123'),
    ]);
  });

  it('shows login form', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertViewIs('auth.login');
  });

  it('allows user to login with valid credentials', function () {
    $response = $this->post(route('login'), [
      'email' => 'john@example.com',
      'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($this->user);
  });

  it('rejects login with invalid email', function () {
    $response = $this->post(route('login'), [
      'email' => 'wrong@example.com',
      'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
  });

  it('rejects login with invalid password', function () {
    $response = $this->post(route('login'), [
      'email' => 'john@example.com',
      'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
  });

  it('validates required fields during login', function () {
    $response = $this->post(route('login'), []);

    $response->assertSessionHasErrors([
      'email',
      'password',
    ]);
  });

  it('remembers user when remember me is checked', function () {
    $response = $this->post(route('login'), [
      'email' => 'john@example.com',
      'password' => 'password123',
      'remember' => true,
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($this->user);

    // Check that remember token is set
    $this->user->refresh();
    expect($this->user->remember_token)->not->toBeNull();
  });

  it('redirects authenticated users away from login', function () {
    $response = $this->actingAs($this->user)
      ->get(route('login'));

    $response->assertRedirect(route('dashboard'));
  });

  it('throttles login attempts', function () {
    // Make multiple failed login attempts
    for ($i = 0; $i < 6; $i++) {
      $this->post(route('login'), [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
      ]);
    }

    // Next attempt should be throttled
    $response = $this->post(route('login'), [
      'email' => 'john@example.com',
      'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect($response->getContent())->toContain('Too many login attempts');
  });
});

describe('User Logout', function () {
  it('allows authenticated user to logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
      ->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
  });

  it('redirects unauthenticated users', function () {
    $response = $this->post(route('logout'));

    $response->assertRedirect(route('login'));
  });
});

describe('Password Reset', function () {
  beforeEach(function () {
    $this->user = User::factory()->create([
      'email' => 'john@example.com',
    ]);
  });

  it('shows forgot password form', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
    $response->assertViewIs('auth.forgot-password');
  });

  it('sends password reset link for valid email', function () {
    $response = $this->post(route('password.email'), [
      'email' => 'john@example.com',
    ]);

    $response->assertSessionHas('status');
  });

  it('validates email format for password reset', function () {
    $response = $this->post(route('password.email'), [
      'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors(['email']);
  });

  it('handles non-existent email gracefully', function () {
    $response = $this->post(route('password.email'), [
      'email' => 'nonexistent@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
  });

  it('shows password reset form with valid token', function () {
    $token = 'valid-reset-token';

    $response = $this->get(route('password.reset', ['token' => $token]));

    $response->assertOk();
    $response->assertViewIs('auth.reset-password');
    $response->assertViewHas('token', $token);
  });

  it('validates password reset form', function () {
    $response = $this->post(route('password.store'), [
      'token' => 'some-token',
      'email' => 'john@example.com',
      'password' => 'newpassword',
      'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);
  });
});

describe('Email Verification', function () {
  it('shows email verification notice for unverified users', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
      ->get(route('verification.notice'));

    $response->assertOk();
    $response->assertViewIs('auth.verify-email');
  });

  it('redirects verified users away from verification notice', function () {
    $user = User::factory()->create(); // Verified by default

    $response = $this->actingAs($user)
      ->get(route('verification.notice'));

    $response->assertRedirect(route('dashboard'));
  });

  it('allows resending verification email', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
      ->post(route('verification.send'));

    $response->assertRedirect();
    $response->assertSessionHas('status');
  });

  it('prevents verified users from resending verification email', function () {
    $user = User::factory()->create(); // Verified by default

    $response = $this->actingAs($user)
      ->post(route('verification.send'));

    $response->assertRedirect(route('dashboard'));
  });
});

describe('Protected Routes', function () {
  it('redirects unauthenticated users to login', function () {
    $protectedRoutes = [
      'dashboard',
      'items.create',
      'items.my-items',
      'profile.edit',
    ];

    foreach ($protectedRoutes as $route) {
      $response = $this->get(route($route));
      $response->assertRedirect(route('login'));
    }
  });

  it('allows authenticated users to access protected routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
      ->get(route('dashboard'));

    $response->assertOk();
  });

  it('redirects unverified users to verification notice', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
      ->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
  });

  it('allows verified users to access dashboard', function () {
    $user = User::factory()->create(); // Verified by default

    $response = $this->actingAs($user)
      ->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewIs('dashboard');
  });
});

describe('Admin Authentication', function () {
  it('prevents non-admin users from accessing admin routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    $adminRoutes = [
      'admin.dashboard',
      'admin.pending-items',
      'admin.statistics',
    ];

    foreach ($adminRoutes as $route) {
      $response = $this->actingAs($user)
        ->get(route($route));

      $response->assertForbidden();
    }
  });

  it('allows admin users to access admin routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
      ->get(route('admin.dashboard'));

    $response->assertOk();
  });

  it('redirects unauthenticated users from admin routes to login', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
  });
});

describe('Session Management', function () {
  it('maintains session across requests', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->get(route('dashboard'))
      ->assertOk();

    // Make another request - should still be authenticated
    $response = $this->get(route('items.create'));
    $response->assertOk();
    $this->assertAuthenticatedAs($user);
  });

  it('clears session on logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
      ->post(route('logout'));

    // Should no longer be authenticated
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
  });

  it('handles concurrent sessions', function () {
    $user = User::factory()->create();

    // Simulate multiple browser sessions
    $this->actingAs($user)
      ->get(route('dashboard'))
      ->assertOk();

    // Should still work in another "session"
    $this->actingAs($user)
      ->get(route('items.create'))
      ->assertOk();
  });
});
