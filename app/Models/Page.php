<?php

namespace App\Models;

use App\Models\Traits\FixesInvalidDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Page extends Model
{
    use HasFactory, FixesInvalidDates;

    protected $fillable = [
        'name',
        'slug',
        'template',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            // Ensure timestamps are set to valid values (current year or past)
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$page->created_at) {
                $page->created_at = $now;
            } else if (Carbon::parse($page->created_at)->year > 2023) {
                $page->created_at = Carbon::parse($page->created_at)->setYear(2023);
            }
            
            if (!$page->updated_at) {
                $page->updated_at = $now;
            } else if (Carbon::parse($page->updated_at)->year > 2023) {
                $page->updated_at = Carbon::parse($page->updated_at)->setYear(2023);
            }
        });

        static::updating(function ($page) {
            // Ensure the updated_at timestamp is valid when updating
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$page->updated_at) {
                $page->updated_at = $now;
            } else if (Carbon::parse($page->updated_at)->year > 2023) {
                $page->updated_at = Carbon::parse($page->updated_at)->setYear(2023);
            }
        });
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function getSectionsByName($name)
    {
        return $this->sections()->where('name', $name)->orderBy('order')->get();
    }
} 