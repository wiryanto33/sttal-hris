<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        // Summary metrics
        $totalUsers = User::count();
        $departmentsCount = Departement::count();

        $pendingTasksQuery = Task::query();
        if (!$user->hasRole('superadmin')) {
            $pendingTasksQuery->where('assigned_to', $user->id);
        }
        $pendingTasks = $pendingTasksQuery->whereIn('status', ['pending', 'in_progress'])->count();

        $pendingLeavesQuery = LeaveRequest::query();
        if (!$user->hasRole('superadmin')) {
            $pendingLeavesQuery->where('user_id', $user->id);
        }
        $pendingLeaves = $pendingLeavesQuery->where('status', 'pending')->count();

        // Attendance today
        if ($user->hasRole('superadmin')) {
            $presentToday = Presence::whereDate('date', today())->count();
            $myPresence = $user->getTodayPresences();
        } else {
            $myPresence = $user->getTodayPresences();
            $presentToday = $myPresence ? 1 : 0; // simplified for non-admin view
        }

        // Recent members (show basic identity + detail)
        $members = User::with('userDetail')
            ->orderBy('name')
            ->take(10)
            ->get();

        // Monthly attendance chart data (current month)
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $base = Presence::whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        if (!$user->hasRole('superadmin')) {
            $base->where('user_id', $user->id);
        }

        $dailyTotal = (clone $base)
            ->selectRaw('DAY(date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dailyLate = (clone $base)
            ->where('status', 'late')
            ->selectRaw('DAY(date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $daysInMonth = (int) $start->daysInMonth;
        $categories = [];
        $totalSeries = [];
        $lateSeries = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $categories[] = str_pad((string) $d, 2, '0', STR_PAD_LEFT);
            $totalSeries[] = (int) ($dailyTotal[$d] ?? 0);
            $lateSeries[] = (int) ($dailyLate[$d] ?? 0);
        }

        $attendanceChart = [
            'title' => 'Absensi ' . now()->format('F Y'),
            'categories' => $categories,
            'series' => [
                ['name' => 'Check-in', 'data' => $totalSeries],
                ['name' => 'Terlambat', 'data' => $lateSeries],
            ],
        ];

        // Today summary donut
        $todayBase = Presence::whereDate('date', today());
        if (!$user->hasRole('superadmin')) {
            $todayBase->where('user_id', $user->id);
        }
        $todayPresentCount = (clone $todayBase)->where('status', 'present')->count();
        $todayLateCount = (clone $todayBase)->where('status', 'late')->count();
        $todayPartialCount = (clone $todayBase)->where('status', 'partial')->count();
        if ($user->hasRole('superadmin')) {
            $todayAbsentCount = max(0, User::count() - ($todayPresentCount + $todayLateCount + $todayPartialCount));
        } else {
            $todayAbsentCount = ($todayPresentCount + $todayLateCount + $todayPartialCount) ? 0 : 1;
        }
        $todaySummary = [
            'labels' => ['Hadir', 'Terlambat', 'Absen'],
            'series' => [
                $todayPresentCount + $todayPartialCount,
                $todayLateCount,
                $todayAbsentCount,
            ],
            'title' => 'Ringkasan Hari Ini (' . now()->format('d M Y') . ')'
        ];

        return view('dashboard', compact(
            'totalUsers',
            'departmentsCount',
            'pendingTasks',
            'pendingLeaves',
            'presentToday',
            'myPresence',
            'members',
            'attendanceChart',
            'todaySummary'
        ));
    }
}
