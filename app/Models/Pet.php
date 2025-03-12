<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'file_path',
        'profile_image_url',
        'qr_code_url',
        'qr_code_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scopeFilter(Builder $query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('species', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('breed', 'LIKE', "%{$search}%")
                ->orWhere('color', 'LIKE', "%{$search}%")
                ->orWhere('age', 'LIKE', "%{$search}%")
                ->orWhere('weight', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('gender', 'LIKE', "%{$search}%")
                ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                ->orWhere('microchip_id', 'LIKE', "%{$search}%")
                ->orWhere('insurance_policy_number', 'LIKE', "%{$search}%");
        });
    }

    protected static function boot()
    {
        parent::boot();

        // Delete the associated image file when the pet is force deleted
        static::forceDeleting(function ($pet) {
            if ($pet->file_path) {
                // \Log::info("Deleting file: {$pet->file_path}");
                Storage::disk()->delete($pet->file_path);
            }

            // Delete QR code if exists
            if ($pet->qr_code_path) {
                // \Log::info("Deleting QR code: {$pet->qr_code_path}");
                Storage::disk()->delete($pet->qr_code_path);
            }
        });
    }
}