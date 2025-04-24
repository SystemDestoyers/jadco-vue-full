<?php

namespace App\Repositories;

use App\Models\Page;
use App\Models\Section;

class PageRepository
{
    public function getPageBySlug($slug)
    {
        return Page::where('slug', $slug)->firstOrFail();
    }

    public function getPageSections($pageId)
    {
        return Section::where('page_id', $pageId)
            ->orderBy('order')
            ->get();
    }

    public function getSectionsByName($name)
    {
        return Section::where('name', $name)
            ->orderBy('order')
            ->get();
    }

    public function getPageSectionsByName($pageId, $name)
    {
        return Section::where('page_id', $pageId)
            ->where('name', $name)
            ->orderBy('order')
            ->get();
    }

    public function getHomeSections()
    {
        $page = $this->getPageBySlug('home');
        return $this->getPageSections($page->id);
    }

    public function getHeaderSections()
    {
        return $this->getSectionsByName('header');
    }

    public function getContactSections()
    {
        return $this->getSectionsByName('contact');
    }

    public function getSectionByName($name)
    {
        return Section::where('name', $name)->first();
    }
} 