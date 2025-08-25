<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'parent_id',
        'amount',
        'currency',
        'method',   // cash|card|bank
        'status',   // pending|paid|failed|refunded
        'due_date',
        'paid_at',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'due_date'=> 'date',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
