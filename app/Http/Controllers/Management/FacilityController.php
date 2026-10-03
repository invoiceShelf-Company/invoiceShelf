<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::withCount('people')->latest()->paginate(12);

        return view('facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'code' => 'required|string|max:50|unique:facilities,code', 'type' => ['required', Rule::in(['company', 'mall', 'store', 'property', 'other'])], 'phone' => 'nullable|string|max:50', 'email' => 'nullable|email|max:255', 'address' => 'nullable|string|max:500', 'description' => 'nullable|string', 'is_active' => 'nullable|boolean']);
        Facility::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('facilities.index')->with('success', 'تمت إضافة المنشأة بنجاح.');
    }

    public function edit(Facility $facility)
    {
        return view('facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'code' => ['required', 'string', 'max:50', Rule::unique('facilities', 'code')->ignore($facility->id)], 'type' => ['required', Rule::in(['company', 'mall', 'store', 'property', 'other'])], 'phone' => 'nullable|string|max:50', 'email' => 'nullable|email|max:255', 'address' => 'nullable|string|max:500', 'description' => 'nullable|string', 'is_active' => 'nullable|boolean']);
        $facility->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('facilities.index')->with('success', 'تم تحديث المنشأة.');
    }

    public function show(Facility $facility)
    {
        $facility->loadCount(['people', 'visitors'])->load(['departments', 'shifts']);

        return view('facilities.show', compact('facility'));
    }
}
