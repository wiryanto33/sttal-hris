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
                    <h3>Edit Kantor</h3>
                    <p class="text-subtitle text-muted">
                        Edit Kantor
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('setings.index') }}">Kantor</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Edit
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
                    {{-- Display validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('setings.update', $seting->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Name Field --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $seting->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Address Field --}}
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $seting->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Latitude Field --}}
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" id="latitude"
                                class="form-control @error('latitude') is-invalid @enderror" name="latitude"
                                value="{{ old('latitude', $seting->latitude) }}" step="any" required>
                            @error('latitude')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Longitude Field --}}
                        <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" id="longitude"
                                class="form-control @error('longitude') is-invalid @enderror" name="longitude"
                                value="{{ old('longitude', $seting->longitude) }}" step="any" required>
                            @error('longitude')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Radius Field --}}
                        <div class="mb-3">
                            <label for="radius_meters" class="form-label">Radius (in meters)</label>
                            <input type="number" id="radius_meters"
                                class="form-control @error('radius_meters') is-invalid @enderror" name="radius_meters"
                                value="{{ old('radius_meters', $seting->radius_meters) }}" min="0" required>
                            @error('radius_meters')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Start Time Field --}}
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Jam Mulai</label>
                            <input type="time" id="start_time"
                                class="form-control @error('start_time') is-invalid @enderror" name="start_time"
                                value="{{ old('start_time', \Carbon\Carbon::parse($seting->start_time)->format('H:i')) }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- End Time Field --}}
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Jam Selesai</label>
                            <input type="time" id="end_time"
                                class="form-control @error('end_time') is-invalid @enderror" name="end_time"
                                value="{{ old('end_time', \Carbon\Carbon::parse($seting->end_time)->format('H:i')) }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Working Days Field --}}
                        <div class="mb-3">
                            <label for="working_days" class="form-label">Hari Kerja</label>
                            <select id="working_days" name="working_days[]"
                                class="form-control @error('working_days') is-invalid @enderror" multiple>
                                <option value="monday" @if (in_array('monday', $selectedDays ?? [])) selected @endif>Senin</option>
                                <option value="tuesday" @if (in_array('tuesday', $selectedDays ?? [])) selected @endif>Selasa</option>
                                <option value="wednesday" @if (in_array('wednesday', $selectedDays ?? [])) selected @endif>Rabu</option>
                                <option value="thursday" @if (in_array('thursday', $selectedDays ?? [])) selected @endif>Kamis</option>
                                <option value="friday" @if (in_array('friday', $selectedDays ?? [])) selected @endif>Jumat</option>
                                <option value="saturday" @if (in_array('saturday', $selectedDays ?? [])) selected @endif>Sabtu</option>
                                <option value="sunday" @if (in_array('sunday', $selectedDays ?? [])) selected @endif>Minggu</option>
                            </select>
                            @error('working_days')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status Field --}}
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select id="is_active" name="is_active"
                                class="form-control @error('is_active') is-invalid @enderror">
                                <option value="0" @if (old('is_active', $seting->is_active) == 0) selected @endif>Inactive</option>
                                <option value="1" @if (old('is_active', $seting->is_active) == 1) selected @endif>Active</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('setings.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
