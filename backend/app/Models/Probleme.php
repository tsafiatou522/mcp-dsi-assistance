<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Probleme extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'titre', 'description', 'cause_racine', 'solution', 'priorite', 'statut'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}