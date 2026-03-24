<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        $user = \App\Models\Accounts::where('username', $request->username)->first();

        if (! $user) {
            return back()->withInput($request->only('username'))
                         ->withErrors(['username' => 'No account found with that username.']);
        }

        // Generate the masked email (only show first 3 letters)
        $emailParts = explode('@', $user->email);
        $localPart = $emailParts[0];
        $domain = $emailParts[1] ?? 'gmail.com';
        
        if (strlen($localPart) > 3) {
            $maskedLocal = substr($localPart, 0, 3) . str_repeat('*', strlen($localPart) - 3);
        } else {
            $maskedLocal = substr($localPart, 0, 1) . str_repeat('*', strlen($localPart) - 1);
        }
        
        $maskedEmail = $maskedLocal . '@' . $domain;

        // If not confirmed yet, return back to ask for confirmation
        if (! $request->has('confirm')) {
            return back()->with('confirm_email', $maskedEmail)
                         ->with('username', $user->username);
        }

        // Send the password reset link
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', "Reset link sent to {$maskedEmail}")
                    : back()->withErrors(['username' => __($status)]);
    }
}
