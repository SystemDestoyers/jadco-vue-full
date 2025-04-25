<?php

namespace App\Models;

use App\Models\Traits\FixesInvalidDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Section extends Model
{
    use HasFactory, FixesInvalidDates;

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

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            // Ensure timestamps are set to valid values (current year or past)
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$section->created_at) {
                $section->created_at = $now;
            } else if (Carbon::parse($section->created_at)->year > 2023) {
                $section->created_at = Carbon::parse($section->created_at)->setYear(2023);
            }
            
            if (!$section->updated_at) {
                $section->updated_at = $now;
            } else if (Carbon::parse($section->updated_at)->year > 2023) {
                $section->updated_at = Carbon::parse($section->updated_at)->setYear(2023);
            }
        });

        static::updating(function ($section) {
            // Ensure the updated_at timestamp is valid when updating
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$section->updated_at) {
                $section->updated_at = $now;
            } else if (Carbon::parse($section->updated_at)->year > 2023) {
                $section->updated_at = Carbon::parse($section->updated_at)->setYear(2023);
            }
        });
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function images()
    {
        return $this->hasMany(SectionImage::class);
    }
} 