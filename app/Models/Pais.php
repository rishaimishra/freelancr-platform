<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'cod',
        'moneda'
    ];

    public function provincias()
    {
        return $this->hasMany(Provincia::class, 'pais_id');
    }
}
