<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('facility')->withCount('people')->latest()->paginate(15);

        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('shifts.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'name' => 'required|string|max:100', 'start_time' => 'required', 'end_time' => 'required', 'grace_minutes' => 'required|integer|min:0|max:180', 'break_minutes' => 'required|integer|min:0|max:300', 'is_overnight' => 'nullable|boolean']);
        Shift::create($data + ['is_active' => true, 'is_overnight' => $request->boolean('is_overnight')]);

        return redirect()->route('shifts.index')->with('success', 'تمت إضافة الوردية.');
    }

    public function edit(Shift $shift)
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('shifts.edit', compact('shift', 'facilities'));
    }

    public function update(Request $request, Shift $shift)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'name' => 'required|string|max:100', 'start_time' => 'required', 'end_time' => 'required', 'grace_minutes' => 'required|integer|min:0|max:180', 'break_minutes' => 'required|integer|min:0|max:300', 'is_overnight' => 'nullable|boolean']);
        $shift->update($data + ['is_overnight' => $request->boolean('is_overnight')]);

        return redirect()->route('shifts.index')->with('success', 'تم تحديث الوردية.');
    }
}
