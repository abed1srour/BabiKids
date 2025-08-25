<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $q = Staff::with('user');
        if ($role = $request->query('staff_role')) $q->where('staff_role', $role);
        return response()->json($q->paginate(15));
    }

    public function show(Staff $staff)
    {
        return response()->json($staff->load(['user','leads']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => ['nullable','exists:users,id'],
            'name'       => ['nullable','string','max:100'],
            'email'      => ['nullable','email','unique:users,email'],
            'password'   => ['nullable','string','min:8'],

            'first_name' => ['required','string','max:100'],
            'last_name'  => ['required','string','max:100'],
            'phone'      => ['required','string','max:30'],
            'hire_date'  => ['nullable','date'],
            'staff_role' => ['required','in:teacher,admin,nurse,assistant'],
        ]);

        $staff = DB::transaction(function () use ($data) {
            if (empty($data['user_id'])) {
                $user = User::create([
                    'name'     => $data['name'] ?? ($data['first_name'].' '.$data['last_name']),
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'secret1234'),
                    'role'     => 'staff',
                ]);
                $data['user_id'] = $user->id;
            }
            unset($data['name'],$data['email'],$data['password']);

            return Staff::create($data);
        });

        return response()->json($staff->load('user'), 201);
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'first_name' => ['sometimes','string','max:100'],
            'last_name'  => ['sometimes','string','max:100'],
            'phone'      => ['sometimes','string','max:30'],
            'hire_date'  => ['nullable','date'],
            'staff_role' => ['sometimes','in:teacher,admin,nurse,assistant'],
        ]);

        $staff->update($data);
        return response()->json($staff->load('user'));
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return response()->noContent();
    }
}
