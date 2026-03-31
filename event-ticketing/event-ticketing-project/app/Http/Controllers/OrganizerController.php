<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;

class OrganizerController extends Controller
{
    // Halaman dashboard organizer
    // Halaman dashboard organizer dengan filter status
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', 'active');
        
        $stats = [
            'total_events'  => Events::where('organizer_id', auth()->id())->count(),
            'active_events' => Events::where('organizer_id', auth()->id())->where('status', 'published')->count(),
            'draft_events'  => Events::where('organizer_id', auth()->id())->where('status', 'draft')->count(),
        ];

        // Query dasar
        $query = Events::where('organizer_id', auth()->id());

        // Logika filter
        if ($status === 'draft') {
            $query->where('status', 'draft');
        } elseif ($status === 'past') {
            $query->where('date', '<', now());
        } else {
            // Default: Active Published
            $query->where('status', 'published')->where('date', '>=', now());
        }

        $events = $query->latest()->take(5)->get();

        return view('organizer.dashboard', compact('user', 'stats', 'events', 'status'));
    }
}
