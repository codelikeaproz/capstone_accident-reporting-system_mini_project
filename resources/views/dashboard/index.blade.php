@extends('layouts.app')

@section('title', 'Dashboard - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">MDRRMO Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, {{ auth()->user()->full_name }}!
                @if(auth()->user()->isAdmin())
                    <span class="badge bg-primary">Administrator</span>
                @else
                    <span class="badge bg-info">MDRRMO Staff</span>
                @endif
            </p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Report Incident
                </a>
                <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown">
                    <span class="visually-hidden">More Actions</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('incidents.index') }}">
                        <i class="fas fa-list me-2"></i>View All Incidents</a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-map-marked-alt me-2"></i>Heat Map</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-download me-2"></i>Export Reports</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['total_incidents'] }}</h3>
                            <p class="text-muted mb-0">Total Incidents</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> {{ $stats['incidents_this_month'] }} this month
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x" style="color: var(--warning);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['pending_incidents'] }}</h3>
                            <p class="text-muted mb-0">Pending Response</p>
                            <small class="text-warning">
                                <i class="fas fa-clock"></i> Requires attention
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x" style="color: var(--info);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['available_vehicles'] }}</h3>
                            <p class="text-muted mb-0">Available Vehicles</p>
                            <small class="text-info">
                                <i class="fas fa-truck"></i> {{ $stats['total_vehicles'] }} total fleet
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ambulance fa-2x" style="color: var(--primary-500);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-1" style="color: var(--primary-700);">{{ $stats['incidents_today'] }}</h3>
                            <p class="text-muted mb-0">Today's Incidents</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar-day"></i> {{ now()->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x" style="color: var(--success);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Incident Trends Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Incident Trends</h5>
                    <small class="text-muted">Last 6 months</small>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Incident Type Distribution -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Incident Types</h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Status Overview Cards -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-warning">{{ $stats['pending_incidents'] }}</h4>
                                <p class="mb-0 text-muted">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1" style="color: var(--info);">{{ $stats['responding_incidents'] }}</h4>
                                <p class="mb-0 text-muted">Responding</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1 text-success">{{ $stats['resolved_incidents'] }}</h4>
                                <p class="mb-0 text-muted">Resolved</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="mb-1" style="color: var(--primary-600);">{{ $stats['total_casualties'] + $stats['total_injuries'] }}</h4>
                                <p class="mb-0 text-muted">Total Affected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Status -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Fleet Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Available</span>
                        <span class="badge bg-success">{{ $vehicleStats['available'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['available'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Deployed</span>
                        <span class="badge" style="background-color: var(--info);">{{ $vehicleStats['deployed'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" style="background-color: var(--info); width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['deployed'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Maintenance</span>
                        <span class="badge bg-warning">{{ $vehicleStats['maintenance'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $stats['total_vehicles'] > 0 ? (($vehicleStats['maintenance'] ?? 0) / $stats['total_vehicles'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents and Pending Actions -->
    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Incidents</h5>
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recentIncidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Incident #</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIncidents->take(5) as $incident)
                                        <tr>
                                            <td><strong>{{ $incident->incident_number }}</strong></td>
                                            <td>
                                                <small>{{ str_replace('_', ' ', \Illuminate\Support\Str::title($incident->incident_type)) }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ Str::limit($incident->location, 20) }}<br>
                                                    <small class="text-muted">{{ $incident->barangay }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $incident->incident_datetime->format('M d, H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge status-{{ $incident->status }}">
                                                    {{ ucfirst($incident->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No recent incidents</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Pending Actions</h5>
                </div>
                <div class="card-body">
                    @if($pendingIncidents->count() > 0)
                        @foreach($pendingIncidents as $incident)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $incident->incident_number }}</h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($incident->location, 30) }}</p>
                                        <small class="text-warning">
                                            <i class="fas fa-clock me-1"></i>{{ $incident->incident_datetime->diffForHumans() }}
                                        </small>
                                    </div>
                                    <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @if($pendingIncidents->count() > 5)
                            <div class="text-center">
                                <a href="{{ route('incidents.index') }}?status=pending" class="btn btn-outline-primary btn-sm">
                                    View All Pending
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted small">All incidents have been addressed!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('incidents.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Report New Incident
                        </a>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>View All Incidents
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-map-marked-alt me-2"></i>View Heat Map
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Incident Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['labels']),
            datasets: [{
                label: 'Incidents',
                data: @json($monthlyData['data']),
                borderColor: 'rgb(107, 118, 113)',
                backgroundColor: 'rgba(107, 118, 113, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Incident Type Distribution Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($typeDistribution['labels']),
            datasets: [{
                data: @json($typeDistribution['data']),
                backgroundColor: [
                    'rgb(107, 118, 113)',
                    'rgba(107, 118, 113, 0.8)',
                    'rgba(107, 118, 113, 0.6)',
                    'rgba(107, 118, 113, 0.4)',
                    'rgba(107, 118, 113, 0.2)',
                    'rgb(139, 115, 85)',
                    'rgba(139, 115, 85, 0.8)',
                    'rgba(139, 115, 85, 0.6)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        // Update statistics without full page reload
        // This could be implemented with AJAX calls
    }, 300000);
});
</script>
@endpush
