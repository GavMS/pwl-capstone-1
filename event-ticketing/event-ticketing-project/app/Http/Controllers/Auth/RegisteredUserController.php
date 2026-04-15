<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\Accounts;
use App\Models\OtpVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Validate data, generate OTP, send to email, redirect to OTP verify page.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . Accounts::class],
            'email'    => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:' . Accounts::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.email' => 'The email address does not appear to be valid or does not exist.',
        ]);

        // Store registration data in session temporarily
        $request->session()->put('register_pending', [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Delete any previous OTPs for this email
        OtpVerification::where('email', $request->email)->delete();

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database
        OtpVerification::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP email — if delivery fails, the email is likely invalid
        try {
            Mail::to($request->email)->send(new OtpVerificationMail($otp, $request->name));
        } catch (\Exception $e) {
            // Clean up OTP record and session, then redirect back with error
            OtpVerification::where('email', $request->email)->delete();
            $request->session()->forget('register_pending');

            return back()->withInput()->withErrors([
                'email' => 'We could not send a verification code to this email address. Please make sure the email is valid and try again.',
            ]);
        }

        return redirect()->route('register.otp')->with('otp_email', $request->email);
    }

    /**
     * Show the OTP verification page.
     */
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        // If no pending registration in session, redirect back to register
        if (!$request->session()->has('register_pending')) {
            return redirect()->route('register')->withErrors(['email' => 'Please complete the registration form first.']);
        }

        $email = $request->session()->get('register_pending')['email'];

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify the OTP and create the account.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Check session
        $pending = $request->session()->get('register_pending');
        if (!$pending) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
        }

        $email = $pending['email'];

        // Find the latest valid OTP for this email
        $otpRecord = OtpVerification::where('email', $email)
            ->where('is_used', false)
            ->latest()
            ->first();

        // OTP not found
        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please request a new one.']);
        }

        // OTP expired
        if ($otpRecord->expires_at->isPast()) {
            return back()->withErrors(['otp' => 'This OTP has expired. Please request a new one.']);
        }

        // OTP mismatch
        if ($otpRecord->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'The OTP you entered is incorrect.']);
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        // Create the user account
        $user = Accounts::create([
            'name'     => $pending['name'],
            'username' => $pending['username'],
            'email'    => $pending['email'],
            'password' => $pending['password'],
            'role'     => 'user',
        ]);

        // Clear pending session data
        $request->session()->forget('register_pending');

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'organizer') {
            return redirect(route('organizer.dashboard', absolute: false));
        }

        return redirect(route('user.dashboard', absolute: false));
    }

    /**
     * Resend OTP to the email stored in session.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('register_pending');
        if (!$pending) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
        }

        $email = $pending['email'];

        // Delete previous OTPs
        OtpVerification::where('email', $email)->delete();

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->send(new OtpVerificationMail($otp, $pending['name']));

        return back()->with('resent', true);
    }
}
