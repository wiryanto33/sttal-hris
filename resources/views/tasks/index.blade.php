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
                    <h3>Task</h3>
                    <p class="text-subtitle text-muted">
                        Tugas yang harus diselesaikan
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Data Tugas
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Tugas</h5>
                </div>
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @can('tasks create')
                        <div class="d-flex">
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3 ms-auto">New Task</a>
                        </div>
                    @endcan

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Assigned To</th>
                                <th>Pelaksanaan</th>
                                <th>File Attachment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        {{ $task->user->name }}
                                        <p>{{ $task->user->userDetail->pangkat }} {{ $task->user->userDetail->korps }} NRP
                                            {{ $task->user->userDetail->nrp }}</p>
                                    </td>
                                    <td>{{ $task->due_date }}</td>

                                    <td>
                                        @if ($task->file)
                                            <a href="{{ asset('storage/' . $task->file) }}" download
                                                class="btn btn-sm btn-secondary">
                                                Download
                                            </a>
                                        @else
                                            <span class="text-muted">No File</span>
                                        @endif
                                    </td>


                                    <td>
                                        @if ($task->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($task->status == 'selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-info">Progress</span>
                                        @endif
                                    </td>

                                    <td>

                                        @if ($task->status == 'pending')
                                            <a href="{{ route('tasks.selesai', $task->id) }}"
                                                class="btn btn-success btn-sm">Tandai Selesai</a>
                                        @else
                                            <a href="{{ route('tasks.pending', $task->id) }}"
                                                class="btn btn-warning btn-sm">Pending</a>
                                        @endif

                                        @can('edit tasks')
                                            <a href="{{ route('tasks.edit', $task->id) }}"
                                                class="btn btn-primary btn-sm">Edit</a>
                                        @endcan

                                        @can('delete tasks')
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
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
