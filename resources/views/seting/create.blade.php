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
                    <h3>Kantor</h3>
                    <p class="text-subtitle text-muted">
                        Buat Kantor
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('setings.index') }}">Kantor</a>
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
                    <h5 class="card-title">Data Kantor</h5>
                </div>
                <div class="card-body">

                    {{-- 'name',
                    'address',
                    'latitude',
                    'longitude',
                    'radius_meters',
                    'start_time',
                    'end_time',
                    'working_days',
                    'is_active' --}}

                    <form action="{{ route('setings.store') }}" method="POST">
                        @csrf
                        <div class="mb-b">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Alamat</label>
                            <textarea name="address" id=""
                                class="form-control @error('address') is-invalid
                            @enderror">

                            </textarea>
                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Latitude</label>
                            <input type="text" class="form-control" name="latitude" value="{{ old('latitude') }}" required>
                            @error('latitude')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Longitude</label>
                            <input type="text" class="form-control" name="longitude" value="{{ old('longitude') }}" required>
                            @error('longitude')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Radius (in meters)</label>
                            <input type="number" class="form-control" name="radius_meters" value="{{ old('radius_meters') }}" required>
                            @error('radius_meters')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" name="start_time" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" name="end_time" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-b">
                            <label for="" class="form-label">Hari Kerja</label>
                            <select name="working_days[]" id=""
                                class="form-control @error('working_days') is-invalid
                            @enderror" multiple>
                                <option value="monday">Senin</option>
                                <option value="tuesday">Selasa</option>
                                <option value="wednesday">Rabu</option>
                                <option value="thursday">Kamis</option>
                                <option value="friday">Jumat</option>
                                <option value="saturday">Sabtu</option>
                                <option value="sunday">Minggu</option>
                            </select>
                            @error('working_days')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>



                        <div class="mb-3">
                            <label for="" class="form-label">Status</label>
                            <select name="is_active" id=""
                                class="form-control @error('is_active') is-invalid
                            @enderror">
                                <option value="0">Inactive</option>
                                <option value="1">Active</option>
                            </select>
                            @error('is_active')
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
