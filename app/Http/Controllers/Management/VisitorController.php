<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visitor::with('facility')->latest()->paginate(20);

        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('visitors.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'name' => 'required|string|max:255', 'phone' => 'nullable|string|max:50', 'national_id' => 'nullable|string|max:100', 'company' => 'nullable|string|max:255', 'host_name' => 'nullable|string|max:255', 'purpose' => 'nullable|string|max:255', 'notes' => 'nullable|string']);
        $data['visitor_number'] = 'VIS-'.now()->format('ymdHis').'-'.strtoupper(Str::random(4));
        Visitor::create($data);

        return redirect()->route('visitors.index')->with('success', 'تم تسجيل الزائر.');
    }
}
