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
                    <h3>Payrolls</h3>
                    <p class="text-subtitle text-muted">
                        Buat Payrolls
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('payrolls.index') }}">Payrolls</a>
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
                    <h5 class="card-title">Data Payrolls</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('payrolls.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">User</label>
                            <select name="user_id" id="" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Salary</label>
                            <input type="text"
                                class="form-control @error('salary') is-invalid
                            @enderror"
                                value="{{ old('salary')  }}" name="salary" id="salary">
                            @error('salary')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Bonuses</label>
                            <input type="text"
                                class="form-control @error('bonuses') is-invalid
                            @enderror"
                                value="{{ old('bonuses') }}" name="bonuses" id="bonuses">
                            @error('bonuses')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label"> Deduction</label>
                            <input type="text"
                                class="form-control @error('deductions') is-invalid
                            @enderror"
                                value="{{ old('deductions') }}" name="deductions" id="deductions">
                            @error('deductions')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">
                                Nett Sallary
                            </label>
                            <input type="text"
                                class="form-control date @error('net_salary') is-invalid
                            @enderror"
                                value="{{ old('net_salary') }}" name="net_salary" id="net_salary" readonly>
                            @error('net_salary')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Pay Date</label>
                            <input type="date"
                                class="form-control date @error('pay_date') is-invalid
                            @enderror"
                                value="{{ old('pay_date') }}" name="date" required>
                            @error('pay_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
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

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('salary').addEventListener('input', calculateNetSalary);
        document.getElementById('bonuses').addEventListener('input', calculateNetSalary);
        document.getElementById('deductions').addEventListener('input', calculateNetSalary);
        calculateNetSalary();
    });
</script>
@endpush

