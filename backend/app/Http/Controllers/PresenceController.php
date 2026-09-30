<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with(['technicien', 'direction'])->whereNull('date_depart');

        if ($request->has('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'direction_id' => 'required|exists:directions,id',
        ]);

        $existante = Presence::where('technicien_id', $request->user()->id)
            ->whereNull('date_depart')
            ->first();

        if ($existante) {
            return response()->json([
                'message' => 'Vous avez déjà une présence active. Signalez votre départ avant d\'en démarrer une nouvelle.',
            ], 409);
        }

        $presence = Presence::create([
            'technicien_id' => $request->user()->id,
            'direction_id' => $validated['direction_id'],
        ]);

        return response()->json($presence->load(['technicien', 'direction']), 201);
    }

    public function terminer(Presence $presence, Request $request)
    {
        if ($presence->technicien_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Seul le technicien concerné peut terminer cette présence.',
            ], 403);
        }

        $presence->update(['date_depart' => now()]);

        return response()->json($presence);
    }
}