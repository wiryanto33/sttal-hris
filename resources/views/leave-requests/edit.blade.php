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
                    <h3>Leave Request</h3>
                    <p class="text-subtitle text-muted">
                        Edit Leave Request
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('leave_requests.index') }}">Leave Request</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                Edit
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Leave request</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('leave_requests.update', $leaveRequest->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="" class="form-label">Employee</label>
                            <select name="employee_id" id="" class="form-control">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Reason</label>
                            <select name="reason" id="" class="form-control">
                                <option value="sakit" @if (old('reason', $leaveRequest->reason) == 'sakit') selected @endif>Sakit</option>
                                <option value="cuti" @if (old('reason', $leaveRequest->reason) == 'cuti') selected @endif>Cuti</option>
                                <option value="menikah" @if (old('reason', $leaveRequest->reason) == 'menikah') @endif>Menikah</option>
                                <option value="melahirkan" @if (old('reason', $leaveRequest->reason) == 'melahirkan') selected @endif>Melahirkan
                                </option>
                                <option value="keluarga" @if (old('reason', $leaveRequest->reason) == 'keluarga') selected @endif>Kepentingan
                                    Keluarga</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Start Date</label>
                            <input type="date"
                                class="form-control date @error('start_date') is-invalid
                            @enderror"
                                value="{{ $leaveRequest->start_date }}" name="start_date" required>
                            @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">End Date</label>
                            <input type="date"
                                class="form-control date @error('end_date') is-invalid
                            @enderror"
                                value="{{ $leaveRequest->end_date }}" name="end_date" required>
                            @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Bukti Dukung</label>
                            <input type="file"
                                class="form-control @error('attachment') is-invalid
                            @enderror"
                                value="{{ $leaveRequest->attachment }}" name="attachment">
                            @error('attachment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Status</label>
                            <select name="status" id="" class="form-control">
                                <option value="pending" @if (old('status', $leaveRequest->status) == 'pending') selected @endif>Pending</option>
                                <option value="approved" @if (old('status', $leaveRequest->status) == 'approved') selected @endif>Approved</option>
                                <option value="rejected" @if (old('status', $leaveRequest->status) == 'rejected') selected @endif>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateNetSalary() {
            const salary = parseFloat(document.getElementById('salary').value) || 0;
            const bonuses = parseFloat(document.getElementById('bonuses').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;

            const net = salary + bonuses - deductions;
            document.getElementById('net_salary').value = net.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('salary').addEventListener('input', calculateNetSalary);
            document.getElementById('bonuses').addEventListener('input', calculateNetSalary);
            document.getElementById('deductions').addEventListener('input', calculateNetSalary);
            calculateNetSalary();
        });
    </script>
@endpush
