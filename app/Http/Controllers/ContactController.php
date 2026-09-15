<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('page.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:160'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        return redirect()->route('contact')->with('success', '¡Mensaje enviado correctamente! Nuestro equipo de soporte académico se pondrá en contacto contigo pronto.');
    }
}