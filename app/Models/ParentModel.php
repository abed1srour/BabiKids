<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
use HasFactory;
protected $table = 'parents';
protected $fillable = ['user_id','first_name','last_name','phone','address'];


public function user(){ return $this->belongsTo(User::class); }
public function children(){
return $this->belongsToMany(Child::class,'child_parent')
->withPivot(['relationship','is_primary','is_emergency_contact'])
->withTimestamps();
}
public function payments(){ return $this->hasMany(Payment::class,'parent_id'); }
}
