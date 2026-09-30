<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
     use HasFactory;

    protected $fillable = ['nom', 'description'];

    public function bureaux()
    {
        return $this->hasMany(Bureau::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}
