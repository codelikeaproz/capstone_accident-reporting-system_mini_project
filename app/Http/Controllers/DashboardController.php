<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Victim;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

final class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin,mdrrmo_staff']);
    }

    /**
     * Display the main dashboard
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get basic statistics
        $stats = $this->getOverviewStats();

        // Get recent incidents (last 7 days)
        $recentIncidents = $this->getRecentIncidents();

        // Get monthly data for charts
        $monthlyData = $this->getMonthlyIncidentData();

        // Get incident type distribution
        $typeDistribution = $this->getIncidentTypeDistribution();

        // Get vehicle status
        $vehicleStats = $this->getVehicleStats();

        // Get pending incidents (for admin/staff action)
        $pendingIncidents = Incident::where('status', 'pending')
            ->with(['reportedBy', 'assignedStaff'])
            ->orderBy('incident_datetime', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentIncidents',
            'monthlyData',
            'typeDistribution',
            'vehicleStats',
            'pendingIncidents',
            'user'
        ));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_incidents' => Incident::count(),
            'incidents_today' => Incident::whereDate('incident_datetime', $today)->count(),
            'incidents_this_month' => Incident::where('incident_datetime', '>=', $thisMonth)->count(),
            'pending_incidents' => Incident::where('status', 'pending')->count(),
            'responding_incidents' => Incident::where('status', 'responding')->count(),
            'resolved_incidents' => Incident::where('status', 'resolved')->count(),
            'total_casualties' => Incident::sum('casualties_count'),
            'total_injuries' => Incident::sum('injuries_count'),
            'total_vehicles' => Vehicle::count(),
            'available_vehicles' => Vehicle::where('status', 'available')->count(),
            'deployed_vehicles' => Vehicle::where('status', 'deployed')->count(),
            'total_staff' => User::where('role', 'mdrrmo_staff')->count(),
            'active_staff' => User::where('role', 'mdrrmo_staff')->where('is_active', true)->count(),
        ];
    }

    /**
     * Get recent incidents (last 7 days)
     */
    private function getRecentIncidents()
    {
        return Incident::with(['reportedBy', 'assignedStaff', 'assignedVehicle'])
            ->where('incident_datetime', '>=', Carbon::now()->subDays(7))
            ->orderBy('incident_datetime', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get monthly incident data for charts (last 6 months)
     */
    private function getMonthlyIncidentData(): array
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = Incident::whereYear('incident_datetime', $date->year)
                ->whereMonth('incident_datetime', $date->month)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get incident type distribution
     */
    private function getIncidentTypeDistribution(): array
    {
        $incidents = Incident::selectRaw('incident_type, COUNT(*) as count')
            ->groupBy('incident_type')
            ->get();

        $labels = [];
        $data = [];

        foreach ($incidents as $incident) {
                            $labels[] = str_replace('_', ' ', \Illuminate\Support\Str::title($incident->incident_type));
            $data[] = $incident->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get vehicle statistics
     */
    private function getVehicleStats(): array
    {
        $vehicles = Vehicle::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $stats = [
            'available' => 0,
            'deployed' => 0,
            'maintenance' => 0,
            'out_of_service' => 0
        ];

        foreach ($vehicles as $vehicle) {
            $stats[$vehicle->status] = $vehicle->count;
        }

        return $stats;
    }

    /**
     * Admin-specific dashboard
     */
    public function adminDashboard(Request $request): View
    {
        $stats = $this->getOverviewStats();
        $recentIncidents = $this->getRecentIncidents();
        $monthlyData = $this->getMonthlyIncidentData();
        $typeDistribution = $this->getIncidentTypeDistribution();
        $vehicleStats = $this->getVehicleStats();

        // Admin-specific data
        $recentUsers = User::where('role', 'mdrrmo_staff')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $systemActivity = [
            'total_logins_today' => 0, // Would be implemented with login tracking
            'active_sessions' => 0,    // Would be implemented with session tracking
        ];

        return view('dashboard.admin', compact(
            'stats',
            'recentIncidents',
            'monthlyData',
            'typeDistribution',
            'vehicleStats',
            'recentUsers',
            'systemActivity'
        ));
    }

    /**
     * Staff-specific dashboard
     */
    public function userDashboard(Request $request): View
    {
        $user = auth()->user();

        // Get incidents assigned to this staff member
        $myIncidents = Incident::where('assigned_staff', $user->id)
            ->with(['victims', 'assignedVehicle'])
            ->orderBy('incident_datetime', 'desc')
            ->limit(10)
            ->get();

        $myStats = [
            'total_assigned' => Incident::where('assigned_staff', $user->id)->count(),
            'pending_assigned' => Incident::where('assigned_staff', $user->id)->where('status', 'pending')->count(),
            'responding_assigned' => Incident::where('assigned_staff', $user->id)->where('status', 'responding')->count(),
            'resolved_assigned' => Incident::where('assigned_staff', $user->id)->where('status', 'resolved')->count(),
        ];

        // Get overall system stats (limited view)
        $systemStats = [
            'total_incidents' => Incident::count(),
            'incidents_today' => Incident::whereDate('incident_datetime', Carbon::today())->count(),
            'pending_incidents' => Incident::where('status', 'pending')->count(),
            'available_vehicles' => Vehicle::where('status', 'available')->count(),
        ];

        return view('dashboard.user', compact(
            'user',
            'myIncidents',
            'myStats',
            'systemStats'
        ));
    }

    /**
     * Get heat map data for incidents
     */
    public function getHeatMapData(Request $request)
    {
        $incidents = Incident::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', 'incident_type', 'severity_level', 'incident_datetime')
            ->get();

        $heatMapData = $incidents->map(function ($incident) {
            return [
                'lat' => (float) $incident->latitude,
                'lng' => (float) $incident->longitude,
                'intensity' => $this->getSeverityWeight($incident->severity_level),
                'type' => $incident->incident_type,
                'date' => $incident->incident_datetime->format('Y-m-d H:i:s')
            ];
        });

        return response()->json($heatMapData);
    }

    /**
     * Get severity weight for heat map intensity
     */
    private function getSeverityWeight(string $severity): float
    {
        return match ($severity) {
            'critical' => 1.0,
            'severe' => 0.8,
            'moderate' => 0.6,
            'minor' => 0.4,
            default => 0.5
        };
    }

    /**
     * Get chart data for API endpoints
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'monthly');

        switch ($type) {
            case 'monthly':
                return response()->json($this->getMonthlyIncidentData());

            case 'type_distribution':
                return response()->json($this->getIncidentTypeDistribution());

            case 'severity_trends':
                return response()->json($this->getSeverityTrends());

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Get severity trends for charts
     */
    private function getSeverityTrends(): array
    {
        $trends = Incident::selectRaw('severity_level, COUNT(*) as count')
            ->groupBy('severity_level')
            ->get();

        $labels = [];
        $data = [];

        foreach ($trends as $trend) {
            $labels[] = ucfirst($trend->severity_level);
            $data[] = $trend->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
