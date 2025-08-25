<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'created_by',   // staff id
        'title',
        'description',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator() // staff who created it
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function children()
    {
        return $this->belongsToMany(Child::class, 'activity_child')
            ->withPivot(['status', 'notes', 'recorded_by'])
            ->withTimestamps();
    }

    public function records()
    {
        // if you create a pivot model (optional)
        return $this->hasMany(ActivityChild::class, 'activity_id');
    }

    // Scopes
    public function scopeUpcoming($q)
    {
        return $q->whereNotNull('scheduled_at')->where('scheduled_at', '>=', now());
    }
}
