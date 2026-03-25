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
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:accounts,username,' . $user->id],
            'email'    => ['required', 'email', 'max:255', 'unique:accounts,email,' . $user->id],
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
        ]);

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

        // Force-logout by deleting all sessions belonging to this user
        if (!$newStatus) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $action = $newStatus ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.index')
                         ->with('success', "User \"{$user->name}\" has been {$action}.");
    }
}
