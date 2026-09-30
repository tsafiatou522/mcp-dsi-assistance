<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BureauController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\InterventionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProblemeController;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('directions', DirectionController::class);
    Route::apiResource('bureaux', BureauController::class);
    Route::apiResource('categories', CategorieController::class);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/problemes', [ProblemeController::class, 'index']);
    Route::post('/problemes', [ProblemeController::class, 'store']);
    Route::put('/problemes/{probleme}', [ProblemeController::class, 'update']);

    Route::get('/demandes', [DemandeController::class, 'index']);
    Route::post('/demandes', [DemandeController::class, 'store']);
    Route::get('/demandes/export', [DemandeController::class, 'export']);
    Route::get('/demandes/{demande}', [DemandeController::class, 'show']);
    Route::get('/demandes/{demande}/historique', [DemandeController::class, 'historique']);
    Route::get('/demandes/{demande}/commentaires', [DemandeController::class, 'commentaires']);
    Route::post('/demandes/{demande}/commentaires', [DemandeController::class, 'ajouterCommentaire']);
    Route::post('/demandes/{demande}/prendre-en-charge', [DemandeController::class, 'prendreEnCharge']);
    Route::put('/demandes/{demande}/affecter', [DemandeController::class, 'affecter']);

    Route::post('/demandes/{demande}/interventions', [InterventionController::class, 'store']);
    Route::put('/interventions/{intervention}', [InterventionController::class, 'update']);
    Route::post('/interventions/{intervention}/resoudre', [InterventionController::class, 'resoudre']);
    Route::post('/demandes/{demande}/confirmer', [DemandeController::class, 'confirmer']);
    Route::post('/demandes/{demande}/signaler-persistance', [DemandeController::class, 'signalerPersistance']);
   
   Route::get('/users', [UserController::class, 'index']);
   Route::get('/users/{user}', [UserController::class, 'show']);
   Route::put('/ma-disponibilite', [UserController::class, 'updateDisponibilite']);
   Route::get('/utilisateurs-en-attente', [UserController::class, 'enAttente']);
   Route::post('/users/{user}/activer', [UserController::class, 'activer']);

  Route::get('/presences', [PresenceController::class, 'index']);
  Route::post('/presences', [PresenceController::class, 'store']);
  Route::post('/presences/{presence}/terminer', [PresenceController::class, 'terminer']);
  
  Route::get('/notifications', [UserController::class, 'notifications']);
  Route::post('/notifications/{notificationId}/lue', [UserController::class, 'marquerNotificationLue']);
  
  Route::get('/equipements', [EquipementController::class, 'index']);
    Route::post('/equipements', [EquipementController::class, 'store']);
    Route::get('/equipements/{equipement}', [EquipementController::class, 'show']);
    Route::put('/equipements/{equipement}', [EquipementController::class, 'update']);
    Route::delete('/equipements/{equipement}', [EquipementController::class, 'destroy']);
  });