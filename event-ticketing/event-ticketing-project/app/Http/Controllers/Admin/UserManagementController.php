<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * List all users with optional search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = Accounts::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user and send credentials via email.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:accounts,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:accounts,email'],
            'role'     => ['required', 'in:admin,organizer,user'],
        ]);

        // Auto-generate a random password
        $plainPassword = Str::random(12);

        $user = Accounts::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'role'      => $request->role,
            'password'  => Hash::make($plainPassword),
            'is_active' => true,
        ]);

        // Send credentials email
        Mail::send('emails.new_account_credentials', [
            'user'          => $user,
            'plainPassword' => $plainPassword,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Your Flowtix Account Credentials');
        });

        return redirect()->route('admin.users.index')
                         ->with('success', "User \"{$user->name}\" created. Credentials sent to {$user->email}.");
    }

    /**
     * Show edit user form (no role field).
     */
    public function edit(Accounts $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user data (all except role).
     */
    public function update(Request $request, Accounts $user)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', 'unique:accounts,username,' . $user->id],
            'email'     => ['required', 'email', 'max:255', 'unique:accounts,email,' . $user->id],
            'is_active' => ['required', 'boolean'],
        ]);

        $newStatus = (bool) $request->is_active;
        $wasActive = $user->is_active;

        // Prevent admin from deactivating themselves
        if (!$newStatus && $user->id === auth()->id()) {
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        $user->update([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'is_active' => $newStatus,
        ]);

        // Force-logout and send notification if newly deactivated
        if (!$newStatus && $wasActive) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            
            Mail::send('emails.account_deactivated', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your Flowtix Account Has Been Deactivated');
            });
        } elseif (!$newStatus) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')
                         ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Activate or deactivate a user.
     * If deactivating, all active sessions for that user are deleted (force-logout).
     */
    public function toggleActive(Accounts $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = !$user->is_active;

        $user->update(['is_active' => $newStatus]);

        // Force-logout and email if deactivated
        if (!$newStatus) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            
            Mail::send('emails.account_deactivated', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your Flowtix Account Has Been Deactivated');
            });
        }

        $action = $newStatus ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.index')
                         ->with('success', "User \"{$user->name}\" has been {$action}.");
    }

    /**
     * Smart Delete — only delete if all safety conditions are met.
     */
    public function destroy(Accounts $user)
    {
        // 1. Cannot delete yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        // 2. Must keep at least 1 admin
        if ($user->role === 'admin') {
            $adminCount = Accounts::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                                 ->with('error', "Cannot delete \"{$user->name}\". There must be at least one admin account remaining.");
            }
        }

        // 3. Cannot delete if user has transactions
        $hasTransactions = DB::table('transaction')->where('accounts_id', $user->id)->exists();
        if ($hasTransactions) {
            return redirect()->route('admin.users.index')
                             ->with('error', "Cannot delete \"{$user->name}\". This account has existing transactions.");
        }

        // 4. Cannot delete if user has issued tickets
        $hasTickets = DB::table('issued_tickets')->where('user_id', $user->id)->exists();
        if ($hasTickets) {
            return redirect()->route('admin.users.index')
                             ->with('error', "Cannot delete \"{$user->name}\". This account has issued e-tickets.");
        }

        // 5. Cannot delete if organizer has created events
        if ($user->role === 'organizer') {
            $hasEvents = DB::table('event')->where('organizer_id', $user->id)->exists();
            if ($hasEvents) {
                return redirect()->route('admin.users.index')
                                 ->with('error', "Cannot delete \"{$user->name}\". This organizer account has created events.");
            }
        }

        // Safe to delete — clean up waiting_lists first (non-critical data)
        DB::table('waiting_lists')->where('accounts_id', $user->id)->delete();

        // Force-logout the user being deleted
        DB::table('sessions')->where('user_id', $user->id)->delete();

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', "User \"{$name}\" has been permanently deleted.");
    }
}
