<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    protected $table = 'lineas';

    public $timestamps = false;

    protected $fillable = [
        'tarjeta_id',
        'numero_linea',
        'n1',
        'n2',
        'n3',
        'n4',
        'n5',
        'n6',
        'n7',
        'n8',
        'n9',
        'n10',
    ];

    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class, 'tarjeta_id');
    }

    public function getNumeros()
    {
        return array_filter([
            $this->n1,
            $this->n2,
            $this->n3,
            $this->n4,
            $this->n5,
            $this->n6,
            $this->n7,
            $this->n8,
            $this->n9,
            $this->n10,
        ]);
    }
}
