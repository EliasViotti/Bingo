<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Linea;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';

    protected $fillable = [
        'codigo',
        'nombre',
        'creada_en',
    ];

    protected $casts = [
        'creada_en' => 'datetime',
    ];

    public function lineas()
    {
        return $this->hasMany(Linea::class, 'tarjeta_id');
    }

    public function getNumeros()
    {
        return $this->lineas()
            ->get()
            ->flatMap(function ($linea) {
                return [
                    $linea->n1,
                    $linea->n2,
                    $linea->n3,
                    $linea->n4,
                    $linea->n5,
                    $linea->n6,
                    $linea->n7,
                    $linea->n8,
                    $linea->n9,
                    $linea->n10
                ];
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
