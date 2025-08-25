<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $q = Activity::with(['group','creator']);
        if ($gid = $request->query('group_id')) $q->where('group_id', $gid);
        if ($from = $request->query('from'))     $q->where('scheduled_at','>=',$from);
        if ($to   = $request->query('to'))       $q->where('scheduled_at','<=',$to);
        return response()->json($q->orderBy('scheduled_at','desc')->paginate(15));
    }

    public function show(Activity $activity)
    {
        return response()->json($activity->load(['group','creator','children']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id'    => ['nullable','exists:groups,id'],
            'created_by'  => ['required','exists:staff,id'],
            'title'       => ['required','string','max:150'],
            'description' => ['nullable','string'],
            'scheduled_at'=> ['nullable','date'],
            // Optional attach children
            'children'    => ['nullable','array'],
            'children.*.id'     => ['required_with:children','exists:children,id'],
            'children.*.status' => ['nullable','in:planned,completed,missed'],
            'children.*.notes'  => ['nullable','string'],
            'children.*.recorded_by' => ['nullable','exists:staff,id'],
        ]);

        $children = $data['children'] ?? null;
        unset($data['children']);

        $activity = Activity::create($data);

        if ($children) {
            $attach = [];
            foreach ($children as $c) {
                $attach[$c['id']] = [
                    'status'      => $c['status'] ?? 'planned',
                    'notes'       => $c['notes'] ?? null,
                    'recorded_by' => $c['recorded_by'] ?? null,
                ];
            }
            $activity->children()->attach($attach);
        }

        return response()->json($activity->load(['group','creator','children']), 201);
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'group_id'    => ['nullable','exists:groups,id'],
            'title'       => ['sometimes','string','max:150'],
            'description' => ['nullable','string'],
            'scheduled_at'=> ['nullable','date'],
        ]);
        $activity->update($data);
        return response()->json($activity->load(['group','creator']));
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->noContent();
    }
}
