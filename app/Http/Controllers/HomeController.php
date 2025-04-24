<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {

        // Get home page sections
        $sections = $this->pageService->getHomeSections();

        
        // Return the home view with sections data
        return view('frontend.pages.home', compact('sections'));
    }
} 