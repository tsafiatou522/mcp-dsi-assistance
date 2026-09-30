<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('direction');

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        if ($request->has('disponibilite')) {
            $query->where('disponibilite', $request->disponibilite);
        }

        return $query->get();
    }

    public function show(User $user)
    {
        return $user->load('direction');
    }

    public function updateDisponibilite(Request $request)
    {
        $validated = $request->validate([
            'disponibilite' => 'required|in:disponible,occupe,indisponible',
        ]);

        $request->user()->update($validated);

        return response()->json($request->user());
    }

    public function enAttente(Request $request)
{
    if ($request->user()->role !== 'admin') {
        return response()->json(['message' => 'Accès réservé aux administrateurs.'], 403);
    }

    return User::where('actif', false)->get();
}

public function activer(Request $request, User $user)
{
    if ($request->user()->role !== 'admin') {
        return response()->json(['message' => 'Accès réservé aux administrateurs.'], 403);
    }

    $user->update(['actif' => true]);

    return response()->json($user);
}

    public function notifications(Request $request)
{
    return $request->user()->notifications()->latest()->get();
}

public function marquerNotificationLue(Request $request, string $notificationId)
{
    $notification = $request->user()->notifications()->findOrFail($notificationId);
    $notification->markAsRead();

    return response()->json(['message' => 'Notification marquée comme lue.']);
}
}