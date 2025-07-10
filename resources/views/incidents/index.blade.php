@extends('layouts.app')

@section('title', 'Incident Management - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Incident Management</h1>
            <p class="page-subtitle">Monitor and manage emergency incidents in Maramag, Bukidnon</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Report New Incident
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="container">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">{{ $stats['total'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Incidents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">{{ $stats['pending'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-ambulance fa-2x" style="color: var(--primary-500);" mb-2"></i>
                    <h4 class="mb-1">{{ $stats['responding'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Responding</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">{{ $stats['resolved'] ?? 0 }}</h4>
                    <p class="text-muted mb-0">Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Search</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('incidents.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Incident number, location...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="responding" {{ request('status') == 'responding' ? 'selected' : '' }}>Responding</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label">Incident Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="vehicle_vs_vehicle" {{ request('type') == 'vehicle_vs_vehicle' ? 'selected' : '' }}>Vehicle vs Vehicle</option>
                            <option value="vehicle_vs_pedestrian" {{ request('type') == 'vehicle_vs_pedestrian' ? 'selected' : '' }}>Vehicle vs Pedestrian</option>
                            <option value="vehicle_vs_animals" {{ request('type') == 'vehicle_vs_animals' ? 'selected' : '' }}>Vehicle vs Animals</option>
                            <option value="vehicle_vs_property" {{ request('type') == 'vehicle_vs_property' ? 'selected' : '' }}>Vehicle vs Property</option>
                            <option value="vehicle_alone" {{ request('type') == 'vehicle_alone' ? 'selected' : '' }}>Vehicle Alone</option>
                            <option value="maternity" {{ request('type') == 'maternity' ? 'selected' : '' }}>Maternity</option>
                            <option value="stabbing_shooting" {{ request('type') == 'stabbing_shooting' ? 'selected' : '' }}>Stabbing/Shooting</option>
                            <option value="transport_to_hospital" {{ request('type') == 'transport_to_hospital' ? 'selected' : '' }}>Transport to Hospital</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="severity" class="form-label">Severity</label>
                        <select class="form-select" id="severity" name="severity">
                            <option value="">All Severities</option>
                            <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                            <option value="moderate" {{ request('severity') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="severe" {{ request('severity') == 'severe' ? 'selected' : '' }}>Severe</option>
                            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_range" class="form-label">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Incidents List</h5>
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF</a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-file-excel me-2"></i>Export Excel</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if($incidents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Incident #</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Date/Time</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidents as $incident)
                                <tr>
                                    <td>
                                        <strong>{{ $incident->incident_number }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                                                                            {{ str_replace('_', ' ', \Illuminate\Support\Str::title($incident->incident_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $incident->location }}</strong><br>
                                            <small class="text-muted">{{ $incident->barangay }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $incident->incident_datetime->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $incident->incident_datetime->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($incident->severity_level == 'critical') bg-danger
                                            @elseif($incident->severity_level == 'severe') bg-warning
                                            @elseif($incident->severity_level == 'moderate') bg-info
                                            @else bg-secondary @endif">
                                            {{ ucfirst($incident->severity_level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $incident->status }}">
                                            {{ ucfirst($incident->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($incident->assignedStaff)
                                            <small>{{ $incident->assignedStaff->full_name }}</small>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($incident->status == 'pending')
                                                <button type="button" class="btn btn-outline-info" title="Assign"
                                                        onclick="assignIncident({{ $incident->id }})">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $incidents->firstItem() }} to {{ $incidents->lastItem() }} of {{ $incidents->total() }} incidents
                        </small>
                    </div>
                    <div>
                        {{ $incidents->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>No incidents found</h5>
                    <p class="text-muted">Try adjusting your filter criteria or report a new incident.</p>
                    <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Report New Incident
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Incident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <input type="hidden" id="assignIncidentId">
                    <div class="mb-3">
                        <label for="assignStaff" class="form-label">Assign to Staff</label>
                        <select class="form-select" id="assignStaff" required>
                            <option value="">Select Staff Member</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }} - {{ $member->position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignVehicle" class="form-label">Assign Vehicle (Optional)</label>
                        <select class="form-select" id="assignVehicle">
                            <option value="">No Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} - {{ $vehicle->vehicle_type }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAssignment()">Assign</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function assignIncident(incidentId) {
    document.getElementById('assignIncidentId').value = incidentId;
    const modal = new bootstrap.Modal(document.getElementById('assignModal'));
    modal.show();
}

function confirmAssignment() {
    const incidentId = document.getElementById('assignIncidentId').value;
    const staffId = document.getElementById('assignStaff').value;
    const vehicleId = document.getElementById('assignVehicle').value;

    if (!staffId) {
        alert('Please select a staff member');
        return;
    }

    fetch(`/incidents/${incidentId}/assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            staff_id: staffId,
            vehicle_id: vehicleId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Assignment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Assignment failed');
    });
}
</script>
@endpush
