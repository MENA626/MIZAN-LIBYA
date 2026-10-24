<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index()
    { 
        $consultations = Consultation::where('user_id', Auth::id())
            ->with('lawyer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.consultations', compact('consultations'));
    }
}