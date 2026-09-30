<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nom',
    'type',
    'numero_serie',
    'marque',
    'modele',
    'direction_id',
    'bureau_id',
    'etat',
    'date_acquisition',
])]
class Equipement extends Model
{
    use HasFactory;

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function bureau()
    {
        return $this->belongsTo(Bureau::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}
