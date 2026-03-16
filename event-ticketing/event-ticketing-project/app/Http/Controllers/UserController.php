<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Halaman dashboard user
    public function dashboard()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        return view('user.dashboard', compact('user'));
    }
}
