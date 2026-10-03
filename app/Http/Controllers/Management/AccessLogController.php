<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessLogController extends Controller
{
    public function index()
    {
        $logs = AccessLog::with(['facility', 'person', 'visitor', 'recorder'])->latest('logged_at')->paginate(25);

        return view('access-logs.index', compact('logs'));
    }

    public function create()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $people = Person::where('status', 'active')->orderBy('full_name')->get();
        $visitors = Visitor::latest()->limit(100)->get();

        return view('access-logs.create', compact('facilities', 'people', 'visitors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['facility_id' => 'required|exists:facilities,id', 'person_id' => 'nullable|exists:people,id', 'visitor_id' => 'nullable|exists:visitors,id', 'gate' => 'nullable|string|max:100', 'action' => ['required', Rule::in(['entry', 'exit'])], 'logged_at' => 'required|date', 'notes' => 'nullable|string']);
        if (! $data['person_id'] && ! $data['visitor_id']) {
            return back()->withInput()->withErrors(['person_id' => 'اختر موظفًا/عاملًا/حارسًا أو زائرًا.']);
        } $data['recorded_by'] = $request->user()->id;
        AccessLog::create($data);

        return redirect()->route('access-logs.index')->with('success', 'تم تسجيل حركة الدخول/الخروج.');
    }
}
