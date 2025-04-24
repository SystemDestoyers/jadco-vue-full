<?php

namespace App\Repositories;

use App\Models\Service;

class ServiceRepository
{
    public function getAllServices()
    {
        return Service::orderBy('order')->get();
    }

    public function getFeaturedServices()
    {
        return Service::where('is_featured', true)
            ->orderBy('order')
            ->get();
    }

    public function getServiceBySlug($slug)
    {
        return Service::where('slug', $slug)->firstOrFail();
    }

    public function getServiceWithSections($slug)
    {
        return Service::where('slug', $slug)
            ->with('sections')
            ->firstOrFail();
    }
} 