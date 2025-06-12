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
                        {{ auth()->user()->hasRole('superadmin') ? 'Kelola Leave Request' : 'Buat Leave Request' }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('leave_requests.index') }}">Leave Request</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Create
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Leave Request</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('leave_requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- User field - only show for superadmin --}}
                        @if (auth()->user()->hasRole('superadmin'))
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id"
                                    class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">Pilih User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <div class="mb-3">
                                <label class="form-label">Pemohon</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Izin <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                required>
                                <option value="">Pilih Alasan</option>
                                <option value="sakit" {{ old('reason') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="cuti" {{ old('reason') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="menikah" {{ old('reason') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="melahirkan" {{ old('reason') == 'melahirkan' ? 'selected' : '' }}>Melahirkan
                                </option>
                                <option value="keluarga" {{ old('reason') == 'keluarga' ? 'selected' : '' }}>Kepentingan
                                    Keluarga</option>
                                <option value="lainnya" {{ old('reason') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Custom reason field (shown when "Lainnya" is selected) --}}
                        <div class="mb-3" id="custom-reason-field" style="display: none;">
                            <label for="custom_reason" class="form-label">Alasan Lainnya</label>
                            <textarea name="custom_reason" id="custom_reason" class="form-control @error('custom_reason') is-invalid @enderror"
                                rows="3" placeholder="Jelaskan alasan izin Anda...">{{ old('custom_reason') }}</textarea>
                            @error('custom_reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan Tambahan</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                rows="3" placeholder="Jelaskan detail izin Anda (opsional)...">{{ old('description') }}</textarea>
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
                                        value="{{ old('start_date') }}" name="start_date" required>
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
                                        value="{{ old('end_date') }}" name="end_date" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="total_days" class="form-label">Total Hari</label>
                            <input type="number" id="total_days" class="form-control" readonly>
                            <small class="text-muted">Otomatis dihitung berdasarkan tanggal mulai dan selesai</small>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Bukti Pendukung</label>
                            <input type="file" id="attachment"
                                class="form-control @error('attachment') is-invalid @enderror" name="attachment"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Format: PDF, JPG, PNG, DOC, DOCX (Max: 2MB)</small>
                            @error('attachment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status field - only show for superadmin --}}
                        @if (auth()->user()->hasRole('superadmin'))
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved
                                    </option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- superadmin notes field --}}
                            <div class="mb-3" id="superadmin-notes-field" style="display: none;">
                                <label for="superadmin_notes" class="form-label">Catatan superadmin</label>
                                <textarea name="superadmin_notes" id="superadmin_notes" class="form-control @error('superadmin_notes') is-invalid @enderror"
                                    rows="3" placeholder="Berikan catatan untuk keputusan ini...">{{ old('superadmin_notes') }}</textarea>
                                @error('superadmin_notes')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @else
                            {{-- Hidden status field for regular users --}}
                            <input type="hidden" name="status" value="pending">
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('leave_requests.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                {{ auth()->user()->hasRole('superadmin') ? 'Simpan' : 'Ajukan Izin' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- JavaScript for dynamic behavior --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reasonSelect = document.getElementById('reason');
            const customReasonField = document.getElementById('custom-reason-field');
            const statusSelect = document.getElementById('status');
            const superadminNotesField = document.getElementById('superadmin-notes-field');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const totalDaysInput = document.getElementById('total_days');

            // Show/hide custom reason field
            if (reasonSelect) {
                reasonSelect.addEventListener('change', function() {
                    if (this.value === 'lainnya') {
                        customReasonField.style.display = 'block';
                        document.getElementById('custom_reason').required = true;
                    } else {
                        customReasonField.style.display = 'none';
                        document.getElementById('custom_reason').required = false;
                    }
                });

                // Trigger on page load
                if (reasonSelect.value === 'lainnya') {
                    customReasonField.style.display = 'block';
                }
            }

            // Show/hide superadmin notes field
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    if (this.value === 'approved' || this.value === 'rejected') {
                        superadminNotesField.style.display = 'block';
                    } else {
                        superadminNotesField.style.display = 'none';
                    }
                });

                // Trigger on page load
                if (statusSelect.value === 'approved' || statusSelect.value === 'rejected') {
                    superadminNotesField.style.display = 'block';
                }
            }

            // Calculate total days
            function calculateTotalDays() {
                if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);

                    if (endDate >= startDate) {
                        const timeDiff = endDate.getTime() - startDate.getTime();
                        const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) +
                        1; // +1 to include both start and end date
                        totalDaysInput.value = dayDiff;
                    } else {
                        totalDaysInput.value = '';
                    }
                }
            }

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', calculateTotalDays);
                endDateInput.addEventListener('change', calculateTotalDays);

                // Set minimum date to today
                const today = new Date().toISOString().split('T')[0];
                startDateInput.min = today;

                // Update end date minimum when start date changes
                startDateInput.addEventListener('change', function() {
                    endDateInput.min = this.value;
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }
                    calculateTotalDays();
                });

                // Calculate on page load if values exist
                calculateTotalDays();
            }
        });
    </script>
@endsection
