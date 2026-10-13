<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('admin.users.index', compact('users'));
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Por seguridad, no puedes modificar el estado de tu propia cuenta activa.');
        }

        $user->is_active = !($user->is_active ?? true);
        $user->save();

        $statusMessage = $user->is_active ? 'activada' : 'desactivada';

        return redirect()->route('admin.users')
            ->with('success', "La cuenta del usuario ha sido {$statusMessage} con éxito.");
    }

    public function forceDelete($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Por seguridad, no puedes eliminar tu propia cuenta activa.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'La cuenta del usuario ha sido eliminada permanentemente.');
    }

    public function updateRole(Request $request, $id)
    {
    $user = User::findOrFail($id);

    if (Auth::id() === $user->id && $request->role !== 'admin') {
        return redirect()->route('admin.users')
            ->with('error', 'Por seguridad, no puedes quitarte el rol de administrador a ti mismo.');
    }

    $request->validate([
        'role' => 'required|string|in:admin,docente,student,user' 
    ]);

    $user->role = $request->role;
    $user->save();

    return redirect()->route('admin.users')
        ->with('success', "El rol de {$user->name} ha sido actualizado con éxito.");
    }
}