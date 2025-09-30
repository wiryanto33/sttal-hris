<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class AttendanceReportController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view reports', only: ['index']),
            // new Middleware('permission:edit users', only: ['edit']),
            // new Middleware('permission:create users', only: ['create']),
            // new Middleware('permission:delete users', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->get('user_id');
        $status = $request->get('status');

        // Build query
        $query = Presence::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        // Apply filters
        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get attendance records
        $presences = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);



        // Get users for filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Calculate summary statistics
        $totalRecords = $query->count();
        $presentCount = (clone $query)->where('status', 'present')->count();
        $absentCount = (clone $query)->where('status', 'absent')->count();
        $lateCount = (clone $query)->where('status', 'late')->count();

        $summary = [
            'total' => $totalRecords,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'present_percentage' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0
        ];

        return view('reports.index', compact(
            'presences',
            'users',
            'startDate',
            'endDate',
            'userId',
            'status',
            'summary'
        ));
    }


    public function export(Request $request)
    {
        // Export functionality can be added here
        // For now, return JSON data
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $attendances = Presence::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($attendances);
    }
}
