<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
use HasFactory;
protected $table = 'attendance';
protected $fillable = ['child_id','recorded_by','date','status','check_in_time','check_out_time','notes'];


public function child(){ return $this->belongsTo(Child::class); }
public function recorder(){ return $this->belongsTo(Staff::class,'recorded_by'); }
}