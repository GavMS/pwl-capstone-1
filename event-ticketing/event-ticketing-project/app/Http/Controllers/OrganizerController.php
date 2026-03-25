<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;

class OrganizerController extends Controller
{
    // Halaman dashboard organizer
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_events'  => Events::count(),
            'active_events' => Events::where('status', 'published')->count(),
            'draft_events'  => Events::where('status', 'draft')->count(),
        ];

        // Ambil 5 event terbaru
        $events = Events::latest()->take(5)->get();

        return view('organizer.dashboard', compact('user', 'stats', 'events'));
    }
}
