<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'template',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function getSectionsByName($name)
    {
        return $this->sections()->where('name', $name)->orderBy('order')->get();
    }
} 