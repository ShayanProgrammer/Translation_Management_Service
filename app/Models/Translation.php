<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'translations', 'tags'];
    protected $casts = [
        'translations' => 'array',
        'tags' => 'array',
    ];
}
