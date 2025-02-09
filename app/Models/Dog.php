<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dog extends Model
{
    /** @use HasFactory<\Database\Factories\DogFactory> */
    use HasFactory;

    protected $table = 'dogs_info';


    public function user() {
        return $this->belongsTo(User::class);
    }
}