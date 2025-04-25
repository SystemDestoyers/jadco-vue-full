<?php

namespace App\Models;

use App\Models\Traits\FixesInvalidDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionImage extends Model
{
    use HasFactory, FixesInvalidDates;

    protected $fillable = [
        'section_id',
        'path',
        'alt',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
} 