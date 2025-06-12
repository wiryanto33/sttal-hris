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
                    <h3>Users</h3>
                    <p class="text-subtitle text-muted">
                        Handle Users data or profile
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                index
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data User</h5>
                </div>
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @can('create users')
                        <div class="d-flex">
                            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3 ms-auto">New User</a>
                        </div>
                    @endcan

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Departement</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}
                                        <p>{{ $user->userDetail->pangkat }} {{ $user->userDetail->korps }} NRP
                                            {{ $user->userDetail->nrp }}</p>
                                    </td>

                                    <td>
                                        @if ($user->userDetail->image == null)
                                            <x-image-preview src="{{ asset('storage/' . 'default/avatar.png') }}" />
                                        @else
                                            <x-image-preview src="{{ asset($user->userDetail?->image) }}" />
                                        @endif
                                    </td>

                                    <td>{{ $user->userDetail?->departement?->name ?? '-' }}</td>

                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($user->userDetail->status == 'active')
                                            <span class="badge bg-success">{{ $user->userDetail->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $user->userDetail->status }}</span>
                                        @endif

                                    </td>
                                    <td>
                                        @can('edit users')
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-sm btn-info btn-sm">Edit</a>
                                        @endcan


                                        @can('delete users')
                                            <!-- Tombol untuk membuka modal -->
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal{{ $user->id }}">
                                                Delete
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="confirmDeleteModal{{ $user->id }}" tabindex="-1"
                                                aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">
                                                                Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus user
                                                            <strong>{{ $user->name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="{{ route('users.destroy', $user->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
