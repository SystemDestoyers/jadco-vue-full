<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function getHomeSections()
    {
        $sections = $this->pageService->getHomeSections();
        return response()->json([
            'sections' => $sections
        ]);
    }

    public function getPageSections($slug)
    {
        $sections = $this->pageService->getPageSections($slug);
        return response()->json([
            'sections' => $sections
        ]);
    }

    public function getHeaderContent()
    {
        // Get header section from database
        $headerSection = $this->pageService->getHeaderSection();
        
        // Return the entire section with content
        return response()->json([
            'success' => true,
            'data' => $headerSection
        ]);
    }

    public function getContactContent()
    {
        // Get contact section from database
        $contactSection = $this->pageService->getContactSection();
        
        // Return the entire section with content
        return response()->json([
            'success' => true,
            'data' => $contactSection
        ]);
    }

    public function getAboutContent()
    {
        $content = $this->pageService->getAboutContent();
        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }
} 