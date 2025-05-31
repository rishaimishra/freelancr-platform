<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';

    protected $fillable = [
        'nombre',
        'fk_pais',
        'cod'
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
}
