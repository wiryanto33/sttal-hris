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
                        
                        {{-- User field - only show for managers --}}
                        @can('manage all leave requests')
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id"
                                    class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">Pilih User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $leaveRequest->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @else
                            {{-- Hidden field for regular users --}}
                            <input type="hidden" name="user_id" value="{{ $leaveRequest->user_id }}">
                            <div class="mb-3">
                                <label class="form-label">Pemohon</label>
                                <input type="text" class="form-control" value="{{ $leaveRequest->user->name }}" readonly>
                            </div>
                        @endcan

                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Izin <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                required>
                                <option value="">Pilih Alasan</option>
                                <option value="sakit" {{ old('reason', $leaveRequest->reason) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="cuti" {{ old('reason', $leaveRequest->reason) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="menikah" {{ old('reason', $leaveRequest->reason) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="melahirkan" {{ old('reason', $leaveRequest->reason) == 'melahirkan' ? 'selected' : '' }}>Melahirkan</option>
                                <option value="keluarga" {{ old('reason', $leaveRequest->reason) == 'keluarga' ? 'selected' : '' }}>Kepentingan Keluarga</option>
                                <option value="lainnya" {{ old('reason', $leaveRequest->reason) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan Tambahan</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                rows="3" placeholder="Jelaskan detail izin Anda (opsional)...">{{ old('description', $leaveRequest->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', $leaveRequest->start_date) }}" name="start_date" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', $leaveRequest->end_date) }}" name="end_date" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Bukti Dukung (Opsional)</label>
                            @if($leaveRequest->attachment)
                            <p>File saat ini: <a href="{{ route('leave_requests.download_attachment', $leaveRequest->id) }}" target="_blank">Download</a></p>
                            @endif
                            <input type="file"
                                class="form-control @error('attachment') is-invalid
                            @enderror" name="attachment">
                             <small class="text-muted">Kosongkan jika tidak ingin mengubah file.</small>
                            @error('attachment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @can('manage all leave requests')
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
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

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Catatan Admin</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ old('admin_notes', $leaveRequest->admin_notes) }}</textarea>
                        </div>
                        @endcan

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
