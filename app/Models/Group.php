<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
use HasFactory;
protected $fillable = ['name','age_range','capacity','lead_staff_id'];


public function lead(){ return $this->belongsTo(Staff::class,'lead_staff_id'); }
public function children(){ return $this->hasMany(Child::class); }
public function activities(){ return $this->hasMany(Activity::class); }
}