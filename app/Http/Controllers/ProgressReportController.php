<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use Illuminate\Http\Request;

class ProgressReportController extends Controller
{
    public function index(Request $request)
    {
        $q = ProgressReport::with(['child','author']);
        if ($cid = $request->query('child_id')) $q->where('child_id', $cid);
        return response()->json($q->orderBy('report_date','desc')->paginate(15));
    }

    public function show(ProgressReport $progressReport)
    {
        return response()->json($progressReport->load(['child','author']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id'        => ['required','exists:children,id'],
            'created_by'      => ['required','exists:staff,id'],
            'report_date'     => ['required','date'],
            'summary'         => ['nullable','string'],
            'milestone_scores'=> ['nullable','array'],
        ]);

        $pr = ProgressReport::create($data);
        return response()->json($pr->load(['child','author']), 201);
    }

    public function update(Request $request, ProgressReport $progressReport)
    {
        $data = $request->validate([
            'report_date'     => ['sometimes','date'],
            'summary'         => ['nullable','string'],
            'milestone_scores'=> ['nullable','array'],
        ]);

        $progressReport->update($data);
        return response()->json($progressReport->load(['child','author']));
    }

    public function destroy(ProgressReport $progressReport)
    {
        $progressReport->delete();
        return response()->noContent();
    }
}
