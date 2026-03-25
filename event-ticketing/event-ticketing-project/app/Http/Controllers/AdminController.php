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
            'total_users'  => Accounts::count(),
            'total_events' => Events::count(),
            'active_events' => Events::where('status', 'published')->count(),
        ];

        return view('admin.dashboard', compact('user', 'stats'));
    }
}
