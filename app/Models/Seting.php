<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'start_time',
        'end_time',
        'working_days',
        'is_active'
    ];

    protected $casts = [
        'working_days' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    public function isWithinRadius($latitude, $longitude)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance <= $this->radius_meters;
    }

    public function isWorkingDay()
    {
        $today = strtolower(now()->format('l')); // e.g., 'monday'

        // Decode JSON jika masih dalam bentuk string
        $days = is_array($this->working_days)
            ? $this->working_days
            : json_decode($this->working_days, true);

        return in_array($today, $days);
    }


    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
