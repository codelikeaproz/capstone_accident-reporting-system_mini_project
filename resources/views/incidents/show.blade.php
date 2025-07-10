@extends('layouts.app')

@section('title', 'Incident #' . $incident->incident_number . ' - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Incident #{{ $incident->incident_number }}</h1>
            <p class="page-subtitle">{{ str_replace('_', ' ', \Illuminate\Support\Str::title($incident->incident_type)) }} - {{ $incident->location }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Incident
                </a>
                <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="printIncident()">
                        <i class="fas fa-print me-2"></i>Print Report</a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('incidents.index') }}">
                        <i class="fas fa-arrow-left me-2"></i>Back to List</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Main Incident Information -->
        <div class="col-lg-8">
            <!-- Status and Actions -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Status & Actions</h5>
                    <span class="badge fs-6 status-{{ $incident->status }}">
                        {{ ucfirst($incident->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Current Status</label>
                            <select class="form-select" id="statusUpdate" onchange="updateStatus()">
                                <option value="pending" {{ $incident->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="responding" {{ $incident->status == 'responding' ? 'selected' : '' }}>Responding</option>
                                <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $incident->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assigned Staff</label>
                            <select class="form-select" id="staffAssignment" onchange="updateAssignment()">
                                <option value="">Unassigned</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $incident->assigned_staff == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assigned Vehicle</label>
                            <select class="form-select" id="vehicleAssignment" onchange="updateVehicleAssignment()">
                                <option value="">No Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ $incident->assigned_vehicle == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} - {{ $vehicle->vehicle_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Incident Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Incident Type</label>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark fs-6">
                                    {{ str_replace('_', ' ', \Illuminate\Support\Str::title($incident->incident_type)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Severity Level</label>
                            <p class="mb-0">
                                <span class="badge fs-6
                                    @if($incident->severity_level == 'critical') bg-danger
                                    @elseif($incident->severity_level == 'severe') bg-warning
                                    @elseif($incident->severity_level == 'moderate') bg-info
                                    @else bg-secondary @endif">
                                    {{ ucfirst($incident->severity_level) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date & Time</label>
                            <p class="mb-0">{{ $incident->incident_datetime->format('F d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Reported By</label>
                            <p class="mb-0">{{ $incident->reportedBy->full_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Location</label>
                            <p class="mb-0">
                                <strong>{{ $incident->location }}</strong><br>
                                <small class="text-muted">{{ $incident->barangay }}, {{ $incident->municipality }}</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Coordinates</label>
                            <p class="mb-0">
                                @if($incident->latitude && $incident->longitude)
                                    {{ $incident->latitude }}, {{ $incident->longitude }}
                                    <a href="https://maps.google.com/?q={{ $incident->latitude }},{{ $incident->longitude }}"
                                       target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>View on Map
                                    </a>
                                @else
                                    <span class="text-muted">Not available</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Description</label>
                            <p class="mb-0">{{ $incident->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Information (if applicable) -->
            @if($incident->isVehicleRelated())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-car me-2"></i>Vehicle Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Vehicles Involved</label>
                                <p class="mb-0">{{ $incident->vehicles_involved ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Estimated Damage</label>
                                <p class="mb-0">
                                    @if($incident->estimated_damage)
                                        ₱{{ number_format($incident->estimated_damage, 2) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Weather Condition</label>
                                <p class="mb-0">{{ ucfirst($incident->weather_condition ?? 'Not specified') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Road Condition</label>
                                <p class="mb-0">{{ str_replace('_', ' ', \Illuminate\Support\Str::title($incident->road_condition ?? 'Not specified')) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Medical Information (if applicable) -->
            @if($incident->isMedicalEmergency())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Medical Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Hospital Destination</label>
                                <p class="mb-0">{{ $incident->hospital_destination ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Patient Condition</label>
                                <p class="mb-0">
                                    @if($incident->patient_condition)
                                        <span class="badge
                                            @if($incident->patient_condition == 'critical') bg-danger
                                            @elseif($incident->patient_condition == 'stable') bg-success
                                            @else bg-secondary @endif">
                                            {{ ucfirst($incident->patient_condition) }}
                                        </span>
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                            @if($incident->medical_notes)
                                <div class="col-12">
                                    <label class="form-label text-muted">Medical Notes</label>
                                    <p class="mb-0">{{ $incident->medical_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Victims/People Involved -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>People Involved</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="addVictim()">
                        <i class="fas fa-user-plus me-2"></i>Add Person
                    </button>
                </div>
                <div class="card-body">
                    @if($incident->victims->count() > 0)
                        <div class="row g-3">
                            @foreach($incident->victims as $victim)
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $victim->full_name }}</h6>
                                                <p class="text-muted mb-1">{{ ucfirst($victim->involvement_type) }}</p>
                                                <span class="badge
                                                    @if($victim->injury_status == 'fatal') bg-danger
                                                    @elseif($victim->injury_status == 'critical_condition') bg-warning
                                                    @elseif($victim->injury_status == 'serious_injury') bg-info
                                                    @else bg-success @endif">
                                                    {{ str_replace('_', ' ', \Illuminate\Support\Str::title($victim->injury_status)) }}
                                                </span>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="editVictim({{ $victim->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="removeVictim({{ $victim->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @if($victim->age)
                                            <small class="text-muted">Age: {{ $victim->age }}</small><br>
                                        @endif
                                        @if($victim->hospital_referred)
                                            <small class="text-muted">Hospital: {{ $victim->hospital_referred }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No people have been added to this incident yet.</p>
                            <button class="btn btn-primary" onclick="addVictim()">
                                <i class="fas fa-user-plus me-2"></i>Add First Person
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $incident->casualties_count }}</h4>
                            <small class="text-muted">Casualties</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $incident->injuries_count }}</h4>
                            <small class="text-muted">Injuries</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Incident Reported</h6>
                                <small class="text-muted">{{ $incident->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        @if($incident->assigned_staff)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Staff Assigned</h6>
                                    <small class="text-muted">{{ $incident->assignedStaff->full_name }}</small>
                                </div>
                            </div>
                        @endif
                        @if($incident->status == 'resolved')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Incident Resolved</h6>
                                    <small class="text-muted">{{ $incident->updated_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts -->
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-phone fa-2x text-danger mb-2"></i>
                    <h6>Emergency Hotline</h6>
                    <p class="mb-0"><strong>911</strong> or <strong>(088) 123-4567</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Victim Modal -->
<div class="modal fade" id="victimModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Person</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="victimForm">
                    <input type="hidden" id="victimId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" min="0" max="150">
                        </div>
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender">
                                <option value="">Not specified</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" placeholder="09xxxxxxxxx">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="involvement_type" class="form-label">Involvement Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="involvement_type" required>
                                <option value="">Select involvement</option>
                                <option value="driver">Driver</option>
                                <option value="passenger">Passenger</option>
                                <option value="pedestrian">Pedestrian</option>
                                <option value="witness">Witness</option>
                                <option value="patient">Patient</option>
                                <option value="expectant_mother">Expectant Mother</option>
                                <option value="victim">Victim</option>
                                <option value="property_owner">Property Owner</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="injury_status" class="form-label">Injury Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="injury_status" required>
                                <option value="">Select status</option>
                                <option value="none">No Injury</option>
                                <option value="minor_injury">Minor Injury</option>
                                <option value="serious_injury">Serious Injury</option>
                                <option value="critical_condition">Critical Condition</option>
                                <option value="in_labor">In Labor</option>
                                <option value="gunshot_wound">Gunshot Wound</option>
                                <option value="stab_wound">Stab Wound</option>
                                <option value="fatal">Fatal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="hospital_referred" class="form-label">Hospital Referred</label>
                            <input type="text" class="form-control" id="hospital_referred" placeholder="Hospital name">
                        </div>
                        <div class="col-md-6">
                            <label for="transport_method" class="form-label">Transport Method</label>
                            <select class="form-select" id="transport_method">
                                <option value="">Not specified</option>
                                <option value="ambulance">Ambulance</option>
                                <option value="private_vehicle">Private Vehicle</option>
                                <option value="motorcycle">Motorcycle</option>
                                <option value="helicopter">Helicopter</option>
                                <option value="walk_in">Walk-in</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="medical_notes" class="form-label">Medical Notes</label>
                            <textarea class="form-control" id="medical_notes" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVictim()">Save Person</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-300);
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: 12px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--gray-300);
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    color: var(--gray-800);
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus() {
    const status = document.getElementById('statusUpdate').value;
    updateIncidentField('status', status);
}

function updateAssignment() {
    const staffId = document.getElementById('staffAssignment').value;
    updateIncidentField('assigned_staff', staffId);
}

function updateVehicleAssignment() {
    const vehicleId = document.getElementById('vehicleAssignment').value;
    updateIncidentField('assigned_vehicle', vehicleId);
}

function updateIncidentField(field, value) {
    fetch(`/incidents/{{ $incident->id }}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            field: field,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message || 'Updated successfully');
        } else {
            showAlert('danger', data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Update failed');
    });
}

function addVictim() {
    document.getElementById('victimForm').reset();
    document.getElementById('victimId').value = '';
    document.querySelector('#victimModal .modal-title').textContent = 'Add Person';
    const modal = new bootstrap.Modal(document.getElementById('victimModal'));
    modal.show();
}

function editVictim(victimId) {
    // Load victim data and populate form
    // Implementation would fetch victim data and populate the form
    document.querySelector('#victimModal .modal-title').textContent = 'Edit Person';
    const modal = new bootstrap.Modal(document.getElementById('victimModal'));
    modal.show();
}

function saveVictim() {
    const formData = new FormData();
    const fields = ['first_name', 'last_name', 'age', 'gender', 'contact_number', 'address',
                   'involvement_type', 'injury_status', 'hospital_referred', 'transport_method', 'medical_notes'];

    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element && element.value) {
            formData.append(field, element.value);
        }
    });

    formData.append('incident_id', {{ $incident->id }});

    const victimId = document.getElementById('victimId').value;
    const url = victimId ? `/victims/${victimId}` : '/victims';
    const method = victimId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showAlert('danger', data.message || 'Save failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Save failed');
    });
}

function removeVictim(victimId) {
    if (confirm('Are you sure you want to remove this person from the incident?')) {
        fetch(`/victims/${victimId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlert('danger', data.message || 'Remove failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Remove failed');
        });
    }
}

function printIncident() {
    window.print();
}

function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.main-content .container');
    container.insertBefore(alert, container.firstChild);

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush
