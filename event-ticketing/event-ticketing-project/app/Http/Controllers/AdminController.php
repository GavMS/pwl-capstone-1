<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Halaman dashboard admin
    public function dashboard()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        return view('admin.dashboard', compact('user'));
    }
}
