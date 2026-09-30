<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipement::with(['direction', 'bureau']);

        if ($request->has('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('etat')) {
            $query->where('etat', $request->etat);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:ordinateur,imprimante,scanner,reseau,autre',
            'numero_serie' => 'nullable|string|max:255',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'direction_id' => 'required|exists:directions,id',
            'bureau_id' => 'nullable|exists:bureaux,id',
            'etat' => 'nullable|in:fonctionnel,en_panne,en_maintenance,hors_service',
            'date_acquisition' => 'nullable|date',
        ]);

        $equipement = Equipement::create($validated);

        return response()->json($equipement->load(['direction', 'bureau']), 201);
    }

    public function show(Equipement $equipement)
    {
        return $equipement->load(['direction', 'bureau', 'demandes']);
    }

    public function update(Request $request, Equipement $equipement)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:ordinateur,imprimante,scanner,reseau,autre',
            'numero_serie' => 'nullable|string|max:255',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'direction_id' => 'sometimes|required|exists:directions,id',
            'bureau_id' => 'nullable|exists:bureaux,id',
            'etat' => 'nullable|in:fonctionnel,en_panne,en_maintenance,hors_service',
            'date_acquisition' => 'nullable|date',
        ]);

        $equipement->update($validated);

        return response()->json($equipement->load(['direction', 'bureau']));
    }

    public function destroy(Equipement $equipement)
    {
        $equipement->delete();

        return response()->json(null, 204);
    }
}
