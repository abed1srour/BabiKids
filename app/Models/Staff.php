<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
use HasFactory;
protected $fillable = ['user_id','first_name','last_name','phone','hire_date','staff_role'];


public function user(){ return $this->belongsTo(User::class); }
public function leads(){ return $this->hasMany(Group::class,'lead_staff_id'); }
public function createdActivities(){ return $this->hasMany(Activity::class,'created_by'); }
}
