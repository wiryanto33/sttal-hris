<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'attachment',
        'status',
        'total_days',
        'requested_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who processed this leave request
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
