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
                ->with('error', 'Por seguridad, no puedes eliminar tu propia cuenta activa.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'La cuenta del usuario ha sido eliminada con éxito.');
    }
}