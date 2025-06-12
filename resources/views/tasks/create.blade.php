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
                                <a href="{{ route('tasks.index') }}"> Tugas</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                New Task
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

                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">User</label>
                            <select name="assigned_to" id=""
                                class="form-control @error('assigned_to') is-invalid
                            @enderror">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{$user->userDetail->pangkat}} {{ $user->userDetail->korps}} {{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Due Date</label>
                            <input type="datetime-local"
                                class="form-control date @error('due_date') is-invalid
                            @enderror"
                                value="{{ old('due_date') }}" name="due_date" required>
                            @error('due_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">File Attachment</label>
                            <input type="file"
                                class="form-control @error('file') is-invalid
                            @enderror"
                                value="{{ old('file') }}" name="file">
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="" class="form-label">Status</label>
                            <select name="status" id="status"
                                class="form-control @error('status') is-invalid
                            @enderror">
                                <option value="pending">Pending</option>
                                <option value="progress">In Progress</option>
                                <option value="selesai">Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Description</label>
                            <textarea name="description" id=""
                                class="form-control @error('description') is-invalid
                            @enderror">

                            </textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
