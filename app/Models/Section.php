<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'name',
        'type',
        'order',
        'is_active',
        'content'
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function images()
    {
        return $this->hasMany(SectionImage::class);
    }
} 