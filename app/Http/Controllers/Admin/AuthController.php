<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
  /**
   * Display the admin login form.
   */
  public function showLoginForm(): View
  {
    return view('admin.auth.login');
  }

  /**
   * Handle an incoming admin authentication request.
   */
  public function login(Request $request): RedirectResponse
  {
    $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required', 'string'],
    ]);

    $credentials = $request->only('email', 'password');
    $remember = $request->boolean('remember');

    if (Auth::guard('admin')->attempt($credentials, $remember)) {
      $request->session()->regenerate();

      return redirect()->intended(route('admin.dashboard'));
    }

    throw ValidationException::withMessages([
      'email' => __('The provided credentials do not match our records.'),
    ]);
  }

  /**
   * Destroy an authenticated admin session.
   */
  public function logout(Request $request): RedirectResponse
  {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
  }
}
