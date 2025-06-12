<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'file',
        'description',
        'assigned_to',
        'due_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
