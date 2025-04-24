<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run()
    {
        // Header section
        Section::create([
            'page_id' => 1, // Home page
            'name' => 'header',
            'order' => 1,
            'content' => [
                'headings' => [
                    'From Education and Training to Innovation',
                    'The Latest AI and Technologies',
                    'Innovative Efforts in Revolutionizing the eSport Industry',
                    'Bringing the global Arts and Entertainment Events to town'
                ],
                'aboutHeading' => 'We Listen, design your vision and bring it to life... Let\'s talk',
                'serviceHeadings' => [
                    'education' => 'From Education and Training to Innovation',
                    'ai' => 'The Latest AI and Technologies',
                    'egaming' => 'Innovative Efforts in Revolutionizing the eSport Industry',
                    'arts' => 'Bringing the Global Arts and Entertainment Events to town',
                    'training' => 'From Education and Training to Innovation'
                ],
                'errorHeading' => 'JADCO Error page',
                'servicesTitle' => 'SERVICES',
                'talkButtonText' => 'Let\'s Talk',
                'slides' => [
                    ['src' => '/images/Header/01_EDU_Home.jpg', 'alt' => 'Education'],
                    ['src' => '/images/Header/02_AI_Home.jpg', 'alt' => 'AI'],
                    ['src' => '/images/Header/03_Games_Home.jpg', 'alt' => 'Gaming'],
                    ['src' => '/images/Header/04_Arts_Header.jpg', 'alt' => 'Arts']
                ],
                'service_images' => [
                    'education' => 'images/Header/01_EDU_Home.jpg',
                    'ai' => 'images/Header/02_AI_Home.jpg',
                    'egaming' => 'images/Header/03_Games_Home.jpg',
                    'arts' => 'images/Header/04_Arts_Header.jpg',
                    'training' => 'images/01_Training_Header.jpg'
                ],
                'about_image' => 'images/About_Page.jpg',
                'services' => [
                    ['title' => 'Education and Scholarship', 'link' => '/services/education-and-scholarship'],
                    ['title' => 'Training and Professional Development', 'link' => '/services/training-and-professional-development'],
                    ['title' => 'AI and Advanced Technologies', 'link' => '/services/ai-and-advanced-technologies'],
                    ['title' => 'E-Gaming and eSport', 'link' => '/services/egaming-and-esport'],
                    ['title' => 'Arts and Entertainment', 'link' => '/services/arts-and-entertainment']
                ]
            ]
        ]);

        // Home about section
        Section::create([
            'page_id' => 1, // Home page
            'name' => 'about',
            'order' => 2,
            'content' => [
                'title' => 'ABOUT',
                'logo' => 'images/jadoo-logo 2.png',
                'main_text' => 'After more than 20 years of experience in the Saudi Arabia\'s Human Capital Development market, JAD Consulting (JADCO) was established to continue supporting the industry with a new inspired vision by the great Saudi Vision 2030.',
                'description1' => 'JADCO and its highly ranked international partners of Companies, Universities and SMEs are forming together an exclusive and innovative consortium to serve and be part of the revolution and development and support the transformation for the next levels.',
                'description2' => 'JADCO in collaboration with the best partners in the globe, customize and Tailor projects to bridge the gap and providing the latest technologies to ensure the max level of quality of deliverables, support local content and transform knowledge to meet the objectives of our clients.',
                'image1' => 'images/About_01.jpg',
                'image2' => 'images/About_02.jpg'
            ]
        ]);

        // Home services section
        Section::create([
            'page_id' => 1, // Home page
            'name' => 'services',
            'order' => 3,
            'content' => [
                'title' => 'SERVICES',
                'services' => [
                    [
                        'title' => 'Education and Training',
                        'description' => 'With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor\'s vision and targets.',
                        'image' => 'images/Home_Serv_01.jpg',
                        'buttons' => [
                            [
                                'text' => 'Education',
                                'link' => '/services/education-and-scholarship',
                                'class' => 'btn-education'
                            ],
                            [
                                'text' => 'Training',
                                'link' => '/services/training-and-professional-development',
                                'class' => ''
                            ]
                        ]
                    ],
                    [
                        'title' => 'AI and Advanced Technologies',
                        'description' => 'AI represents a transformative technology with the potential to revolutionize organizations services and operations. By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.',
                        'image' => 'images/Home_Serv_02.jpg',
                        'link' => '/services/ai-and-advanced-technologies'
                    ],
                    [
                        'title' => 'eGaming and eSport',
                        'description' => 'JADCO and international partners in gaming and eSport, USA highly ranked universities in gaming and simulation development and integrated e-Arts programs and a Consortium firm supported by the U.S department of ...',
                        'image' => 'images/Home_Serv_03.jpg',
                        'link' => '/services/egaming-and-esport'
                    ],
                    [
                        'title' => 'Arts and Entertainment',
                        'description' => 'Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.',
                        'image' => 'images/Home_Serv_04.jpg',
                        'link' => '/services/arts-and-entertainment'
                    ]
                ]
            ]
        ]);

        // Home educational services section
        Section::create([
            'page_id' => 1, // Home page
            'name' => 'educational_services',
            'order' => 4,
            'content' => [
                'title' => 'Educational Services',
                'services' => [
                    [
                        'title' => 'Scholarship Programs Management',
                        'description' => 'With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor\'s vision and targets.',
                        'link' => '/services/education-and-scholarship'
                    ],
                    [
                        'title' => 'STEM Education and Innovation Centers',
                        'description' => 'Providing innovative STEM education approaches and establishing cutting-edge innovation centers to foster creativity and practical skills.',
                        'link' => '/services/education-and-scholarship'
                    ],
                    [
                        'title' => 'K-12 International Schools',
                        'description' => 'Development and management of international standard K-12 schools with globally recognized curricula and excellent teaching staff.',
                        'link' => '/services/education-and-scholarship'
                    ]
                ]
            ]
        ]);

        // Home statistics section
        Section::create([
            'page_id' => 1, // Home page
            'name' => 'statistics',
            'order' => 5,
            'content' => [
                'show_statistics' => true,
                'stats' => [
                    [
                        'number' => '15 Institutes',
                        'text' => 'Cross disciplinary boundaries'
                    ],
                    [
                        'number' => '20 Libraries',
                        'text' => 'Hold over 12 million items'
                    ],
                    [
                        'number' => '$2.2 Billion',
                        'text' => 'Sponsored research budget'
                    ]
                ]
            ]
        ]);

        // Contact section
        Section::create([
            'page_id' => 4, // Contact page
            'name' => 'contact',
            'order' => 1,
            'content' => [
                'logo' => 'images/logo.png',
                'tagline' => 'We Listen, design your vision and bring it to life...',
                'heading' => 'LET\'S TALK.',
                'locations' => [
                    [
                        'title' => 'Saudi Arabia',
                        'address' => 'Level 7, Building 4.07, Zone 4<br>King Abdullah Financial District<br>(KAFD)<br>Riyadh 13519, Saudi Arabia.',
                        'contacts' => [
                            ['label' => 'Tel', 'value' => '(+966) 115256175', 'type' => 'whatsapp'],
                            ['label' => 'Mobile', 'value' => '(+966) 569292048', 'type' => 'whatsapp'],
                            ['label' => 'Email', 'value' => 'jad@jadco.co', 'type' => 'email']
                        ]
                    ],
                    [
                        'title' => 'USA',
                        'address' => '3972 Barranca Parkway,<br>Ste J139, Irvine, CA 92606'
                    ],
                    [
                        'title' => 'UAE',
                        'address' => 'A1, Dubai Digital Park, Dubai<br>Silicon Oasis, Dubai,<br>United Arab Emirates.'
                    ]
                ],
                'form' => [
                    'labels' => [
                        'firstName' => 'First Name',
                        'lastName' => 'Last Name',
                        'email' => 'Email',
                        'phone' => 'Phone Number',
                        'message' => 'Message'
                    ],
                    'submitButton' => 'SEND A MESSAGE',
                    'successMessage' => 'Thank you for your message. We will get back to you soon!'
                ],
                'socialLinks' => [
                    ['icon' => 'fab fa-youtube', 'title' => 'YouTube', 'url' => '#'],
                    ['icon' => 'fab fa-linkedin', 'title' => 'LinkedIn', 'url' => '#']
                ],
                'copyright' => 'All Rights Reserved.'
            ]
        ]);

        // About page content
        Section::create([
            'page_id' => 2, // About page
            'name' => 'about',
            'order' => 1,
            'content' => [
                'title' => 'ABOUT',
                'logo' => '/images/jadoo-logo 2.png',
                'main_text' => 'After more than 20 years of experience in the Saudi Arabia\'s Human Capital Development market, JAD Consulting (JADCO) was established to continue supporting the industry with a new inspired vision by the great Saudi Vision 2030.',
                'description1' => 'JADCO and its highly ranked international partners of Companies, Universities and SMEs are forming together an exclusive and innovative consortium to serve and be part of the revolution and development and support the transformation for the next levels.',
                'description2' => 'JADCO in collaboration with the best partners in the globe, customize and Tailor projects to bridge the gap and providing the latest technologies to ensure the max level of quality of deliverables, support local content and transform knowledge to meet the objectives of our clients.',
                'image1' => '/images/About_01.jpg',
                'image2' => '/images/About_02.jpg'
            ]
        ]);

        // Services page content
        Section::create([
            'page_id' => 3, // Services page
            'name' => 'services',
            'order' => 1,
            'content' => [
                'title' => 'Our Services',
                'description' => 'Discover our comprehensive range of services designed to meet your needs.',
                'services' => [
                    [
                        'id' => 1,
                        'title' => 'Education & Scholarship',
                        'slug' => 'education-and-scholarship',
                        'description' => 'Comprehensive education and scholarship management services.',
                        'image' => '/images/services/education.jpg',
                        'is_featured' => true,
                        'order' => 1
                    ],
                    [
                        'id' => 2,
                        'title' => 'Training & Professional Development',
                        'slug' => 'training-and-professional-development',
                        'description' => 'Professional training and development programs.',
                        'image' => '/images/services/training.jpg',
                        'is_featured' => true,
                        'order' => 2
                    ],
                    [
                        'id' => 3,
                        'title' => 'AI & Advanced Technologies',
                        'slug' => 'ai-and-advanced-technologies',
                        'description' => 'AI and advanced technology solutions.',
                        'image' => '/images/services/ai.jpg',
                        'is_featured' => true,
                        'order' => 3
                    ],
                    [
                        'id' => 4,
                        'title' => 'E-Gaming & eSport',
                        'slug' => 'egaming-and-esport',
                        'description' => 'E-Gaming and eSport solutions.',
                        'image' => '/images/services/gaming.jpg',
                        'is_featured' => true,
                        'order' => 4
                    ],
                    [
                        'id' => 5,
                        'title' => 'Arts & Entertainment',
                        'slug' => 'arts-and-entertainment',
                        'description' => 'Arts and entertainment services.',
                        'image' => '/images/services/arts.jpg',
                        'is_featured' => true,
                        'order' => 5
                    ]
                ]
            ]
        ]);

        // Education & Scholarship page content
        Section::create([
            'page_id' => 5, // Education & Scholarship page
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
        ]);

        Section::create([
            'page_id' => 5, // Education & Scholarship page
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
        ]);

        Section::create([
            'page_id' => 5, // Education & Scholarship page
            'name' => 'k12',
            'order' => 3,
            'content' => [
                'title' => 'K-12 International Schools',
                'description' => 'JADCO in partnership with the 1st international School Services Worldwide with more than 60 years of experience with track records, is supporting the global education community.',
                'image' => '/images/04_EDU.jpg',
                'services' => [
                    'School Operation and Management.',
                    'Teacher Recruitment.',
                    'Leadership Search.',
                    'Professional Development.',
                    'School Supply Services.',
                    'Accounting and Finance.'
                ]
            ]
        ]);

        // Training & Professional Development page content
        Section::create([
            'page_id' => 6, // Training & Professional Development page
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
        ]);

        Section::create([
            'page_id' => 6, // Training & Professional Development page
            'name' => 'technical',
            'order' => 2,
            'content' => [
                'title' => 'Technical and Vocational Training and Education (TVTE)',
                'description' => 'Studying overseas at leading colleges that provide students with academic, technical skills, Knowledge, career training necessary to succeed in future jobs, make them ready to go for labor market.',
                'image' => '/images/03_Trining/03_Training.jpg',
                'mainContent' => 'In collaboration with our best TVTE providers in the U.S, UK and other countries. These programs are hands-on diploma or associate degrees of 2 years study and training of majors from all fields and sectors, that make students equipped by all skills needed for the career job market.',
                'listTitle' => 'Example majors including:',
                'services' => [
                    'Advanced Transportation Technologies',
                    'Automotive Technology',
                    'Architecture',
                    'Baking & Pastry Arts',
                    'Construction Technology',
                    'Culinary Arts',
                    'Cybersecurity',
                    'Fashion',
                    'Game Design',
                    'Horticulture',
                    'Nursing & Medical Assisting',
                    'And more.'
                ]
            ]
        ]);

        Section::create([
            'page_id' => 6, // Training & Professional Development page
            'name' => 'online',
            'order' => 3,
            'content' => [
                'title' => 'Online Professional Programs',
                'description' => 'Flexible learning for students and busy professionals with high quality teaching, blended learning activities with expert instructors, SMEs, professionals and leaders from the industry.',
                'image' => '/images/03_Trining/04_Training.jpg',
                'mainContent' => 'JADCO in partnership with top ranked universities in the world will serve to provide your organization the best online learning experience and quality of knowledge in all sectors, delivering programs that develop the skills necessary to success in a changing and challenging work environment.'
            ]
        ]);

        // AI & Advanced Technologies page content
        Section::create([
            'page_id' => 7, // AI & Advanced Technologies page
            'name' => 'ai',
            'order' => 1,
            'content' => [
                'title' => 'Artificial Intelligence (AI) and Advanced Tech',
                'description' => 'AI represents a transformative technology with the potential to revolutionize organizations services and operations.<br>By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.<br>By investing in AI education, training and projects, organizations can better meet the needs of their people, drive innovation and ensure sustainable development.',
                'image' => '/images/04_AI/01.jpg',
                'subtitle' => 'We Customize Transformation Projects',
                'mainContent' => 'JADCO and partners support to harness the power of AI and digital technologies to help improve business operations and organization thrive.<br>We help to explore ways to leverage new advances in digital-tech to re-invent how things get done and boost your organization positioning in its sector.<br>We analyze the existing structure, navigate challenges and evaluate ways that technology can affect factors in your organization and identify new business models enabled by AI and explore opportunities presented by AI.<br>Learn how to shape your AI business strategy, organizational dynamics, products, services innovation and evolving workforce skills and discover practical solutions to build business advantage.'
            ]
        ]);

        // E-Gaming & eSport page content
        Section::create([
            'page_id' => 8, // E-Gaming & eSport page
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
        ]);

        // Arts & Entertainment page content
        Section::create([
            'page_id' => 9, // Arts & Entertainment page
            'name' => 'arts',
            'order' => 1,
            'content' => [
                'title' => 'Arts and Entertainment',
                'description' => 'Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.',
                'image' => '/images/06_Arts/01.jpg',
                'subtitle' => 'From Training and education in Arts & Entertainment subjects, to customizing projects and live events in association with our local and international partners.',
                'services' => []
            ]
        ]);
    }
} 