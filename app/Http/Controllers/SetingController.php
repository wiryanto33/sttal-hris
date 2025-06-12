<?php

namespace App\Http\Controllers;

use App\Models\Seting;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SetingController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view seting', only: ['index']),
            new Middleware('permission:edit seting', only: ['edit']),
            new Middleware('permission:create seting', only: ['create']),
            new Middleware('permission:delete seting', only: ['destroy']),
        ];
    }

    public function index()
    {
        $setings = Seting::withCount('presences')->get();

        return view('seting.index', compact('setings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seting.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|numeric|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'working_days' => 'required|array',
            'is_active' => 'boolean'
        ]);
        Seting::create([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'working_days' => $request->working_days,
            'is_active' => $request->is_active ?? false
        ]);
        return redirect()->route('setings.index')->with('success', 'Setting created successfully.');
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
        $seting = Seting::findOrFail($id);

        // Handle working_days properly
        $selectedDays = [];
        if ($seting->working_days) {
            $selectedDays = is_array($seting->working_days)
                ? $seting->working_days
                : json_decode($seting->working_days, true) ?? [];
        }

        return view('seting.edit', compact('seting', 'selectedDays'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|max:255', // Changed from nullable to required
            'address' => 'nullable|string|max:255',
            'latitude' => 'required|numeric', // Changed from nullable to required
            'longitude' => 'required|numeric', // Changed from nullable to required
            'radius_meters' => 'required|numeric|min:0', // Changed from nullable to required
            'start_time' => 'required|date_format:H:i', // Changed from nullable to required
            'end_time' => 'required|date_format:H:i|after:start_time', // Changed from nullable to required
            'working_days' => 'nullable|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'required|boolean' // Changed from boolean to required|boolean
        ]);

        $seting = Seting::findOrFail($id);

        $seting->update([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'working_days' => $request->working_days ?? [],
            'is_active' => (bool)$request->is_active, // Explicit boolean casting
        ]);

        return redirect()->route('setings.index')->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seting = Seting::findOrFail($id);
        $seting->delete();

        return redirect()->route('setings.index')->with('success', 'Setting deleted successfully.');
    }
}
