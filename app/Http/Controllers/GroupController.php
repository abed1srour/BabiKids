<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return response()->json(Group::with(['lead'])->paginate(15));
    }

    public function show(Group $group)
    {
        return response()->json($group->load(['lead','children']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required','string','max:120'],
            'age_range'     => ['nullable','string','max:50'],
            'capacity'      => ['nullable','integer','min:1'],
            'lead_staff_id' => ['nullable','exists:staff,id'],
        ]);

        $group = Group::create($data);
        return response()->json($group->load('lead'), 201);
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name'          => ['sometimes','string','max:120'],
            'age_range'     => ['nullable','string','max:50'],
            'capacity'      => ['sometimes','integer','min:1'],
            'lead_staff_id' => ['nullable','exists:staff,id'],
        ]);

        $group->update($data);
        return response()->json($group->load('lead'));
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response()->noContent();
    }
}
