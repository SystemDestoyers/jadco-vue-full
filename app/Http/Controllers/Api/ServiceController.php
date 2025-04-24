<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function getAllServices()
    {
        $services = $this->serviceService->getAllServices();
        return response()->json([
            'services' => $services
        ]);
    }

    public function getFeaturedServices()
    {
        $services = $this->serviceService->getFeaturedServices();
        return response()->json([
            'services' => $services
        ]);
    }

    public function getServiceBySlug($slug)
    {
        $service = $this->serviceService->getServiceBySlug($slug);
        return response()->json([
            'service' => $service
        ]);
    }

    public function getServiceWithSections($slug)
    {
        $service = $this->serviceService->getServiceWithSections($slug);
        return response()->json([
            'service' => $service
        ]);
    }
} 