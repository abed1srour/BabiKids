<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    // Generic listing with optional filters
    public function index(Request $request)
    {
        $q = Attendance::with(['child','recorder']);
        if ($cid = $request->query('child_id')) $q->where('child_id', $cid);
        if ($from = $request->query('from'))    $q->where('date','>=',$from);
        if ($to   = $request->query('to'))      $q->where('date','<=',$to);
        return response()->json($q->orderBy('date','desc')->paginate(20));
    }

    // List for a specific child
    public function indexByChild(Child $child)
    {
        return response()->json(
            $child->attendance()->with('recorder')->orderBy('date','desc')->paginate(20)
        );
    }

    // Create under /children/{child}/attendance
    public function storeForChild(Request $request, Child $child)
    {
        $data = $request->validate([
            'date'           => ['required','date'],
            'status'         => ['required', Rule::in(['present','absent','late','excused'])],
            'check_in_time'  => ['nullable','date_format:H:i'],
            'check_out_time' => ['nullable','date_format:H:i'],
            'notes'          => ['nullable','string'],
            'recorded_by'    => ['required','exists:staff,id'],
        ]);

        // unique per (child,date)
        if (Attendance::where('child_id',$child->id)->where('date',$data['date'])->exists()) {
            return response()->json(['message'=>'Attendance already exists for this date'], 422);
        }

        $data['child_id'] = $child->id;
        $attendance = Attendance::create($data);
        return response()->json($attendance->load(['child','recorder']), 201);
    }

    // Generic store under /attendance (optional)
    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id'       => ['required','exists:children,id'],
            'date'           => ['required','date'],
            'status'         => ['required', Rule::in(['present','absent','late','excused'])],
            'check_in_time'  => ['nullable','date_format:H:i'],
            'check_out_time' => ['nullable','date_format:H:i'],
            'notes'          => ['nullable','string'],
            'recorded_by'    => ['required','exists:staff,id'],
        ]);

        if (Attendance::where('child_id',$data['child_id'])->where('date',$data['date'])->exists()) {
            return response()->json(['message'=>'Attendance already exists for this date'], 422);
        }

        $attendance = Attendance::create($data);
        return response()->json($attendance->load(['child','recorder']), 201);
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load(['child','recorder']));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status'         => ['sometimes', Rule::in(['present','absent','late','excused'])],
            'check_in_time'  => ['nullable','date_format:H:i'],
            'check_out_time' => ['nullable','date_format:H:i'],
            'notes'          => ['nullable','string'],
            'recorded_by'    => ['sometimes','exists:staff,id'],
        ]);

        $attendance->update($data);
        return response()->json($attendance->load(['child','recorder']));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->noContent();
    }
}
