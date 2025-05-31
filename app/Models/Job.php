<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'created_jobs';
    protected $fillable = [
        'title',
        'description',
        'budget',
        'status',
        'paises_id',
        'provincia_id',
        'company_types_id',
        'client_id',
        'contractor_id'
    ];
}
