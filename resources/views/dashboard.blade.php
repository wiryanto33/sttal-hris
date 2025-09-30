
@extends('layouts.dashboard')

@section('content')
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>

        <style>
            .scrollable-container { max-height: 420px; overflow-y: auto; }
            .stat-card { min-height: 112px; }
            .stat-card .stats-icon { width: 42px; height: 42px; display:flex; align-items:center; justify-content:center; border-radius: 10px; }
            .stat-card .stats-icon i, .stat-card .stats-icon .bi { font-size: 20px; }
            .stat-title { white-space: nowrap; }
        </style>

    </header>

    <div class="page-heading">
        <h3>Dashboard Laporan</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold stat-title">Total Users</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalUsers ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-diagram-3"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold stat-title">Departemen</h6>
                                        <h6 class="font-extrabold mb-0">{{ $departmentsCount ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-list-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold stat-title">Tugas Pending</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pendingTasks ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon red mb-2">
                                            <i class="bi bi-envelope-open"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold stat-title">Cuti Pending</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pendingLeaves ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Monitoring Absensi Bulanan</h4>
                                <span class="text-muted small">{{ $attendanceChart['title'] ?? '' }}</span>
                            </div>
                            <div class="card-body">
                                <div id="chart-monthly-attendance"></div>
                                <!-- placeholder to avoid error in default dashboard.js -->
                                <div id="chart-profile-visit" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="{{ auth()->user()->userDetail->image ?? asset('storage/default/avatar.png') }}" alt="User Avatar">
                            </div>
                            <div class="ms-3">
                                <h5 class="font-bold mb-0">
                                    {{ auth()->user()->name }}
                                    {{-- Posisi:({{ auth()->user()->roles->pluck('name')->implode(', ') }}) --}}
                                </h5>
                                <p class="text-muted mb-0">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Anggota Sttal</h4>
                    </div>
                    <div class="card-content pb-4 scrollable-container">
                        @forelse ($members ?? [] as $member)
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg">
                                    @php($img = $member->userDetail->image ?? null)
                                    @if (!$img)
                                        <img src="{{ asset('storage/default/avatar.png') }}" />
                                    @else
                                        <img src="{{ asset($img) }}" />
                                    @endif
                                </div>
                                <div class="name ms-4">
                                    <h6 class="mb-1">{{ $member->name }}</h6>
                                    <p class="text-muted mb-0">
                                        {{ $member->userDetail->pangkat ?? '-' }}
                                        {{ $member->userDetail->korps ?? '' }}
                                        NRP {{ $member->userDetail->nrp ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-3 text-center text-muted">Belum ada data anggota.</div>
                        @endforelse
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Ringkasan Absensi</h4>
                        <span class="text-muted small">{{ $todaySummary['title'] ?? '' }}</span>
                    </div>
                    <div class="card-body">
                        <div id="chart-today-summary"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        if (!window.ApexCharts) return;
        const chartData = @json($attendanceChart ?? ['categories'=>[], 'series'=>[]]);
        const allSeries = (chartData.series || []).map(s => s.data || []);
        const flat = allSeries.reduce((a,b)=>a.concat(b), []);
        const maxVal = Math.max(1, ...flat, 0);
        const isAllZero = flat.length > 0 && flat.every(v => v === 0);
        const options = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
            },
            series: chartData.series || [],
            xaxis: {
                categories: chartData.categories || [],
                tickPlacement: 'on',
                labels: { style: { colors: '#9aa0ac' } }
            },
            colors: ['#435ebe', '#f59e0b'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            grid: { strokeDashArray: 4 },
            legend: { position: 'top' },
            yaxis: {
                min: 0,
                max: maxVal,
                tickAmount: 4,
                labels: { formatter: (val) => Math.round(val) }
            },
            tooltip: { theme: 'dark' },
            noData: { text: 'Belum ada data bulan ini' }
        };
        const el = document.querySelector('#chart-monthly-attendance');
        if (el) {
            const chart = new ApexCharts(el, options);
            chart.render();
            if (isAllZero) {
                // Tampilkan pesan ringan jika semua nilai 0
                const empty = document.createElement('div');
                empty.className = 'text-center text-muted mt-3';
                empty.innerText = 'Belum ada aktivitas absensi pada bulan ini';
                el.appendChild(empty);
            }
        }
    })();
    (function () {
        if (!window.ApexCharts) return;
        const donutData = @json($todaySummary ?? ['labels'=>[], 'series'=>[]]);
        const el = document.querySelector('#chart-today-summary');
        if (!el) return;
        const options = {
            chart: { type: 'donut', width: '100%', height: 320 },
            labels: donutData.labels || [],
            series: donutData.series || [],
            colors: ['#22c55e', '#f59e0b', '#ef4444'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            stroke: { width: 1 },
            plotOptions: { pie: { donut: { size: '40%' } } },
            noData: { text: 'Tidak ada data hari ini' }
        };
        const chart = new ApexCharts(el, options);
        chart.render();
    })();
</script>
<!-- hidden placeholders to keep Mazer's default dashboard.js happy -->
<div style="display:none">
    <div id="chart-profile-visit"></div>
    <div id="chart-visitors-profile"></div>
    <div id="chart-europe"></div>
    <div id="chart-america"></div>
    <div id="chart-indonesia"></div>
    <!-- actual charts are rendered elsewhere with the IDs below, so don't duplicate those -->
</div>
@endpush
