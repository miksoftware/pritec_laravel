<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List users with pagination and search
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $users = User::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'full_name' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'Este nombre de usuario ya existe.',
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => $request->password,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
                'redirect' => route('users.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario.',
            ], 500);
        }
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'username' => ['required', 'string', 'min:3', 'max:50', Rule::unique('users')->ignore($user->id)],
            'full_name' => 'required|string|min:2|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'status' => 'required|in:active,inactive',
        ];

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $request->validate($rules, [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'Este nombre de usuario ya existe.',
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            $data = [
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente.',
                'redirect' => route('users.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario.',
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        try {
            // Cannot delete yourself
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta.',
                ], 403);
            }

            // Admin protection is handled at model level (deleting event)
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        try {
            // Cannot toggle your own status
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes cambiar el estado de tu propia cuenta.',
                ], 403);
            }

            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del usuario actualizado exitosamente.',
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del usuario.',
            ], 500);
        }
    }
}
