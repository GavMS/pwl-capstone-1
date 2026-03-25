<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    // Halaman dashboard organizer
    public function dashboard()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        return view('organizer.dashboard', compact('user'));
    }
}
