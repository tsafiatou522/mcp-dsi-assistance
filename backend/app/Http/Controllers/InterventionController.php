<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Intervention;
use App\Models\DemandeHistorique;
use Illuminate\Http\Request;
use App\Notifications\DemandeResolueNotification;

class InterventionController extends Controller
{
    public function store(Request $request, Demande $demande)
    {
        if ($demande->technicien_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Seul le technicien assigné peut intervenir sur cette demande.',
            ], 403);
        }

        $validated = $request->validate([
            'diagnostic' => 'nullable|string',
            'actions_realisees' => 'nullable|string',
            'materiel_utilise' => 'nullable|string',
            'difficultes_rencontrees' => 'nullable|string',
            'solution_apportee' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $validated['demande_id'] = $demande->id;
        $validated['technicien_id'] = $request->user()->id;
        $validated['date_debut'] = now();

        $intervention = Intervention::create($validated);

        $demande->update(['statut' => 'en_intervention']);
        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Intervention démarrée',
        ]);

        return response()->json($intervention, 201);
    }

    public function update(Request $request, Intervention $intervention)
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Seul le technicien assigné peut modifier cette intervention.',
            ], 403);
        }

        $validated = $request->validate([
            'diagnostic' => 'nullable|string',
            'actions_realisees' => 'nullable|string',
            'materiel_utilise' => 'nullable|string',
            'difficultes_rencontrees' => 'nullable|string',
            'solution_apportee' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $intervention->update($validated);

        return response()->json($intervention);
    }

    public function resoudre(Request $request, Intervention $intervention)
{
    if ($intervention->technicien_id !== $request->user()->id) {
        return response()->json([
            'message' => 'Seul le technicien assigné peut déclarer la demande résolue.',
        ], 403);
    }

    $intervention->update(['date_resolution' => now()]);

    $demande = $intervention->demande;
    $demande->update(['statut' => 'en_attente_confirmation']);
    DemandeHistorique::create([
        'demande_id' => $demande->id,
        'user_id' => $request->user()->id,
        'action' => 'Intervention déclarée résolue',
    ]);
    $demande->load('agent', 'technicien');

    $demande->agent->notify(new DemandeResolueNotification($demande));

    return response()->json($intervention->load('demande'));
}
}