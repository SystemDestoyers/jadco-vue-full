<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            [
                'name' => 'Home',
                'slug' => 'home',
                'template' => 'home',
            ],
            [
                'name' => 'About',
                'slug' => 'about',
                'template' => 'about',
            ],
            [
                'name' => 'Services',
                'slug' => 'services',
                'template' => 'services',
            ],
            [
                'name' => 'Contact',
                'slug' => 'contact',
                'template' => 'contact',
            ],
            [
                'name' => 'Education & Scholarship',
                'slug' => 'education-and-scholarship',
                'template' => 'service-detail',
            ],
            [
                'name' => 'Training & Professional Development',
                'slug' => 'training-and-professional-development',
                'template' => 'service-detail',
            ],
            [
                'name' => 'AI & Advanced Technologies',
                'slug' => 'ai-and-advanced-technologies',
                'template' => 'service-detail',
            ],
            [
                'name' => 'E-Gaming & eSport',
                'slug' => 'egaming-and-esport',
                'template' => 'service-detail',
            ],
            [
                'name' => 'Arts & Entertainment',
                'slug' => 'arts-and-entertainment',
                'template' => 'service-detail',
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
} 