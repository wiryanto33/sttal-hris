<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pangkat',
        'korps',
        'nrp',
        'gender',
        'image',
        'address',
        'phone',
        'birth_date',
        'join_date',
        'user_id',
        'departement_id',
        'role_id',
        'status',
        'salary',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Departement
    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    // Relasi ke Role (jika menggunakan Spatie Role atau model Role sendiri)
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
