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
                        Payrolls Anggota Sttal
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Payrolls
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

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @can('create payrolls')
                        <div class="d-flex">
                            <a href="{{ route('payrolls.create') }}" class="btn btn-primary mb-3 ms-auto">New Payroll</a>
                        </div>
                    @endcan


                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Salary</th>
                                <th>Bonuses</th>
                                <th>Deduction</th>
                                <th>Nett Salary</th>
                                <th>Pay Date</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payrolls as $payroll)
                                <tr>
                                    <td>
                                        @if ($payroll->user->userDetail->image == null)
                                            <x-image-preview src="{{ asset('storage/' . 'default/avatar.png') }}" />
                                        @else
                                            <x-image-preview src="{{ asset($payroll->user->userDetail?->image) }}" />
                                        @endif

                                        {{ $payroll->user->name }}
                                        <p>{{ $payroll->user->userDetail->pangkat }} {{ $payroll->user->userDetail->korps }}
                                            NRP
                                            {{ $payroll->user->userDetail->nrp }}</p>
                                    </td>
                                    <td>
                                        {{ number_format($payroll->salary) }}
                                    </td>
                                    <td>
                                        {{ number_format($payroll->bonuses) }}
                                    </td>
                                    <td>{{ number_format($payroll->deductions) }}</td>
                                    <td>
                                        {{ number_format($payroll->net_salary) }}
                                    </td>
                                    <td>
                                        {{ $payroll->pay_date }}
                                    </td>
                                    <td>
                                        @can('edit payrolls')
                                            <a href="{{ route('payrolls.edit', $payroll->id) }}"
                                                class="btn btn-info btn-sm">Edit</a>
                                        @endcan

                                        @can('show payrolls')
                                            <a href="{{ route('payrolls.show', $payroll->id) }}"
                                                class="btn btn-primary btn-sm">Slip gaji</a>
                                        @endcan

                                        @can('delete payrolls')
                                            <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST"
                                                style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        @endcan

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
