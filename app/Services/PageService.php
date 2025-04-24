<?php

namespace App\Services;

use App\Repositories\PageRepository;

class PageService
{
    protected $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function getPageSections($slug)
    {
        $page = $this->pageRepository->getPageBySlug($slug);
        return $this->pageRepository->getPageSections($page->id);
    }

    public function getHomeSections()
    {
        return $this->pageRepository->getHomeSections();
    }

    public function getPageBySlug($slug)
    {
        return $this->pageRepository->getPageBySlug($slug);
    }

    public function getHeaderSection()
    {
        return $this->pageRepository->getSectionByName('header');
    }

    public function getHeaderContent()
    {
        $sections = $this->pageRepository->getHeaderSections();
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }

    public function getContactSection()
    {
        return $this->pageRepository->getSectionByName('contact');
    }

    public function getContactContent()
    {
        $sections = $this->pageRepository->getContactSections();
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }

    public function getAboutContent()
    {
        $page = $this->pageRepository->getPageBySlug('about');
        $sections = $this->pageRepository->getPageSectionsByName($page->id, 'about');
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }
    
    public function getEducationContent()
    {
        $page = $this->pageRepository->getPageBySlug('education-and-scholarship');
       
        $sections = $this->pageRepository->getPageSections($page->id);
        // dd($sections);
        if ($sections->isNotEmpty()) {
            return $sections;
        }
        
        return [];
    }
    
    public function getTrainingContent()
    {
        $page = $this->pageRepository->getPageBySlug('training-and-professional-development');
        $sections = $this->pageRepository->getPageSections($page->id);
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }
    
    public function getAiContent()
    {
        $page = $this->pageRepository->getPageBySlug('ai-and-advanced-technologies');
        $sections = $this->pageRepository->getPageSections($page->id);
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }
    
    public function getEgamingContent()
    {
        $page = $this->pageRepository->getPageBySlug('egaming-and-esport');
        $sections = $this->pageRepository->getPageSections($page->id);
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }
    
    public function getArtsContent()
    {
        $page = $this->pageRepository->getPageBySlug('arts-and-entertainment');
        $sections = $this->pageRepository->getPageSections($page->id);
        
        if ($sections->isNotEmpty()) {
            return $sections->first()->content;
        }
        
        return [];
    }
} 