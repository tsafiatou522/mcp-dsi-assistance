<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        return Article::with('auteur:id,name')
            ->where('publie', true)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('titre', 'like', "%{$search}%")
                        ->orWhere('contenu', 'like', "%{$search}%")
                        ->orWhere('categorie', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['responsable', 'admin'], true)) {
            return response()->json(['message' => 'Création réservée aux responsables et administrateurs.'], 403);
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:180',
            'categorie' => 'nullable|string|max:100',
            'contenu' => 'required|string',
        ]);

        return response()->json(Article::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ])->load('auteur:id,name'), 201);
    }
}