<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'technicien_id',
    'direction_id',
    'date_arrivee',
    'date_depart',
])]
class Presence extends Model
{
    use HasFactory;

    protected $casts = [
        'date_arrivee' => 'datetime',
        'date_depart' => 'datetime',
    ];

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }
}