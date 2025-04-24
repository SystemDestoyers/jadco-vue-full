<?php

namespace App\Services;

use App\Repositories\ServiceRepository;

class ServiceService
{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices()
    {
        return $this->serviceRepository->getAllServices();
    }

    public function getFeaturedServices()
    {
        return $this->serviceRepository->getFeaturedServices();
    }

    public function getServiceBySlug($slug)
    {
        return $this->serviceRepository->getServiceBySlug($slug);
    }

    public function getServiceWithSections($slug)
    {
        return $this->serviceRepository->getServiceWithSections($slug);
    }
} 