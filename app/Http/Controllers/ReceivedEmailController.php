<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceivedEmail;

class ReceivedEmailController extends Controller
{
    public function index()
    {
        $emails = ReceivedEmail::latest('received_at')->paginate(10);
        return view('admin.emails.index', compact('emails'));
    }

    public function show($id)
    {
        $email = ReceivedEmail::findOrFail($id);

        if (!$email->is_read) {
            $email->update(['is_read' => true]);
        }

        return view('admin.emails.show', compact('email'));
    }
}