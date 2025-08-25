<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
use HasFactory;
protected $fillable = ['first_name','last_name','date_of_birth','gender','medical_notes','enrollment_date','status','group_id'];


public function group(){ return $this->belongsTo(Group::class); }
public function parents(){
return $this->belongsToMany(ParentModel::class,'child_parent')
->withPivot(['relationship','is_primary','is_emergency_contact'])
->withTimestamps();
}
public function attendance(){ return $this->hasMany(Attendance::class); }
public function activities(){ return $this->belongsToMany(Activity::class,'activity_child'); }
public function payments(){ return $this->hasMany(Payment::class); }
public function progressReports(){ return $this->hasMany(ProgressReport::class); }
}
