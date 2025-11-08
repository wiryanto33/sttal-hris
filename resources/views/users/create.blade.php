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
                    <h3>User</h3>
                    <p class="text-subtitle text-muted">
                        Handle User data or profile
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('users.index') }}">User</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                New User
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

                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Pangkat</label>
                            <input type="text" class="form-control" name="pangkat" value="{{ old('pangkat') }}" required>
                            @error('pangkat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Korps</label>
                            <input type="text" class="form-control" name="korps" value="{{ old('korps') }}" required>
                            @error('korps')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Nrp</label>
                            <input type="text" class="form-control" name="nrp" value="{{ old('nrp') }}" required>
                            @error('nrp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender"
                                class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Tanggal Lahir</label>
                            <input type="date"
                                class="form-control date @error('birth_date') is-invalid
                            @enderror"
                                value="{{ old('birth_date') }}" name="birth_date" required>
                            @error('birth_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="" class="form-label">Join Date</label>
                            <input type="date"
                                class="form-control date @error('join_date') is-invalid
                            @enderror"
                                value="{{ old('join_date') }}" name="join_date" required>
                            @error('join_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="departement_id" class="form-label">Department</label>
                            <select name="departement_id" id="departement_id"
                                class="form-control @error('departement_id') is-invalid @enderror" required>
                                <option value="">Select Departement</option>
                                @foreach ($departements as $departement)
                                    <option value="{{ $departement->id }}" {{ old('departement_id') == $departement->id ? 'selected' : '' }}>
                                        {{ $departement->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departement_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select name="role_id" id="role_id"
                                class="form-control @error('role_id') is-invalid @enderror" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status"
                                class="form-control @error('status') is-invalid @enderror" required>
                                <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">password</label>
                            <input type="password"
                                class="form-control @error('password') is-invalid
                            @enderror"
                                value="{{ old('password') }}" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">Confirm Password</label>
                            <input type="password"
                                class="form-control @error('password_confirmation') is-invalid
                            @enderror"
                                value="{{ old('password_confirmation') }}" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Buat User</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
