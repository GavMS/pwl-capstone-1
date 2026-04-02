<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticketTypes = TicketType::latest()->paginate(10);
        return view('admin.ticket-types.index', compact('ticketTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ticket-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:ticket_types,name',
            'description' => 'required|string',
        ]);

        TicketType::create($validated);

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'ticket type created successfully!');
    }

    /**
     * Display the specified resource. — (Usually handled in index/modal)
     */
    public function show(TicketType $ticketType)
    {
        return redirect()->route('admin.ticket-types.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketType $ticketType)
    {
        return view('admin.ticket-types.edit', compact('ticketType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:ticket_types,name,' . $ticketType->id_ticket_type . ',id_ticket_type',
            'description' => 'required|string',
        ]);

        $ticketType->update($validated);

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'ticket type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketType $ticketType)
    {
        // Add check if ticket types are used in events
        if ($ticketType->events()->count() > 0) {
            return back()->with('error', 'cannot delete ticket type, it is already assigned to events!');
        }

        $ticketType->delete();

        return redirect()->route('admin.ticket-types.index')
            ->with('success', 'ticket type deleted successfully!');
    }
}
