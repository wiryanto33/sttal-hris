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
                        LIhat Payrolls
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
                                Slip Gaji
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

                    <div id="print-area">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>User</strong> </label>

                                    <p>{{ $payroll->user->name }}</p>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Salary</strong> </label>
                                    <p>{{ number_format($payroll->salary) }}</p>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Bonuses</strong> </label>
                                    <p>{{ number_format($payroll->bonuses) }}</p>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Deduction</strong> </label>
                                    <p>{{ number_format($payroll->deductions) }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">
                                        <strong>Nett Sallary</strong>
                                    </label>
                                    <p>{{ number_format($payroll->net_salary) }}</p>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Pay Date</strong></label>
                                    <p>{{ \Carbon\Carbon::parse($payroll->pay_date)->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary" id="btn-print"><span class="bi bi-printer"></span>
                        Print</button>
                </div>
            </div>
        </section>
    </div>

    <script></script>
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

        document.getElementById('btn-print').addEventListener('click', function() {

            let printContent = document.getElementById('print-area').innerHTML;
            let origunalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;

            window.print();

            document.body.innerHTML = origunalContent;
        });
    </script>
@endpush
