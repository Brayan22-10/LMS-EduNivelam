<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminTeacherController extends Controller
{
    public function index()
    {
        $pendingTeachers = User::where('is_approved', false)
                               ->where(function ($query) {
                                   $query->where('role', 'teacher')
                                         ->orWhereNotNull('teaching_type');
                               })
                               ->orderBy('created_at', 'desc')
                               ->get();
                               
        return view('admin.teachers-pending', compact('pendingTeachers'));
    }

    public function approve($id)
    {
        $teacher = User::where('is_approved', false)->where('id', $id)->firstOrFail();
        
        $tempPassword = Str::password(10);
        
        $teacher->password = Hash::make($tempPassword);
        $teacher->role = 'teacher'; 
        $teacher->is_approved = true;
        $teacher->is_active = true;
        $teacher->email_verified_at = now();
        $teacher->save();

        Mail::raw("Hola {$teacher->name},\n\nTu solicitud como docente en EduNivelam ha sido APROBADA.\n\nTus credenciales de acceso son:\nCorreo: {$teacher->email}\nContraseña Temporal: {$tempPassword}\n\nPor favor ingresa a la plataforma y cambia tu contraseña desde tu panel.", function ($message) use ($teacher) {
            $message->to($teacher->email)
                    ->subject('¡Cuenta de Docente Aprobada - EduNivelam!');
        });

        return back()->with('success', "¡Docente {$teacher->name} aprobado con éxito! Credenciales enviadas por correo.");
    }

    public function deny($id)
    {
        $teacher = User::where('is_approved', false)->where('id', $id)->firstOrFail();
        
        $teacherName = $teacher->name;

        Mail::raw("Hola {$teacherName},\n\nLamentamos informarte que tu solicitud de registro como docente en EduNivelam ha sido denegada por el administrador.", function ($message) use ($teacher) {
            $message->to($teacher->email)
                    ->subject('Solicitud de Docente Denegada - EduNivelam');
        });

        $teacher->delete();

        return back()->with('info', "La solicitud de {$teacherName} ha sido denegada y su información fue eliminada por completo.");
    }
}