<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'demande_id',
    'technicien_id',
    'diagnostic',
    'actions_realisees',
    'materiel_utilise',
    'difficultes_rencontrees',
    'solution_apportee',
    'observations',
    'date_debut',
    'date_resolution',
])]
class Intervention extends Model
{
    use HasFactory;

    protected $casts = [
        'date_debut' => 'datetime',
        'date_resolution' => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}