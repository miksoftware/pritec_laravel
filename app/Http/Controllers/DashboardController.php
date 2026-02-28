<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expertise;
use App\Models\VehicleType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        // KPIs principales
        $totalExpertises = Expertise::count();
        $completedExpertises = Expertise::completed()->count();
        $inProgressExpertises = Expertise::inProgress()->count();
        $totalClients = Client::notDeleted()->count();
        $totalVehicleTypes = VehicleType::where('status', 'active')->count();
        $totalUsers = User::where('status', 'active')->count();

        // Este mes
        $expertisesThisMonth = Expertise::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)->count();
        $clientsThisMonth = Client::notDeleted()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)->count();

        // Hoy
        $expertisesToday = Expertise::whereDate('created_at', $now->toDateString())->count();

        // Últimos 5 peritajes
        $recentExpertises = Expertise::with(['client', 'user', 'vehicleType'])
            ->latest()
            ->take(5)
            ->get();

        // Últimos 5 clientes
        $recentClients = Client::notDeleted()->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalExpertises', 'completedExpertises', 'inProgressExpertises',
            'totalClients', 'totalVehicleTypes', 'totalUsers',
            'expertisesThisMonth', 'clientsThisMonth', 'expertisesToday',
            'recentExpertises', 'recentClients'
        ));
    }
}
