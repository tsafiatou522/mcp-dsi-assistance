<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bureau extends Model
{
    use HasFactory;

    protected $table = 'bureaux';

    protected $fillable = ['nom', 'direction_id'];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}