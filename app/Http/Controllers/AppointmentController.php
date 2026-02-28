<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'upcoming');

        $query = Appointment::with(['client', 'user']);

        if ($filter === 'upcoming') {
            $query->upcoming();
        } elseif ($filter === 'today') {
            $query->whereDate('appointment_date', now()->toDateString())
                ->orderBy('appointment_time');
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed')->latest('appointment_date');
        } elseif ($filter === 'cancelled') {
            $query->where('status', 'cancelled')->latest('appointment_date');
        } else {
            $query->latest('appointment_date')->latest('appointment_time');
        }

        $appointments = $query->paginate(15)->withQueryString();

        $todayCount = Appointment::whereDate('appointment_date', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])->count();
        $upcomingCount = Appointment::upcoming()->count();

        return view('appointments.index', compact('appointments', 'filter', 'todayCount', 'upcomingCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'vehicle_description' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        Appointment::create([
            'client_id' => $request->client_id,
            'user_id' => Auth::id(),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'vehicle_description' => $request->vehicle_description,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Cita agendada exitosamente.']);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json(['success' => true, 'message' => 'Cita eliminada.']);
    }

    public function storeClient(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'identification' => 'required|string|max:50|unique:clients,identification',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $client = Client::create([
            'first_name' => strtoupper($request->first_name),
            'last_name' => strtoupper($request->last_name),
            'identification' => $request->identification,
            'phone' => $request->phone,
            'email' => $request->email ?? '',
            'address' => $request->address ?? '',
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente.',
            'client' => $client,
        ]);
    }

    public function searchClients(Request $request)
    {
        $term = $request->get('q', '');
        $clients = Client::where('status', 'active')
            ->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('identification', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'identification', 'phone']);

        return response()->json($clients);
    }
}
