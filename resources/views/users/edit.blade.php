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
                    <h3>Profile</h3>
                    <p class="text-subtitle text-muted">
                        Profile Detail
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.index') }}">Employees</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Edit
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <section class="section">
            <div class="row">

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <div class="avatar avatar-2xl">
                                    @if ($user->userDetail->image == null)
                                        <x-image-preview src="{{ asset('storage/' . 'default/avatar.png') }}" />
                                    @else
                                        <x-image-preview src="{{ asset($user->userDetail?->image) }}" />
                                    @endif
                                </div>

                                <h3 class="mt-3">{{ $user->name }}</h3>
                                <p class="text-small">{{ $user->userDetail->pangkat }} {{ $user->userDetail->korps }} NRP
                                    {{ $user->userDetail->nrp }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.change-password', $user->id) }}" method="POST">
                                @csrf
                                <div class="form-group my-2">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Enter your current password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group my-2">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter new password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group my-2">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="Confirm new password">
                                </div>

                                <div class="form-group my-2 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>


                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ route('users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Your Name" value="{{ $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="pangkat" class="form-label">Pangkat</label>
                                    <input type="text" name="pangkat" id="pangkat" class="form-control"
                                        placeholder="Rank" value="{{ $user->userDetail->pangkat }}">
                                </div>
                                <div class="form-group">
                                    <label for="korps" class="form-label">Korps</label>
                                    <input type="text" name="korps" id="korps" class="form-control"
                                        placeholder="Corps" value="{{ $user->userDetail->korps }}">
                                </div>
                                <div class="form-group">
                                    <label for="nrp" class="form-label">Nrp</label>
                                    <input type="text" name="nrp" id="nrp" class="form-control"
                                        placeholder="Nrp" value="{{ $user->userDetail->nrp }}">
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Gender</label>
                                    <select name="gender" id="gender"
                                        class="form-control @error('gender') is-invalid
                                    @enderror">
                                        <option value="male" @if (old('gender', $user->userDetail->gender) == 'male') selected @endif>Male
                                        </option>
                                        <option value="female" @if (old('gender', $user->userDetail->gender) == 'female') selected @endif>Female
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Image</label>
                                    <input type="file"
                                        class="form-control @error('image') is-invalid
                                    @enderror"
                                        value="{{ old('image') }}" name="image">
                                    @error('image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Address</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"> {{ $user->userDetail->address }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email"
                                        value="{{ $user->email }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ $user->userDetail->phone }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label">Tanggal Lahir</label>
                                    <input type="date"
                                        class="form-control date @error('birth_date') is-invalid
                                    @enderror"
                                        value="{{ $user->userDetail->birth_date }}" name="birth_date" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Join Date</label>
                                    <input type="date"
                                        class="form-control date @error('join_date') is-invalid @enderror"
                                        value="{{ $user->userDetail->join_date }}" name="join_date" required>
                                    @error('join_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Department</label>
                                    <select name="departement_id" id=""
                                        class="form-control @error('departement_id') is-invalid @enderror">
                                        <option value="">Select Departement</option>
                                        @foreach ($departements as $departement)
                                            <option value="{{ $departement->id }}"
                                                @if (old('departement_id', $user->userDetail->departement_id) == $departement->id) selected @endif>{{ $departement->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departement_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Role field - Only visible for superadmin --}}
                                @if (auth()->user()->hasRole('superadmin'))
                                    <div class="form-group">
                                        <label for="role" class="form-label">Role</label>
                                        <select name="role" id="role"
                                            class="form-control @error('role') is-invalid @enderror">
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    @if (old('role', $user->roles->first()?->name) == $role->name) selected @endif>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    {{-- Hidden input to maintain current role for non-superadmin users --}}
                                    <input type="hidden" name="role" value="{{ $user->roles->first()?->name }}">

                                    {{-- Display current role as read-only for non-superadmin users --}}
                                    <div class="form-group">
                                        <label for="role_display" class="form-label">Role</label>
                                        <input type="text" id="role_display" class="form-control"
                                            value="{{ $user->roles->first()?->name ?? 'No Role Assigned' }}" readonly>
                                        <small class="form-text text-muted">Only superadmin can modify user roles.</small>
                                    </div>
                                @endif


                                <div class="form-group">
                                    <label for="" class="form-label">Status</label>
                                    <select name="status" id=""
                                        class="form-control @error('status') is-invalid
                                    @enderror">
                                        <option value="inactive" @if (old('status', $user->userDetail->status) == 'inactive') selected @endif>
                                            Inactive</option>
                                        <option value="active" @if (old('status', $user->userDetail->status) == 'active') selected @endif>Active
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
