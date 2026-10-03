<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Facility;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('facility')->withCount('people')->latest()->paginate(20);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('departments.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'description' => 'nullable|string']);
        Department::create($data + ['is_active' => true]);

        return redirect()->route('departments.index')->with('success', 'تمت إضافة القسم.');
    }

    public function edit(Department $department)
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('departments.edit', compact('department', 'facilities'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'description' => 'nullable|string']);
        $department->update($data);

        return redirect()->route('departments.index')->with('success', 'تم تحديث القسم.');
    }
}
