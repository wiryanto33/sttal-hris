<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Seting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $presences = $user->presences()
            ->with('location')
            ->orderBy('date', 'desc')
            ->paginate(15);
        $todayPresence = $user->getTodayPresences();
        $locations = Seting::where('is_active', true)->get();

        return view('presences.index', compact('presences', 'todayPresence', 'locations'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            // Keep your original table reference
            'location_id' => 'required|exists:setings,id'
        ]);

        $user = Auth::user();
        $location = Seting::findOrFail($request->location_id);

        // Check if already checked in today
        $todayAttendance = $user->getTodayPresences();
        if ($todayAttendance && $todayAttendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today.'
            ]);
        }

        // Validate location
        $isLocationValid = $location->isWithinRadius($request->latitude, $request->longitude);

        // Check if it's working day
        if (!$location->isWorkingDay()) {
            return response()->json([
                'success' => false,
                'message' => 'Today is not a working day for this location.'
            ]);
        }

        // Check if late
        $now = now();
        $isLate = $now->format('H:i:s') > $location->start_time;

        // Determine status
        $status = 'present';
        if ($isLate) {
            $status = 'late';
        }
        if (!$isLocationValid) {
            $status = 'partial'; // Present but location invalid
        }

        // Create or update attendance
        $attendance = Presence::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => today()
            ],
            [
                // Keep your original foreign key naming
                'seting_id' => $location->id, // or whatever your foreign key column is named
                'check_in_time' => $now,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'is_late' => $isLate,
                'is_location_valid' => $isLocationValid,
                'status' => $status,
                'notes' => $request->notes ?? null
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => [
                'attendance' => $attendance,
                'is_late' => $isLate,
                'is_location_valid' => $isLocationValid,
                'status' => $status
            ]
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $user = Auth::user();
        $attendance = $user->getTodayPresences();

        if (!$attendance || !$attendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'You must check in first.'
            ]);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked out today.'
            ]);
        }

        $location = $attendance->location;
        $isLocationValid = $location->isWithinRadius($request->latitude, $request->longitude);

        $attendance->update([
            'check_out_time' => now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'is_location_valid' => $attendance->is_location_valid && $isLocationValid
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out successful!',
            'data' => [
                'attendance' => $attendance->fresh(),
                'working_hours' => $attendance->working_hours
            ]
        ]);
    }

    public function getLocationDetails($id)
    {
        $location = Seting::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
