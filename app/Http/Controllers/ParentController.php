<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $q = ParentModel::with('user');
        if ($search = $request->query('q')) {
            $q->where(function ($x) use ($search) {
                $x->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }
        return response()->json($q->paginate(15));
    }

    public function show(ParentModel $parent)
    {
        return response()->json($parent->load(['user', 'children']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Option A: link to existing user
            'user_id'    => ['nullable', 'exists:users,id'],
            // Option B: create new user
            'name'       => ['nullable','string','max:100'],
            'email'      => ['nullable','email','unique:users,email'],
            'password'   => ['nullable','string','min:8'],

            'first_name' => ['required','string','max:100'],
            'last_name'  => ['required','string','max:100'],
            'phone'      => ['required','string','max:30'],
            'address'    => ['nullable','string','max:255'],
        ]);

        $parent = DB::transaction(function () use ($data) {
            if (empty($data['user_id'])) {
                $user = User::create([
                    'name'     => $data['name'] ?? ($data['first_name'].' '.$data['last_name']),
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'secret1234'),
                    'role'     => 'parent',
                ]);
                $data['user_id'] = $user->id;
            }
            unset($data['name'],$data['email'],$data['password']);

            return ParentModel::create($data);
        });

        return response()->json($parent->load('user'), 201);
    }

    public function update(Request $request, ParentModel $parent)
    {
        $data = $request->validate([
            'first_name' => ['sometimes','string','max:100'],
            'last_name'  => ['sometimes','string','max:100'],
            'phone'      => ['sometimes','string','max:30'],
            'address'    => ['nullable','string','max:255'],
        ]);

        $parent->update($data);
        return response()->json($parent->load('user'));
    }

    public function destroy(ParentModel $parent)
    {
        $parent->delete();
        return response()->noContent();
    }
}
