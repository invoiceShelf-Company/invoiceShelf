<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $attendances = Attendance::with(['person.facility', 'person.shift'])->whereDate('attendance_date', $date)->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->when($request->filled('person_id'), fn ($q) => $q->where('person_id', $request->person_id))->latest()->paginate(20)->withQueryString();
        $stats = ['present' => Attendance::whereDate('attendance_date', $date)->where('status', 'present')->count(), 'late' => Attendance::whereDate('attendance_date', $date)->where('status', 'late')->count(), 'absent' => Attendance::whereDate('attendance_date', $date)->where('status', 'absent')->count(), 'leave' => Attendance::whereDate('attendance_date', $date)->where('status', 'leave')->count(), 'overtime' => Attendance::whereDate('attendance_date', $date)->sum('overtime_minutes')];

        return view('attendance.index', compact('attendances', 'date', 'stats'));
    }

    public function create()
    {
        $people = Person::with('shift')->where('status', 'active')->orderBy('full_name')->get();

        return view('attendance.create', compact('people'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['person_id' => 'required|exists:people,id', 'attendance_date' => 'required|date', 'check_in' => 'nullable', 'check_out' => 'nullable', 'status' => ['required', Rule::in(['present', 'late', 'absent', 'leave', 'half_day', 'holiday'])], 'notes' => 'nullable|string']);
        $person = Person::with('shift')->findOrFail($data['person_id']);
        $calc = $this->calculate($person, $data['attendance_date'], $data['check_in'] ?? null, $data['check_out'] ?? null, $data['status']);
        Attendance::updateOrCreate(['person_id' => $person->id, 'attendance_date' => $data['attendance_date']], array_merge($data, $calc));

        return redirect()->route('attendance.index', ['date' => $data['attendance_date']])->with('success', 'تم تسجيل الحضور وحساب الساعات والتأخير والإضافي.');
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load('person.shift');
        $people = Person::where('status', 'active')->orderBy('full_name')->get();

        return view('attendance.edit', compact('attendance', 'people'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate(['person_id' => 'required|exists:people,id', 'attendance_date' => 'required|date', 'check_in' => 'nullable', 'check_out' => 'nullable', 'status' => ['required', Rule::in(['present', 'late', 'absent', 'leave', 'half_day', 'holiday'])], 'notes' => 'nullable|string']);
        $person = Person::with('shift')->findOrFail($data['person_id']);
        $attendance->update(array_merge($data, $this->calculate($person, $data['attendance_date'], $data['check_in'] ?? null, $data['check_out'] ?? null, $data['status'])));

        return redirect()->route('attendance.index', ['date' => $data['attendance_date']])->with('success', 'تم تحديث سجل الحضور.');
    }

    private function calculate(Person $person, string $date, ?string $in, ?string $out, string $status): array
    {
        $shift = $person->shift;
        $scheduledStart = $shift?->start_time;
        $scheduledEnd = $shift?->end_time;
        if (! $in || ! $out || in_array($status, ['absent', 'leave', 'holiday'])) {
            return ['scheduled_start' => $scheduledStart, 'scheduled_end' => $scheduledEnd, 'worked_minutes' => 0, 'late_minutes' => 0, 'early_leave_minutes' => 0, 'overtime_minutes' => 0];
        } $start = Carbon::parse($date.' '.$in);
        $end = Carbon::parse($date.' '.$out);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        } $worked = max(0, $start->diffInMinutes($end) - ($shift?->break_minutes ?? 0));
        $late = 0;
        $early = 0;
        $overtime = 0;
        if ($scheduledStart) {
            $scheduled = Carbon::parse($date.' '.$scheduledStart);
            $late = max(0, $scheduled->diffInMinutes($start, false) - ($shift?->grace_minutes ?? 0));
            if ($late < 0) {
                $late = 0;
            }
        } if ($scheduledEnd) {
            $scheduledEndAt = Carbon::parse($date.' '.$scheduledEnd);
            if ($shift?->is_overnight) {
                $scheduledEndAt->addDay();
            }$early = max(0, $end->diffInMinutes($scheduledEndAt, false));
            if ($early < 0) {
                $early = 0;
            }$overtime = max(0, $scheduledEndAt->diffInMinutes($end, false));
            if ($overtime < 0) {
                $overtime = 0;
            }
        }

return ['scheduled_start' => $scheduledStart, 'scheduled_end' => $scheduledEnd, 'worked_minutes' => $worked, 'late_minutes' => $late, 'early_leave_minutes' => $early, 'overtime_minutes' => $overtime];
    }
}
