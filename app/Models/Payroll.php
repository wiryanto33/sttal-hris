<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    // protected $table = 'payroll';

    protected $fillable = [
        'user_id',
        'sallary',
        'bonuses',
        'deductions',
        'net_salary',
        'pay_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
