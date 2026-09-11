<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Department;
use App\Models\Profile;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        // Step 1: Verify credentials first
        if (! Auth::attempt($credentials, $remember)) {
            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Step 2: Role-based status gate — enforced on the BACKEND regardless of UI
        $msg = null;

        if ($user->isTrainer()) {
            // --- TRAINER status checks (all four required cases) ---
            if ($user->isPending()) {
                $msg = 'Your trainer account is awaiting admin approval. Please wait for the approval email.';
            } elseif ($user->isRejected()) {
                $msg = 'Your trainer registration request has been rejected. Please contact the administrator for further information.';
            } elseif ($user->isSuspended()) {
                $msg = 'Your trainer account has been suspended. Please contact the administrator.';
            } elseif (! $user->isApproved()) {
                // Safety fallback — unknown status
                $msg = 'Your trainer account is not approved yet. Please contact the administrator.';
            }
            // isApproved() (status = 'approved') → $msg stays null → login allowed
        } else {
            // --- TRAINEE and ADMIN — unaffected, use isActive() only ---
            if (! $user->isActive()) {
                $msg = $user->status === 'suspended'
                    ? 'Your account has been suspended. Please contact the administrator.'
                    : 'Your account is not active. Please contact the administrator.';
            }
        }

        // Step 3: If blocked, fully log out and return to login with error
        if ($msg !== null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => $msg]);
        }

        // Step 4: Allowed — log the event and redirect to role dashboard
        AuditLogService::log('auth.login', $user);

        return $this->redirectBasedOnRole($user);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $departments = Department::orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $status = ($validated['role'] === 'trainer') ? 'pending' : 'active';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $status,
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            // Approval audit fields (null for new registrations)
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ]);

        Profile::create([
            'user_id' => $user->id,
            'job_title' => $validated['role'] === 'trainer' ? 'Certified Disaster Management Trainer' : 'Emergency Response Specialist',
            'location' => 'State Operations Command',
        ]);

        AuditLogService::log('auth.register', $user);

        if ($status === 'pending') {
            return redirect()->route('login')->with(
                'success',
                'Your trainer registration request has been submitted successfully. Your account is currently awaiting admin approval. You will receive an email once your request has been approved.'
            );
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        $token = bin2hex(random_bytes(20));
        session(['password_reset_token' => $token, 'password_reset_email' => $user->email]);

        return redirect()->route('password.reset', ['token' => $token])
            ->with('status', 'Password reset instructions generated. Please set your new password.');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        $email = session('password_reset_email', $request->query('email'));

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->input('email'))->firstOrFail();
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        AuditLogService::log('auth.password_reset', $user);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in.');
    }

    public function showTwoFactor(): View
    {
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);
        $code = $request->input('code');

        if ($code === '123456' || $code === session('2fa_otp')) {
            session(['2fa_passed' => true]);
            $user = Auth::user();

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors(['code' => 'Invalid or expired 2FA passcode. Use demo passcode 123456.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLogService::log('auth.logout', Auth::user());
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been securely signed out.');
    }

    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'trainer' => redirect()->route('trainer.dashboard'),
            default   => redirect()->route('trainee.dashboard'),
        };
    }
}
