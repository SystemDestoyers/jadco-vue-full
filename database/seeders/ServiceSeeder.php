<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Section;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'id' => 1,
                'title' => 'Education & Scholarship',
                'slug' => 'education-and-scholarship',
                'description' => 'Comprehensive education and scholarship management services.',
                'image' => '/images/services/education.jpg',
                'hero_image' => '/images/services/education-hero.jpg',
                'is_featured' => true,
                'order' => 1,
                'sections' => [
                    [
                        'name' => 'scholarship',
                        'order' => 1,
                        'content' => [
                            'title' => 'Scholarship Programs Management',
                            'description' => 'With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor\'s vision and targets. Services include but not limited to:',
                            'image' => '/images/02_Education/01.jpg',
                            'services' => [
                                'Candidate Selection Criteria',
                                'Universities Selection Criteria',
                                'Program bylaws and regulations',
                                'Student Journey plan',
                                'Counseling & students development plans',
                                'Pre-Departure preparatory programs',
                                'Enroll students into international universities',
                                'Workshops, awareness sessions and summits',
                                'Pre travel and post travel logistics',
                                'Overseas continual support',
                                'And more'
                            ]
                        ]
                    ],
                    [
                        'name' => 'stem',
                        'order' => 2,
                        'content' => [
                            'title' => 'STEM Education and Innovation Centers',
                            'description' => 'Education is changing, STEM (Since, Technology, Engineering and Math), is the pathway to reshaping the ecosystem to rethinking and broadly reimagining. STEM is the future workforce skills, steps toward a new generation of innovators, creative leaders for now and the future.',
                            'image' => '/images/03_EDU.jpg',
                            'listTitle' => 'Our services including:',
                            'services' => [
                                'STEM Teacher Training.',
                                'STEM Students Training.',
                                'STEM Labs.',
                                'Summer STEM Camps.',
                                'Students Workshops.',
                                'Research and development.',
                                'Innovation Parks.',
                                'K-12 after school programs (Coding, Robotics, Drones, AI, Cybersecurity, AR & VR, 3D Printing & more).',
                                'Establishing new STEM Schools or Shift to STEM.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 2,
                'title' => 'Training & Professional Development',
                'slug' => 'training-and-professional-development',
                'description' => 'Professional training and development programs.',
                'image' => '/images/services/training.jpg',
                'hero_image' => '/images/services/training-hero.jpg',
                'is_featured' => true,
                'order' => 2,
                'sections' => [
                    [
                        'name' => 'fellowship',
                        'order' => 1,
                        'content' => [
                            'title' => 'Fellowship, Internship and Work Experience',
                            'description' => 'For a new talented and future skilled generation, we draw the map and open the door for understanding the contemporary workplace and environment that promote ready to innovate employees.',
                            'image' => '/images/03_Trining/01.jpg',
                            'mainContent' => 'JADCO, in association with prestigious organizations from all sizes, take your personal and professional development in an international company in a global city to learn new skills, reskill or upskill yourself and accelerate your career advancement.<br><br>Gain experience, new skills, network, knowledge, culture, friends and prepare yourself for a new workplace challenge:',
                            'services' => [
                                'In-person internship.',
                                'Any career field.',
                                'Central Cities around the world.',
                                'Boost your employability.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 3,
                'title' => 'AI & Advanced Technologies',
                'slug' => 'ai-and-advanced-technologies',
                'description' => 'AI and advanced technology solutions.',
                'image' => '/images/services/ai.jpg',
                'hero_image' => '/images/services/ai-hero.jpg',
                'is_featured' => true,
                'order' => 3,
                'sections' => [
                    [
                        'name' => 'ai',
                        'order' => 1,
                        'content' => [
                            'title' => 'Artificial Intelligence (AI) and Advanced Tech',
                            'description' => 'AI represents a transformative technology with the potential to revolutionize organizations services and operations.<br>By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.<br>By investing in AI education, training and projects, organizations can better meet the needs of their people, drive innovation and ensure sustainable development.',
                            'image' => '/images/04_AI/01.jpg',
                            'subtitle' => 'We Customize Transformation Projects',
                            'mainContent' => 'JADCO and partners support to harness the power of AI and digital technologies to help improve business operations and organization thrive.<br>We help to explore ways to leverage new advances in digital-tech to re-invent how things get done and boost your organization positioning in its sector.<br>We analyze the existing structure, navigate challenges and evaluate ways that technology can affect factors in your organization and identify new business models enabled by AI and explore opportunities presented by AI.<br>Learn how to shape your AI business strategy, organizational dynamics, products, services innovation and evolving workforce skills and discover practical solutions to build business advantage.'
                        ]
                    ]
                ]
            ],
            [
                'id' => 4,
                'title' => 'E-Gaming & eSport',
                'slug' => 'egaming-and-esport',
                'description' => 'E-Gaming and eSport solutions.',
                'image' => '/images/services/gaming.jpg',
                'hero_image' => '/images/services/gaming-hero.jpg',
                'is_featured' => true,
                'order' => 4,
                'sections' => [
                    [
                        'name' => 'egaming',
                        'order' => 1,
                        'content' => [
                            'title' => 'eGaming and eSport',
                            'description' => 'JADCO and international partners in gaming and eSport, USA highly ranked universities in gaming and simulation development and integrated e-Arts programs and a Consortium firm supported by the U.S department of Commerce (International Trade Administration), are together forming a consortium to propose a broad-based support package and plans for e-gaming and eSport to help and greatly accelerate the Kingdom\'s positioning as a leader in this industry worldwide by leveraging international relevant partners, SMEs, and other resources to support developing the sector\'s entire value chain and achieve the objectives of the Saudi Arabia\'s newly gaming, eSport and AI strategy.',
                            'image' => '/images/05_eGame/01.jpg',
                            'listTitle' => 'What we do:',
                            'services' => [
                                'Industry Analysis',
                                'Policy and Regulatory Infrastructure',
                                'Economic Impact',
                                'Infrastructure and Facilities planning',
                                'Education and Talent Development Strategy',
                                'AI Engagement in e-gaming and esport',
                                'Community engagement and outreach',
                                'Event Management and Marketing Support',
                                'Evaluation and Monitoring Framework'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 5,
                'title' => 'Arts & Entertainment',
                'slug' => 'arts-and-entertainment',
                'description' => 'Arts and entertainment services.',
                'image' => '/images/services/arts.jpg',
                'hero_image' => '/images/services/arts-hero.jpg',
                'is_featured' => true,
                'order' => 5,
                'sections' => [
                    [
                        'name' => 'arts',
                        'order' => 1,
                        'content' => [
                            'title' => 'Arts and Entertainment',
                            'description' => 'Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.',
                            'image' => '/images/06_Arts/01.jpg',
                            'subtitle' => 'From Training and education in Arts & Entertainment subjects, to customizing projects and live events in association with our local and international partners.',
                            'services' => []
                        ]
                    ]
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            $sections = $serviceData['sections'] ?? [];
            unset($serviceData['sections']);
            
            $service = Service::create($serviceData);
            
            foreach ($sections as $sectionData) {
                Section::create([
                    'page_id' => $service->id,
                    'name' => $sectionData['name'],
                    'order' => $sectionData['order'],
                    'content' => $sectionData['content']
                ]);
            }
        }
    }
} 