
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Absensi Saya</h2>

                {{-- Check In/Out Section --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Absensi Hari ini - {{ now()->format('F j, Y') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="presences-status">
                            @if ($todayPresence)
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Check In:</strong>
                                            {{ $todayPresence->check_in_time ? $todayPresence->check_in_time->format('H:i:s') : 'Not checked in' }}
                                            @if ($todayPresence->is_late)
                                                <span class="badge bg-warning">Terlambat</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Check Out:</strong>
                                            {{ $todayPresence->check_out_time ? $todayPresence->check_out_time->format('H:i:s') : 'Not checked out' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Location:</strong> {{ $todayPresence->location->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-{{ $todayPresence->status_color }}">
                                                {{ ucfirst($todayPresence->status) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p>Tidak ada record absensi hari ini.</p>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select id="location-select" class="form-select mb-3">
                                        <option value="">Pilih Lokasi</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" data-lat="{{ $location->latitude }}"
                                                data-lng="{{ $location->longitude }}"
                                                data-radius="{{ $location->radius_meters }}">
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button id="get-location-btn" class="btn btn-info btn-sm">Ambil Lokasi Saya</button>
                                    <span id="location-status" class="text-muted ms-2"></span>
                                </div>

                            </div>

                            @if (!$todayPresence || !$todayPresence->check_in_time)
                                <button id="check-in-btn" class="btn btn-success" disabled
                                    data-url="{{ route('presences.checkin') }}">Check In</button>
                            @elseif(!$todayPresence->check_out_time)
                                <button id="check-out-btn" class="btn btn-danger">Check Out</button>
                            @else
                                <p class="text-muted">Anda telah melakukan absensi hari ini.</p>
                            @endif
                        </div>

                        {{-- Map Container --}}
                        <div id="map" style="height: 300px; margin-top: 20px; display: none;"></div>
                    </div>
                </div>

                {{-- presences History --}}
                <div class="card">
                    <div class="card-header">
                        <h5>Riwayat Absensi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($presences as $presence)
                                        <tr>
                                            <td>{{ $presence->date->format('M j, Y') }}</td>
                                            <td>{{ $presence->location->name }}</td>
                                            <td>
                                                {{ $presence->check_in_time ? $presence->check_in_time->format('H:i') : '-' }}
                                                @if ($presence->is_late)
                                                    <small class="text-warning">(Late)</small>
                                                @endif
                                            </td>
                                            <td>{{ $presence->check_out_time ? $presence->check_out_time->format('H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($presence->working_hours)
                                                    {{ number_format($presence->working_hours / 60, 1) }}h
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $presence->status_color }}">
                                                    {{ ucfirst($presence->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada riwayat absensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $presences->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map;
        let userMarker;
        let locationMarker;
        let locationCircle;
        let userLat, userLng;
        let selectedLocation;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            map = L.map('map').setView([-7.5360639, 112.7665045], 13); // Default to Malang, East Java

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Event listeners
            document.getElementById('get-location-btn').addEventListener('click', getCurrentLocation);
            document.getElementById('location-select').addEventListener('change', onLocationSelect);

            const checkInBtn = document.getElementById('check-in-btn');
            const checkOutBtn = document.getElementById('check-out-btn');

            if (checkInBtn) {
                checkInBtn.addEventListener('click', checkIn);
            }

            if (checkOutBtn) {
                checkOutBtn.addEventListener('click', checkOut);
            }
        });

        function getCurrentLocation() {
            console.log('getCurrentLocation called');
            const statusEl = document.getElementById('location-status');
            statusEl.textContent = 'Getting location...';

            document.getElementById('map').style.display = 'block';


            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLat = position.coords.latitude;
                        userLng = position.coords.longitude;

                        statusEl.textContent = 'Location obtained';
                        statusEl.className = 'text-success ms-2';
                        document.getElementById('map').style.display = 'block';

                        updateMap();
                        checkLocationValidity();
                    },
                    function(error) {
                        statusEl.textContent = 'Location access denied';
                        statusEl.className = 'text-danger ms-2';
                    }
                );
            } else {
                statusEl.textContent = 'Geolocation not supported';
                statusEl.className = 'text-danger ms-2';
            }
        }

        function onLocationSelect() {
            const select = document.getElementById('location-select');
            const option = select.options[select.selectedIndex];

            if (option.value) {
                selectedLocation = {
                    id: option.value,
                    lat: parseFloat(option.dataset.lat),
                    lng: parseFloat(option.dataset.lng),
                    radius: parseInt(option.dataset.radius)
                };

                document.getElementById('map').style.display = 'block';
                updateMap();
                checkLocationValidity();
            } else {
                selectedLocation = null;
                document.getElementById('map').style.display = 'none';
            }
        }

        function updateMap() {
            if (!selectedLocation) return;

            // Clear existing markers
            if (locationMarker) map.removeLayer(locationMarker);
            if (locationCircle) map.removeLayer(locationCircle);
            if (userMarker) map.removeLayer(userMarker);

            // Add location marker and radius
            locationMarker = L.marker([selectedLocation.lat, selectedLocation.lng])
                .addTo(map)
                .bindPopup('Office Location');

            locationCircle = L.circle([selectedLocation.lat, selectedLocation.lng], {
                color: 'blue',
                fillColor: '#3085d6',
                fillOpacity: 0.2,
                radius: selectedLocation.radius
            }).addTo(map);

            // Add user marker if location is available
            if (userLat && userLng) {
                userMarker = L.marker([userLat, userLng])
                    .addTo(map)
                    .bindPopup('Your Location');

                // Fit map to show both markers
                const group = new L.featureGroup([locationMarker, userMarker]);
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                map.setView([selectedLocation.lat, selectedLocation.lng], 16);
            }
        }

        function checkLocationValidity() {
            if (!selectedLocation || !userLat || !userLng) return;

            const distance = calculateDistance(
                userLat, userLng,
                selectedLocation.lat, selectedLocation.lng
            );

            const isValid = distance <= selectedLocation.radius;
            const checkInBtn = document.getElementById('check-in-btn');
            const checkOutBtn = document.getElementById('check-out-btn');

            if (checkInBtn) {
                checkInBtn.disabled = !isValid;
                checkInBtn.title = isValid ? 'Ready to check in' :
                    `You are ${Math.round(distance)}m away (max: ${selectedLocation.radius}m)`;
            }

            if (checkOutBtn) {
                checkOutBtn.disabled = false; // Allow checkout regardless of location
            }

            // Update user marker color
            if (userMarker) {
                userMarker.setIcon(L.icon({
                    iconUrl: isValid ?
                        'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png' :
                        'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }));
            }
        }

        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function checkIn() {
            if (!selectedLocation || !userLat || !userLng) {
                alert('Please select a location and get your current position first.');
                return;
            }

            const data = {
                latitude: userLat,
                longitude: userLng,
                location_id: selectedLocation.id,
                _token: '{{ csrf_token() }}'
            };

            const url = document.getElementById('check-in-btn').dataset.url;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during check-in.');
                });
        }

        function checkOut() {
            if (!userLat || !userLng) {
                alert('Please get your current position first.');
                return;
            }

            const data = {
                latitude: userLat,
                longitude: userLng,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route('presences.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during check-out.');
                });
        }
    </script>
@endsection
