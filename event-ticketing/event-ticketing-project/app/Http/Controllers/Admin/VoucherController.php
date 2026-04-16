<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Events;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with('event')->latest()->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $events = Events::orderBy('title', 'asc')->get();
        return view('admin.vouchers.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code|alpha_dash',
            'description' => 'nullable|string|max:255',
            'discount_percent' => 'required|integer|min:1|max:100',
            'scope' => 'required|in:global,event',
            'event_id' => 'nullable|required_if:scope,event|exists:event,id_event',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['scope']);
        $data['min_purchase'] = $request->input('min_purchase') ?: 0;
        $data['max_discount'] = $request->input('max_discount') ?: null;
        
        if ($request->scope === 'global') {
            $data['event_id'] = null;
        }

        // Auto uppercase code
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created successfully.');
    }

    public function edit(Voucher $voucher)
    {
        $events = Events::orderBy('title', 'asc')->get();
        return view('admin.vouchers.edit', compact('voucher', 'events'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|alpha_dash|unique:vouchers,code,' . $voucher->id,
            'description' => 'nullable|string|max:255',
            'discount_percent' => 'required|integer|min:1|max:100',
            'scope' => 'required|in:global,event',
            'event_id' => 'nullable|required_if:scope,event|exists:event,id_event',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['scope']);
        $data['min_purchase'] = $request->input('min_purchase') ?: 0;
        $data['max_discount'] = $request->input('max_discount') ?: null;

        if ($request->scope === 'global') {
            $data['event_id'] = null;
        }

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted successfully.');
    }

    public function toggleActive(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);
        return back()->with('success', 'Voucher status toggled.');
    }
}
