<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $people = Person::with(['facility', 'department', 'shift'])->when($request->filled('q'), fn ($q) => $q->where(fn ($x) => $x->where('full_name', 'like', '%'.$request->q.'%')->orWhere('employee_number', 'like', '%'.$request->q.'%')))->when($request->filled('type'), fn ($q) => $q->where('person_type', $request->type))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->latest()->paginate(15)->withQueryString();

        return view('people.index', compact('people'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $shifts = Shift::where('is_active', true)->orderBy('name')->get();

        return view('people.create', compact('facilities', 'departments', 'shifts'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Person::create($data);

        return redirect()->route('people.index')->with('success', 'تمت إضافة الشخص بنجاح.');
    }

    public function show(Person $person)
    {
        $person->load(['facility', 'department', 'shift'])->loadCount('attendances');
        $recentAttendance = $person->attendances()->latest('attendance_date')->limit(10)->get();

        return view('people.show', compact('person', 'recentAttendance'));
    }

    public function edit(Person $person)
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $shifts = Shift::where('is_active', true)->orderBy('name')->get();

        return view('people.edit', compact('person', 'facilities', 'departments', 'shifts'));
    }

    public function update(Request $request, Person $person)
    {
        $person->update($this->validated($request));

        return redirect()->route('people.show', $person)->with('success', 'تم تحديث بيانات الشخص.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['facility_id' => 'required|exists:facilities,id', 'department_id' => 'nullable|exists:departments,id', 'shift_id' => 'nullable|exists:shifts,id', 'employee_number' => ['nullable', 'string', 'max:100', Rule::unique('people', 'employee_number')->ignore($request->route('person')?->id)], 'full_name' => 'required|string|max:255', 'person_type' => 'required|string|max:50', 'job_title' => 'nullable|string|max:255', 'phone' => 'nullable|string|max:50', 'email' => 'nullable|email|max:255', 'national_id' => 'nullable|string|max:100', 'birth_date' => 'nullable|date', 'gender' => 'nullable|string|max:30', 'join_date' => 'nullable|date', 'status' => 'required|in:active,on_leave,suspended,terminated', 'work_location' => 'nullable|string|max:255', 'floor' => 'nullable|string|max:100', 'room' => 'nullable|string|max:100', 'security_company' => 'nullable|string|max:255', 'security_permit_number' => 'nullable|string|max:100', 'security_permit_expires_at' => 'nullable|date', 'guard_post' => 'nullable|string|max:255', 'notes' => 'nullable|string']);
    }
}
