<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * List clients with search, filters, pagination, and stats
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $clients = Client::notDeleted()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('identification', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $statistics = Client::getStatistics();

        return view('clients.index', compact('clients', 'search', 'status', 'statistics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store new client
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'identification' => 'required|string|max:50|unique:clients,identification',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'email'          => 'required|email|max:255|unique:clients,email',
            'status'         => 'required|in:active,inactive',
        ], [
            'first_name.required'     => 'Los nombres son obligatorios.',
            'last_name.required'      => 'Los apellidos son obligatorios.',
            'identification.required' => 'La identificación es obligatoria.',
            'identification.unique'   => 'Ya existe un cliente con esta identificación.',
            'phone.required'          => 'El teléfono es obligatorio.',
            'address.required'        => 'La dirección es obligatoria.',
            'email.required'          => 'El correo electrónico es obligatorio.',
            'email.email'             => 'El correo electrónico no tiene un formato válido.',
            'email.unique'            => 'Ya existe un cliente con este correo.',
        ]);

        try {
            Client::create($request->only('first_name', 'last_name', 'identification', 'phone', 'address', 'email', 'status'));

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente.',
                'redirect' => route('clients.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el cliente.'], 500);
        }
    }

    /**
     * Show client details
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show edit form
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'identification' => ['required', 'string', 'max:50', Rule::unique('clients')->ignore($client->id)],
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'email'          => ['required', 'email', 'max:255', Rule::unique('clients')->ignore($client->id)],
            'status'         => 'required|in:active,inactive',
        ], [
            'first_name.required'     => 'Los nombres son obligatorios.',
            'last_name.required'      => 'Los apellidos son obligatorios.',
            'identification.required' => 'La identificación es obligatoria.',
            'identification.unique'   => 'Ya existe otro cliente con esta identificación.',
            'email.unique'            => 'Ya existe otro cliente con este correo.',
        ]);

        try {
            $client->update($request->only('first_name', 'last_name', 'identification', 'phone', 'address', 'email', 'status'));

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente.',
                'redirect' => route('clients.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
        }
    }

    /**
     * Soft delete client (status = 'deleted')
     */
    public function destroy(Client $client)
    {
        try {
            $client->update(['status' => 'deleted']);

            return response()->json(['success' => true, 'message' => 'Cliente eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    /**
     * Toggle status active/inactive
     */
    public function toggleStatus(Client $client)
    {
        try {
            $newStatus = $client->status === 'active' ? 'inactive' : 'active';
            $client->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente.',
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar el estado.'], 500);
        }
    }

    /**
     * AJAX search for clients (used by other modules like expertise)
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        if (strlen($term) < 2) {
            return response()->json(['success' => false, 'message' => 'Mínimo 2 caracteres.']);
        }

        $clients = Client::active()
            ->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('identification', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            })
            ->select('id', 'first_name', 'last_name', 'identification', 'email', 'phone')
            ->orderBy('first_name')
            ->limit($limit)
            ->get()
            ->map(fn($c) => array_merge($c->toArray(), ['full_name' => $c->full_name]));

        return response()->json(['success' => true, 'clients' => $clients]);
    }

    /**
     * Export clients to CSV
     */
    public function export(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $clients = Client::notDeleted()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('identification', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="clientes_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($clients) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            fputcsv($output, ['ID', 'Nombres', 'Apellidos', 'Identificación', 'Teléfono', 'Dirección', 'Correo', 'Estado', 'Creado', 'Actualizado']);

            foreach ($clients as $c) {
                fputcsv($output, [
                    $c->id, $c->first_name, $c->last_name, $c->identification,
                    $c->phone, $c->address, $c->email,
                    $c->status === 'active' ? 'Activo' : 'Inactivo',
                    $c->created_at->format('d/m/Y H:i'), $c->updated_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
