<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\DemandeCommentaire;
use App\Models\DemandeHistorique;
use App\Models\User;
use App\Notifications\NouvelleDemandeNotification;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::with(['agent', 'technicien', 'direction', 'bureau', 'categorie']);
        $user = $request->user();

        if ($user->role === 'agent') {
            $query->where('agent_id', $user->id);
        } elseif ($user->role === 'technicien') {
            $query->where(function ($query) use ($user) {
                $query->where('technicien_id', $user->id)
                    ->orWhere(function ($query) {
                        $query->whereNull('technicien_id')
                            ->where('statut', 'nouvelle');
                    });
            });
        } elseif ($user->role === 'responsable') {
            $query->where('direction_id', $user->direction_id);
        }

        if ($request->has('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        return $query->latest()->get();
    }

    public function export(Request $request)
    {
        if (! in_array($request->user()->role, ['responsable', 'admin'], true)) {
            return response()->json(['message' => 'Export réservé aux responsables et administrateurs.'], 403);
        }

        $demandes = Demande::with(['agent', 'technicien', 'direction', 'bureau', 'categorie'])
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($demandes) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Description', 'Statut', 'Priorité', 'Agent', 'Technicien', 'Direction', 'Bureau', 'Catégorie', 'Créée le', 'Échéance'], ';');

            foreach ($demandes as $demande) {
                fputcsv($handle, [
                    $demande->id,
                    $demande->description,
                    $demande->statut,
                    $demande->priorite,
                    $demande->agent?->name,
                    $demande->technicien?->name,
                    $demande->direction?->nom,
                    $demande->bureau?->nom,
                    $demande->categorie?->nom,
                    $demande->created_at?->format('d/m/Y H:i'),
                    $demande->date_echeance?->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);
        }, 'demandes-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'agent') {
            return response()->json(['message' => 'Seuls les agents peuvent créer une demande.'], 403);
        }

        $validated = $request->validate([
            'direction_id' => 'required|exists:directions,id',
            'bureau_id' => 'required|exists:bureaux,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
        ]);

        $validated['agent_id'] = $request->user()->id;
        $validated['statut'] = 'nouvelle';
        $delais = [
            'urgente' => 4,
            'haute' => 8,
            'normale' => 24,
            'basse' => 72,
        ];
        $validated['date_echeance'] = now()->addHours($delais[$validated['priorite']]);

        $demande = Demande::create($validated);
        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Demande créée',
        ]);
        $demande->load(['agent', 'direction', 'bureau', 'categorie']);

        $techniciens = User::where('role', 'technicien')
            ->where('actif', true)
            ->get();

        foreach ($techniciens as $technicien) {
            $technicien->notify(new NouvelleDemandeNotification($demande));
        }

        return response()->json($demande, 201);
    }

    public function show(Request $request, Demande $demande)
    {
        if (! $this->peutVoir($request, $demande)) {
            return response()->json(['message' => 'Accès refusé à cette demande.'], 403);
        }

        return $demande->load(['agent', 'technicien', 'direction', 'bureau', 'categorie', 'interventions']);
    }

    private function peutVoir(Request $request, Demande $demande): bool
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => true,
            'agent' => $demande->agent_id === $user->id,
            'technicien' => $demande->technicien_id === $user->id
                || ($demande->technicien_id === null && $demande->statut === 'nouvelle'),
            'responsable' => $demande->direction_id === $user->direction_id,
            default => false,
        };
    }

    public function historique(Request $request, Demande $demande)
    {
        abort_unless($this->peutVoir($request, $demande), 403);

        return $demande->historiques()->with('user:id,name,role')->get();
    }

    public function commentaires(Request $request, Demande $demande)
    {
        abort_unless($this->peutVoir($request, $demande), 403);

        return $demande->commentaires()->with('user:id,name,role')->get();
    }

    public function ajouterCommentaire(Request $request, Demande $demande)
    {
        abort_unless($this->peutVoir($request, $demande), 403);
        $validated = $request->validate([
            'contenu' => 'required|string|max:2000',
        ]);

        $commentaire = DemandeCommentaire::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'contenu' => $validated['contenu'],
        ]);

        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Commentaire ajouté',
        ]);

        return response()->json($commentaire->load('user:id,name,role'), 201);
    }

    public function prendreEnCharge(Request $request, Demande $demande)
    {
        if ($demande->technicien_id !== null) {
            return response()->json([
                'message' => 'Cette demande est déjà prise en charge.',
            ], 409);
        }

        $demande->update([
            'technicien_id' => $request->user()->id,
            'statut' => 'prise_en_charge',
        ]);

        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Demande prise en charge',
        ]);

        return response()->json($demande->load(['agent', 'technicien', 'direction', 'bureau', 'categorie']));
    }

    public function affecter(Request $request, Demande $demande)
    {
        if (! in_array($request->user()->role, ['responsable', 'admin'], true)) {
            return response()->json(['message' => 'Seuls un responsable ou un administrateur peuvent affecter une demande.'], 403);
        }

        if ($request->user()->role === 'responsable' && $demande->direction_id !== $request->user()->direction_id) {
            return response()->json(['message' => 'Cette demande ne dépend pas de votre direction.'], 403);
        }

        $validated = $request->validate([
            'technicien_id' => 'required|exists:users,id',
        ]);

        $technicien = User::where('id', $validated['technicien_id'])
            ->where('role', 'technicien')
            ->first();

        if (! $technicien) {
            return response()->json(['message' => 'L’utilisateur sélectionné n’est pas un technicien.'], 422);
        }

        $demande->update([
            'technicien_id' => $technicien->id,
            'statut' => $demande->statut === 'nouvelle' ? 'prise_en_charge' : $demande->statut,
        ]);

        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Demande affectée',
            'commentaire' => "Affectée à {$technicien->name}",
        ]);

        return response()->json($demande->load(['agent', 'technicien', 'direction', 'bureau', 'categorie']));
    }

    public function confirmer(Request $request, Demande $demande)
    {
        if ($demande->agent_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Seul l\'agent ayant créé la demande peut confirmer la résolution.',
            ], 403);
        }

        if ($demande->statut !== 'en_attente_confirmation') {
            return response()->json([
                'message' => 'Cette demande n\'est pas en attente de confirmation.',
            ], 409);
        }

        $demande->update(['statut' => 'cloturee']);
        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Résolution confirmée et demande clôturée',
        ]);

        return response()->json($demande->load(['agent', 'technicien', 'direction', 'bureau', 'categorie']));
    }

    public function signalerPersistance(Request $request, Demande $demande)
    {
        if ($demande->agent_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Seul l\'agent ayant créé la demande peut signaler la persistance du problème.',
            ], 403);
        }

        if ($demande->statut !== 'en_attente_confirmation') {
            return response()->json([
                'message' => 'Cette demande n\'est pas en attente de confirmation.',
            ], 409);
        }

        $demande->update(['statut' => 'en_intervention']);
        DemandeHistorique::create([
            'demande_id' => $demande->id,
            'user_id' => $request->user()->id,
            'action' => 'Problème toujours présent',
        ]);

        return response()->json($demande->load(['agent', 'technicien', 'direction', 'bureau', 'categorie']));
    }
}
