<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Events;

class AdminController extends Controller
{
    // Halaman dashboard admin
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_users'   => Accounts::count(),
            'total_events'  => Events::count(),
            'active_events' => Events::where('status', 'published')->count(),
        ];

        $new_users     = Accounts::latest()->take(5)->get();
        $recent_events = Events::with('category', 'organizer')->latest()->take(5)->get();

        return view('admin.dashboard', compact('user', 'stats', 'new_users', 'recent_events'));
    }
}
