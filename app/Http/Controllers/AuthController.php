<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
public function register(RegisterRequest $request){
$data = $request->validated();
$user = User::create([
'name'=>$data['name'],
'email'=>$data['email'],
'password'=>$data['password'],
'phone'=>$data['phone'] ?? null,
'role'=>$data['role'] ?? 'parent',
]);
if($user->role === 'parent'){
ParentModel::create([
'user_id'=>$user->id,
'first_name'=>$data['first_name'] ?? $user->name,
'last_name'=>$data['last_name'] ?? '',
'phone'=>$data['phone'] ?? '',
]);
}
$token = $user->createToken('api')->plainTextToken;
return response()->json(['user'=>$user,'token'=>$token],201);
}


public function login(LoginRequest $request){
$creds = $request->validated();
$user = User::where('email',$creds['email'])->first();
if(!$user || !Hash::check($creds['password'],$user->password)){
return response()->json(['message'=>'Invalid credentials'],401);
}
$token = $user->createToken('api')->plainTextToken;
return response()->json(['user'=>$user,'token'=>$token]);
}


public function logout(Request $request){
$request->user()->currentAccessToken()->delete();
return response()->json(['message'=>'Logged out']);
}
}
