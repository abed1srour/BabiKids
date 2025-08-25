<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'created_by',      // staff id
        'report_date',
        'summary',
        'milestone_scores' // JSON
    ];

    protected $casts = [
        'report_date'      => 'date',
        'milestone_scores' => 'array',
    ];

    // Relationships
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function author() // staff who wrote it
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }
}
