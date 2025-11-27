<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tarjeta;

class Juego extends Model
{
    protected $table = 'juegos';

    protected $fillable = [
        'codigo',
        'estado',
        'plataforma',
        'numeros_sorteados',
        'tarjeta_ganadora_id',
    ];

    protected $casts = [
        'numeros_sorteados' => 'array',
    ];

    public function tarjetaGanadora ()
    {
        return $this->belongsTo(Tarjeta::class, 'tarjeta_ganadora_id');
    }

    public function tarjetas ()
    {
        return $this->hasMany(Tarjeta::class, 'juego_id');
    }
}
