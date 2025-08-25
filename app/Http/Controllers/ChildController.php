<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $q = Child::with(['group','parents']);
        if ($groupId = $request->query('group_id')) $q->where('group_id', $groupId);
        if ($status  = $request->query('status'))   $q->where('status', $status);
        return response()->json($q->paginate(15));
    }

    public function show(Child $child)
    {
        return response()->json($child->load(['group','parents','attendance','activities','progressReports','payments']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'        => ['required','string','max:100'],
            'last_name'         => ['required','string','max:100'],
            'date_of_birth'     => ['required','date','before:today'],
            'gender'            => ['nullable','in:male,female,other'],
            'medical_notes'     => ['nullable','string'],
            'enrollment_date'   => ['required','date'],
            'status'            => ['required','in:active,inactive'],
            'group_id'          => ['nullable','exists:groups,id'],

            // Optional: link parents right away
            'parents'           => ['nullable','array'],
            'parents.*.id'      => ['required_with:parents','exists:parents,id'],
            'parents.*.relationship' => ['nullable','in:mother,father,guardian,other'],
            'parents.*.is_primary'   => ['nullable','boolean'],
            'parents.*.is_emergency_contact' => ['nullable','boolean'],
        ]);

        $child = DB::transaction(function () use ($data) {
            $parents = $data['parents'] ?? null;
            unset($data['parents']);

            $child = Child::create($data);

            if ($parents) {
                $attach = [];
                foreach ($parents as $p) {
                    $attach[$p['id']] = [
                        'relationship' => $p['relationship'] ?? null,
                        'is_primary'   => (bool)($p['is_primary'] ?? false),
                        'is_emergency_contact' => (bool)($p['is_emergency_contact'] ?? false),
                    ];
                }
                $child->parents()->attach($attach);
            }
            return $child;
        });

        return response()->json($child->load(['group','parents']), 201);
    }

    public function update(Request $request, Child $child)
    {
        $data = $request->validate([
            'first_name'      => ['sometimes','string','max:100'],
            'last_name'       => ['sometimes','string','max:100'],
            'date_of_birth'   => ['sometimes','date','before:today'],
            'gender'          => ['nullable','in:male,female,other'],
            'medical_notes'   => ['nullable','string'],
            'enrollment_date' => ['sometimes','date'],
            'status'          => ['sometimes','in:active,inactive'],
            'group_id'        => ['nullable','exists:groups,id'],
        ]);

        $child->update($data);
        return response()->json($child->load(['group','parents']));
    }

    public function destroy(Child $child)
    {
        $child->delete();
        return response()->noContent();
    }
}
