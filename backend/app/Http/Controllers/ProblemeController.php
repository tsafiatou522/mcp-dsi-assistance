<?php

namespace App\Http\Controllers;

use App\Models\Probleme;
use Illuminate\Http\Request;

class ProblemeController extends Controller
{
    public function index()
    {
        return Probleme::with('auteur:id,name')->latest()->get();
    }

    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['responsable', 'admin'], true)) {
            return response()->json(['message' => 'Création réservée aux responsables et administrateurs.'], 403);
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:180',
            'description' => 'required|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
        ]);

        return response()->json(Probleme::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ])->load('auteur:id,name'), 201);
    }

    public function update(Request $request, Probleme $probleme)
    {
        if (!in_array($request->user()->role, ['responsable', 'admin'], true)) {
            return response()->json(['message' => 'Modification réservée aux responsables et administrateurs.'], 403);
        }

        $validated = $request->validate([
            'cause_racine' => 'nullable|string',
            'solution' => 'nullable|string',
            'statut' => 'required|in:ouvert,en_analyse,resolu,ferme',
        ]);

        $probleme->update($validated);
        return response()->json($probleme->load('auteur:id,name'));
    }
}