<?php

namespace App\Models;

use App\Models\Traits\FixesInvalidDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, FixesInvalidDates;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'content',
        'is_featured',
        'order'
    ];

    protected $casts = [
        'content' => 'array',
        'is_featured' => 'boolean'
    ];

    public function sections()
    {
        return $this->hasMany(Section::class, 'page_id');
    }
} 