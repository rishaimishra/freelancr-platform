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

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function getCompanyTypeNames()
    {
        if (empty($this->company_types)) {
            return [];
        }

        return CompanyType::whereIn('id', $this->company_types)
            ->pluck('name')
            ->toArray();
    }

    // In your Job model
    public function getBudgetMinAttribute()
    {
        return match ((int) $this->budget) {
            1 => 0,
            2 => 5000,
            3 => 15000,
            default => null,
        };
    }

    public function getBudgetMaxAttribute()
    {
        return match ((int) $this->budget) {
            1 => 5000,
            2 => 15000,
            3 => null, // No upper limit for "15000+"
            default => null,
        };
    }
}