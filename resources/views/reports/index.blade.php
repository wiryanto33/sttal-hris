@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Laporan Absensi</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Records</span>
                            <h4 class="mb-3">{{ $summary['total'] }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="mdi mdi-account-multiple font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Hadir</span>
                            <h4 class="mb-3 text-success">{{ $summary['present'] }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="mdi mdi-check-circle font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Tidak Hadir</span>
                            <h4 class="mb-3 text-danger">{{ $summary['absent'] }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger">
                                <span class="avatar-title rounded-circle bg-danger">
                                    <i class="mdi mdi-close-circle font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Absensi Persentase</span>
                            <h4 class="mb-3 text-info">{{ $summary['present_percentage'] }}%</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info">
                                <span class="avatar-title rounded-circle bg-info">
                                    <i class="mdi mdi-chart-line font-size-16"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absen</option>
                                <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                            <button type="button" class="btn btn-success" onclick="exportData()">
                                <i class="mdi mdi-download"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Working Hours</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presences as $index => $presence)
                                    <tr>
                                        <td>{{ $presences->firstItem() + $index }}</td>
                                        <td>{{ \Carbon\Carbon::parse($presence->date)->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($presence->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                {{ $presence->user->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($presence->check_in_time)
                                                <span class="text-success">
                                                    {{ \Carbon\Carbon::parse($presence->check_in_time)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presence->check_out_time)
                                                <span class="text-danger">
                                                    {{ \Carbon\Carbon::parse($presence->check_out_time)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presence->status == 'present')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($presence->status == 'absent')
                                                <span class="badge bg-danger">Absen</span>
                                            @elseif($presence->status == 'late')
                                                <span class="badge bg-warning">Terlambat</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($presence->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presence->check_in_time && $presence->check_out_time)
                                                @php
                                                    $checkIn = \Carbon\Carbon::parse($presence->check_in_time);
                                                    $checkOut = \Carbon\Carbon::parse($presence->check_out_time);
                                                    $workingHours = $checkOut->diff($checkIn);
                                                @endphp
                                                <span class="text-info">
                                                    {{ $workingHours->format('%H:%I') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($presence->notes ?? false)
                                                <span class="text-truncate" style="max-width: 150px;"
                                                      title="{{ $presence->notes }}">
                                                    {{ $presence->notes }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="mdi mdi-clipboard-text-outline font-size-48 text-muted mb-2"></i>
                                                <p class="text-muted mb-0">tidak ada data absensi</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($presences->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="text-muted">
                                    Showing {{ $presences->firstItem() }} to {{ $presences->lastItem() }}
                                    of {{ $presences->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $presences->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportData() {
    const params = new URLSearchParams();
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const userId = document.getElementById('user_id').value;
    const status = document.getElementById('status').value;

    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (userId) params.append('user_id', userId);
    if (status) params.append('status', status);

    // For now, this will return JSON data
    // You can modify this to export as CSV/Excel
    const url = "{{ route('reports.index') }}/export?" + params.toString();
    window.open(url, '_blank');
}
</script>
@endsection
