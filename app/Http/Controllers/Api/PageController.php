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
        $content = $this->pageService->getHeaderContent();
        return response()->json($content);
    }

    public function getContactContent()
    {
        $content = $this->pageService->getContactContent();
        return response()->json($content);
    }

    public function getAboutContent()
    {
        $content = $this->pageService->getAboutContent();
        return response()->json([
            'content' => $content
        ]);
    }
} 