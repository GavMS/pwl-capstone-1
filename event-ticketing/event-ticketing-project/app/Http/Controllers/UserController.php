<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventCategories;
use App\Models\Accounts;
use App\Models\Events;

class UserController extends Controller
{
    // Halaman dashboard user
    public function dashboard()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        
        // Fetch data for the dashboard layout
        $categories = EventCategories::all();
        $organizers = Accounts::where('role', 'organizer')->latest()->take(8)->get();
        
        // Events untuk hero carousel (max 5 event terbaru yang published)
        $heroEvents = Events::where('status', 'published')
            ->with(['category', 'organizer', 'ticketTypes'])
            ->latest()
            ->take(5)
            ->get();

        // Events untuk featured section (max 4, bisa sama atau beda)
        $featuredEvents = Events::where('status', 'published')
            ->with(['category', 'organizer', 'ticketTypes'])
            ->latest()
            ->take(4)
            ->get();

        $allEventsLite = Events::where('status', 'published')
            ->with(['organizer:id,name'])
            ->select('id_event', 'title', 'date', 'banner', 'organizer_id', 'city')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function($ev) {
                return [
                    'id' => $ev->id_event,
                    'title' => $ev->title,
                    'date_formatted' => $ev->date->translatedFormat('d M \'y'),
                    'city' => $ev->city,
                    'banner' => $ev->banner ? asset('storage/' . $ev->banner) : null,
                    'organizer' => $ev->organizer ? $ev->organizer->name : 'Platform',
                    'url' => '#' // ganti url detail jika sudah ada
                ];
            });

        return view('user.dashboard', compact('user', 'categories', 'organizers', 'heroEvents', 'featuredEvents', 'allEventsLite'));
    }

    // Halaman Explore / Browse Events
    public function explore(Request $request)
    {
        $query = Events::where('status', 'published')->with(['category', 'organizer', 'ticketTypes']);

        // Filter by keyword (Search bar)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Location — exact match on dedicated city column
        if ($request->filled('location')) {
            $query->where('city', $request->location);
        }

        // Filter by Tanggal (date period)
        $today = now()->startOfDay();
        if ($request->filled('tanggal')) {
            switch ($request->tanggal) {
                case 'today':
                    $query->whereDate('date', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('date', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_weekend':
                    $query->whereBetween('date', [now()->nextOrSameWeekday(6)->startOfDay(), now()->nextOrSameWeekday(0)->endOfDay()]);
                    break;
                case 'next_week':
                    $query->whereBetween('date', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]);
                    break;
                case 'next_weekend':
                    $query->whereBetween('date', [now()->addWeek()->nextOrSameWeekday(6)->startOfDay(), now()->addWeek()->nextOrSameWeekday(0)->endOfDay()]);
                    break;
                case 'this_month':
                    $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('custom_date')) {
                        $query->whereDate('date', $request->custom_date);
                    }
                    break;
            }
        }

        // Filter by Waktu (time of day)
        if ($request->filled('waktu')) {
            $waktu = $request->waktu; // 'pagi', 'siang', or 'malam'
            if ($waktu === 'pagi') {
                $query->whereRaw("TIME(date) >= '05:00:00' AND TIME(date) < '10:00:00'");
            } elseif ($waktu === 'siang') {
                $query->whereRaw("TIME(date) >= '10:00:00' AND TIME(date) < '16:00:00'");
            } elseif ($waktu === 'malam') {
                $query->whereRaw("TIME(date) >= '16:00:00' OR TIME(date) < '05:00:00'");
            }
        }

        // Filter by Harga (price range based on MINIMUM ticket price — matches card display)
        if ($request->filled('harga')) {
            $harga = $request->harga;
            $minPriceSub = "(SELECT MIN(price) FROM event_ticket_types WHERE event_id = event.id_event)";

            if ($harga === 'gratis') {
                $query->whereRaw("$minPriceSub = 0");
            } elseif ($harga === 'under100') {
                $query->whereRaw("$minPriceSub > 0 AND $minPriceSub < 100000");
            } elseif ($harga === '100to250') {
                $query->whereRaw("$minPriceSub >= 100000 AND $minPriceSub <= 250000");
            } elseif ($harga === '251to500') {
                $query->whereRaw("$minPriceSub >= 251000 AND $minPriceSub <= 500000");
            } elseif ($harga === 'over500') {
                $query->whereRaw("$minPriceSub > 500000");
            }
        }

        // Filter by Format — exact match on dedicated format column
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Order by latest and paginate
        $events = $query->orderBy('date', 'asc')->paginate(12)->withQueryString();
        
        $categories = EventCategories::all();
        
        // Define popular locations for the GOERS-style filter modal
        $popularLocations = [
            'Jakarta', 'Bandung', 'Bali', 'Surabaya', 'Yogyakarta', 'Tangerang', 'Bekasi', 'Semarang', 'Medan', 'Solo'
        ];

        // If AJAX/live search request, return only the grid partial + count
        if ($request->ajax()) {
            return response()->json([
                'html'  => view('user.partials.explore_grid', compact('events'))->render(),
                'total' => $events->total(),
            ]);
        }

        return view('user.explore', compact('events', 'categories', 'popularLocations'));
    }

    // Halaman Profil Kreator (Organizer)
    public function organizerProfile($id)
    {
        $organizer = Accounts::where('role', 'organizer')->findOrFail($id);
        
        $events = Events::where('organizer_id', $id)
            ->where('status', 'published')
            ->with(['category', 'ticketTypes'])
            ->latest()
            ->paginate(9);

        return view('user.organizer_profile', compact('organizer', 'events'));
    }

    // Halaman Explore Kreator / Organizer
    public function exploreCreators(Request $request)
    {
        $query = Accounts::where('role', 'organizer')->withCount(['events' => function ($q) {
            $q->where('status', 'published');
        }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $organizers = $query->orderBy('events_count', 'desc')->paginate(12)->withQueryString();

        return view('user.explore_creators', compact('organizers'));
    }
}
