<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    /** @use HasFactory<\Database\Factories\PetFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'species',
        'name',
        'breed',
        'color',
        'age',
        'weight',
        'description',
        'gender',
        'date_of_birth',
        'microchip_id',
        'insurance_policy_number',
        'is_vaccinated',
        'is_neutered',
        'profile_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}