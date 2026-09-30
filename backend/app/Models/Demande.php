<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'agent_id',
    'technicien_id',
    'direction_id',
    'bureau_id',
    'category_id',
    'equipement_id',
    'description',
    'priorite',
    'statut',
    'date_echeance',
    'piece_jointe',
])]
class Demande extends Model
{
    use HasFactory;

    protected $casts = [
        'date_echeance' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function bureau()
    {
        return $this->belongsTo(Bureau::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function historiques()
    {
        return $this->hasMany(DemandeHistorique::class)->latest();
    }

    public function commentaires()
    {
        return $this->hasMany(DemandeCommentaire::class)->latest();
    }
}
