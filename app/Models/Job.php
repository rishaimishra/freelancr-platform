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
        'company_types', // Changed to store JSON array
        'client_id',
        'contractor_id'
    ];

   protected $casts = [
        'company_types' => 'array' // Auto-convert between JSON and array
    ];
    
    public function getCompanyTypeNames()
    {
        if (empty($this->company_types)) {
            return [];
        }
        
        return CompanyType::whereIn('id', $this->company_types)
                        ->pluck('name')
                        ->toArray();
    }
}