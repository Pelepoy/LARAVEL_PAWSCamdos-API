<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        'file_name',
        'profile_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    protected static function boot()
    {
    parent::boot();

    // Delete the associated image file when the pet is force deleted
    static::forceDeleting(function ($pet) {
        if ($pet->file_name) {
            // Log::info("Deleting file: {$pet->file_name}");
            Storage::disk()->delete($pet->file_name);
            }
        });
    }
}