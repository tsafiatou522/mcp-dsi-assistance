<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'titre', 'categorie', 'contenu', 'publie'];

    protected $casts = ['publie' => 'boolean'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}