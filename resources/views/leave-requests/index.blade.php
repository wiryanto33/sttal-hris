@extends('layouts.dashboard')

@section('content')
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cuti/Izin</h3>
                    <p class="text-subtitle text-muted">
                        {{ auth()->user()->hasRole('superadmin') ? 'Kelola Cuti/Izin Anggota' : 'Riwayat Cuti/Izin Anda' }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Cuti/Izin
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Statistics Cards (for superadmin) --}}
            @if (auth()->user()->hasRole('superadmin'))
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="stats-icon purple">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="text-muted font-semibold">Pending</h6>
                                            <h6 class="font-extrabold mb-0">
                                                {{ $leaveRequests->where('status', 'pending')->count() }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="stats-icon green">
                                                <i class="bi bi-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="text-muted font-semibold">Approved</h6>
                                            <h6 class="font-extrabold mb-0">
                                                {{ $leaveRequests->where('status', 'approved')->count() }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="stats-icon red">
                                                <i class="bi bi-x-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="text-muted font-semibold">Rejected</h6>
                                            <h6 class="font-extrabold mb-0">
                                                {{ $leaveRequests->where('status', 'rejected')->count() }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="stats-icon blue">
                                                <i class="bi bi-calendar3"></i>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="text-muted font-semibold">Total</h6>
                                            <h6 class="font-extrabold mb-0">{{ $leaveRequests->count() }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Data Cuti/Izin</h5>
                        <div class="d-flex gap-2">
                            {{-- Filter Status --}}
                            @if (auth()->user()->hasRole('superadmin'))
                                <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            @endif

                            {{-- Create Button --}}
                            @if (!auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('superadmin'))
                                <a href="{{ route('leave_requests.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Ajukan Cuti/Izin
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($leaveRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Belum Ada Data Cuti/Izin</h5>
                            <p class="text-muted">
                                {{ auth()->user()->hasRole('superadmin') ? 'Belum ada pengajuan cuti/izin dari anggota.' : 'Anda belum pernah mengajukan cuti/izin.' }}
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="leaveRequestsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ auth()->user()->hasRole('superadmin') ? 'Pemohon' : 'Tanggal Pengajuan' }}
                                        </th>
                                        <th>Periode Izin</th>
                                        <th>Total Hari</th>
                                        <th>Alasan</th>
                                        <th>Lampiran</th>
                                        <th>Status</th>
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <th>Catatan superadmin</th>
                                        @endif
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaveRequests as $index => $leaveRequest)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            {{-- User Info or Request Date --}}
                                            <td>
                                                @if (auth()->user()->hasRole('superadmin'))
                                                    <div class="d-flex align-items-center">
                                                        @if ($leaveRequest->user->profile_image)
                                                            <img src="{{ asset('storage/' . $leaveRequest->user->profile_image) }}"
                                                                alt="Avatar" class="rounded-circle me-2" width="40"
                                                                height="40">
                                                        @else
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                style="width: 40px; height: 40px;">
                                                                <span class="text-white fw-bold">
                                                                    {{ strtoupper(substr($leaveRequest->user->name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $leaveRequest->user->name }}</div>
                                                            <small
                                                                class="text-muted">{{ $leaveRequest->user->email }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $leaveRequest->requested_at ? $leaveRequest->requested_at->format('d/m/Y H:i') : $leaveRequest->created_at->format('d/m/Y H:i') }}
                                                        </div>
                                                        <small class="text-muted">Tanggal pengajuan</small>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Leave Period --}}
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }}
                                                    </div>
                                                    <small class="text-muted">s/d
                                                        {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</small>
                                                </div>
                                            </td>

                                            {{-- Total Days --}}
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $leaveRequest->total_days ?? \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }}
                                                    hari
                                                </span>
                                            </td>

                                            {{-- Reason --}}
                                            <td>
                                                <div>
                                                    <span
                                                        class="fw-semibold text-capitalize">{{ $leaveRequest->reason }}</span>
                                                    @if ($leaveRequest->description)
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($leaveRequest->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Attachment --}}
                                            <td>
                                                @if ($leaveRequest->attachment)
                                                    <a href="{{ asset('storage/' . $leaveRequest->attachment) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                                @switch($leaveRequest->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-clock me-1"></i>Menunggu
                                                        </span>
                                                    @break

                                                    @case('approved')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Disetujui
                                                        </span>
                                                    @break

                                                    @case('rejected')
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Ditolak
                                                        </span>
                                                    @break
                                                @endswitch
                                            </td>

                                            {{-- superadmin Notes (for superadmin only) --}}
                                            @if (auth()->user()->hasRole('superadmin'))
                                                <td>
                                                    @if ($leaveRequest->superadmin_notes)
                                                        <span class="text-muted" data-bs-toggle="tooltip"
                                                            title="{{ $leaveRequest->superadmin_notes }}">
                                                            {{ Str::limit($leaveRequest->superadmin_notes, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif

                                            {{-- Actions --}}
                                            <td>
                                                <div class="btn-group" role="group">
                                                    {{-- View Detail --}}
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailModal{{ $leaveRequest->id }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    @if (auth()->user()->hasRole('superadmin'))
                                                        {{-- superadmin Actions --}}
                                                        @if ($leaveRequest->status === 'pending')
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                onclick="updateStatus({{ $leaveRequest->id }}, 'approved')">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="updateStatus({{ $leaveRequest->id }}, 'rejected')">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Edit --}}
                                                        <a href="{{ route('leave_requests.edit', $leaveRequest->id) }}"
                                                            class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>

                                                        {{-- Delete --}}
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete({{ $leaveRequest->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @else
                                                        {{-- User Actions --}}
                                                        @if ($leaveRequest->status === 'pending' && $leaveRequest->user_id === auth()->id())
                                                            <a href="{{ route('leave_requests.edit', $leaveRequest->id) }}"
                                                                class="btn btn-sm btn-outline-warning">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete({{ $leaveRequest->id }})">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Detail Modal --}}
                                        <div class="modal fade" id="detailModal{{ $leaveRequest->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Cuti/Izin</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Pemohon:</strong><br>
                                                                {{ $leaveRequest->user->name }}<br>
                                                                {{ $leaveRequest->user->email }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Status:</strong><br>
                                                                @switch($leaveRequest->status)
                                                                    @case('pending')
                                                                        <span class="badge bg-warning text-dark">Menunggu
                                                                            Persetujuan</span>
                                                                    @break

                                                                    @case('approved')
                                                                        <span class="badge bg-success">Disetujui</span>
                                                                    @break

                                                                    @case('rejected')
                                                                        <span class="badge bg-danger">Ditolak</span>
                                                                    @break
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <strong>Tanggal Mulai:</strong><br>
                                                                {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d F Y') }}
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Tanggal Selesai:</strong><br>
                                                                {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d F Y') }}
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Total Hari:</strong><br>
                                                                {{ $leaveRequest->total_days ?? \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }}
                                                                hari
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Alasan:</strong><br>
                                                                {{ $leaveRequest->reason }}
                                                            </div>
                                                        </div>
                                                        @if ($leaveRequest->description)
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Keterangan:</strong><br>
                                                                    {{ $leaveRequest->description }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($leaveRequest->superadmin_notes)
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Catatan superadmin:</strong><br>
                                                                    {{ $leaveRequest->superadmin_notes }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($leaveRequest->processed_at)
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Diproses pada:</strong><br>
                                                                    {{ $leaveRequest->processed_at->format('d F Y H:i') }}
                                                                    @if ($leaveRequest->processedBy)
                                                                        oleh {{ $leaveRequest->processedBy->name }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        @if ($leaveRequest->attachment)
                                                            <a href="{{ asset('storage/' . $leaveRequest->attachment) }}"
                                                                target="_blank" class="btn btn-secondary me-auto">
                                                                <i class="bi bi-download"></i> Download Lampiran
                                                            </a>
                                                        @endif
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    {{-- Status Update Modal --}}
    @if (auth()->user()->hasRole('superadmin'))
        <div class="modal fade" id="statusUpdateModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status Cuti/Izin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="statusUpdateForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="statusSelect" class="form-select" required>
                                    <option value="approved">Setujui</option>
                                    <option value="rejected">Tolak</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="superadmin_notes" class="form-label">Catatan superadmin</label>
                                <textarea name="superadmin_notes" id="superadminNotes" class="form-control" rows="3"
                                    placeholder="Berikan catatan untuk keputusan ini..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data cuti/izin ini?</p>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status filter functionality
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#leaveRequestsTable tbody tr');

            tableRows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(7)'); // Status column
                const statusText = statusCell.textContent.toLowerCase();

                if (filterValue === '' || statusText.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Update status function
        // Update status function - replace the existing one in your blade template
        function updateStatus(leaveRequestId, status) {
            document.getElementById('statusSelect').value = status;
            // Use the correct route name with Laravel's route helper
            document.getElementById('statusUpdateForm').action = `/leave_requests/${leaveRequestId}/update-status`;

            const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
            modal.show();
        }

        // Confirm delete function
        function confirmDelete(leaveRequestId) {
            document.getElementById('deleteForm').action = `/leave-requests/${leaveRequestId}`;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
