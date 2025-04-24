# Page Documentation
have uploaded a full documentation file (pages.md) describing a Laravel CMS structure for dynamic frontend pages. The pages are built in Blade (home.blade.php) and Vue (/js/pages/*) and get their content from the database.

The file includes:

Page and section layout structures

Content schemas in JSON

Database tables: create a relevant database that has : pages, sections,, etc 

API endpoints mapped to views

Please:

🧱 Backend Setup
Generate Laravel migrations for pages, sections, and section_images based on the file.

Create seeders that populate data matching the example content in the JSON blocks.

Set up Eloquent models with relationships.

Build Repositories and Services for fetching content by slug or section name.

Implement Controllers to serve these APIs (e.g., /api/home/sections, /api/about/sections).

Create API routes grouped under /api that serve dynamic JSON for all frontend pages.

🖼 Frontend Integration
Show how to use these API endpoints in:

Blade: Example in home.blade.php using @json($sections) or similar

Vue: Example using axios to call APIs and bind to data

## Database Tables

### Pages

| Column Name | Type | Description |
|-------------|------|-------------|
| id | integer | Primary key |
| name | string | Page name |
| slug | string | URL slug |
| template | string | Template name |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

### Sections

| Column Name | Type | Description |
|-------------|------|-------------|
| id | integer | Primary key |
| page_id | integer | References pages.id |
| name | string | Section name |
| order | integer | Display order |
| content | json | JSON content |

### Section Images

| Column Name | Type | Description |
|-------------|------|-------------|
| id | integer | Primary key |
| section_id | integer | References sections.id |
| path | string | Image path |
| alt | string | Alt text |

---

# BLADE COMPONENTS

## HEADER COMPONENT (Blade Version)
**Location:** `resources/views/frontend/partials/header.blade.php`
**API:** `/api/header/sections`
**Database:** Sections (name:'header')

### Content Structure
```json
{
  "headings": [
    "From Education and Training to Innovation",
    "The Latest AI and Technologies",
    "Innovative Efforts in Revolutionizing the eSport Industry",
    "Bringing the global Arts and Entertainment Events to town"
  ],
  "aboutHeading": "We Listen, design your vision and bring it to life... Let's talk",
  "serviceHeadings": {
    "education": "From Education and Training to Innovation",
    "ai": "The Latest AI and Technologies",
    "egaming": "Innovative Efforts in Revolutionizing the eSport Industry",
    "arts": "Bringing the Global Arts and Entertainment Events to town",
    "training": "From Education and Training to Innovation"
  },
  "errorHeading": "JADCO Error page",
  "servicesTitle": "SERVICES",
  "talkButtonText": "Let's Talk",
  "slides": [
    { "src": "/images/Header/01_EDU_Home.jpg", "alt": "Education" },
    { "src": "/images/Header/02_AI_Home.jpg", "alt": "AI" },
    { "src": "/images/Header/03_Games_Home.jpg", "alt": "Gaming" },
    { "src": "/images/Header/04_Arts_Header.jpg", "alt": "Arts" }
  ],
  "service_images": {
    "education": "images/Header/01_EDU_Home.jpg",
    "ai": "images/Header/02_AI_Home.jpg",
    "egaming": "images/Header/03_Games_Home.jpg",
    "arts": "images/Header/04_Arts_Header.jpg",
    "training": "images/01_Training_Header.jpg"
  },
  "about_image": "images/About_Page.jpg",
  "services": [
    { "title": "Education and Scholarship", "link": "/services/education-and-scholarship" },
    { "title": "Training and Professional Development", "link": "/services/training-and-professional-development" },
    { "title": "AI and Advanced Technologies", "link": "/services/ai-and-advanced-technologies" },
    { "title": "E-Gaming and eSport", "link": "/services/egaming-and-esport" },
    { "title": "Arts and Entertainment", "link": "/services/arts-and-entertainment" }
  ]
}
```

## CONTACT COMPONENT
**Location:** `resources/views/frontend/partials/contact.blade.php`
**API:** `/api/contact/sections`
**Database:** Sections (name:'contact')

### Content Structure
```json
{
  "logo": "images/logo.png",
  "tagline": "We Listen, design your vision and bring it to life...",
  "heading": "LET'S TALK.",
  "locations": [
    {
      "title": "Saudi Arabia",
      "address": "Level 7, Building 4.07, Zone 4<br>King Abdullah Financial District<br>(KAFD)<br>Riyadh 13519, Saudi Arabia.",
      "contacts": [
        { "label": "Tel", "value": "(+966) 115256175", "type": "whatsapp" },
        { "label": "Mobile", "value": "(+966) 569292048", "type": "whatsapp" },
        { "label": "Email", "value": "jad@jadco.co", "type": "email" }
      ]
    },
    {
      "title": "USA",
      "address": "3972 Barranca Parkway,<br>Ste J139, Irvine, CA 92606"
    },
    {
      "title": "UAE",
      "address": "A1, Dubai Digital Park, Dubai<br>Silicon Oasis, Dubai,<br>United Arab Emirates."
    }
  ],
  "form": {
    "labels": {
      "firstName": "First Name",
      "lastName": "Last Name",
      "email": "Email",
      "phone": "Phone Number",
      "message": "Message"
    },
    "submitButton": "SEND A MESSAGE",
    "successMessage": "Thank you for your message. We will get back to you soon!"
  },
  "socialLinks": [
    { "icon": "fab fa-youtube", "title": "YouTube", "url": "#" },
    { "icon": "fab fa-linkedin", "title": "LinkedIn", "url": "#" }
  ],
  "copyright": "All Rights Reserved."
}
```

# PAGES

## HOME PAGE
**Location:** `resources/views/frontend/pages/home.blade.php`
**API:** `/api/home/sections`
**Database:** Pages (id:1, slug:'home')

### Content Structure
The home page is made up of multiple sections:

#### About Section
```json
{
  "title": "ABOUT",
  "logo": "images/jadoo-logo 2.png",
  "main_text": "After more than 20 years of experience in the Saudi Arabia's Human Capital Development market, JAD Consulting (JADCO) was established to continue supporting the industry with a new inspired vision by the great Saudi Vision 2030.",
  "description1": "JADCO and its highly ranked international partners of Companies, Universities and SMEs are forming together an exclusive and innovative consortium to serve and be part of the revolution and development and support the transformation for the next levels.",
  "description2": "JADCO in collaboration with the best partners in the globe, customize and Tailor projects to bridge the gap and providing the latest technologies to ensure the max level of quality of deliverables, support local content and transform knowledge to meet the objectives of our clients.",
  "image1": "images/About_01.jpg", 
  "image2": "images/About_02.jpg"
}
```

#### Services Section
```json
{
  "title": "SERVICES",
  "services": [
    {
      "title": "Education and Training",
      "description": "With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor's vision and targets.",
      "image": "images/Home_Serv_01.jpg",
      "buttons": [
        {
          "text": "Education",
          "link": "/services/education-and-scholarship",
          "class": "btn-education"
        },
        {
          "text": "Training",
          "link": "/services/training-and-professional-development",
          "class": ""
        }
      ]
    },
    {
      "title": "AI and Advanced Technologies",
      "description": "AI represents a transformative technology with the potential to revolutionize organizations services and operations. By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.",
      "image": "images/Home_Serv_02.jpg",
      "link": "/services/ai-and-advanced-technologies"
    },
    {
      "title": "eGaming and eSport",
      "description": "JADCO and international partners in gaming and eSport, USA highly ranked universities in gaming and simulation development and integrated e-Arts programs and a Consortium firm supported by the U.S department of ...",
      "image": "images/Home_Serv_03.jpg",
      "link": "/services/egaming-and-esport"
    },
    {
      "title": "Arts and Entertainment",
      "description": "Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.",
      "image": "images/Home_Serv_04.jpg",
      "link": "/services/arts-and-entertainment"
    }
  ]
}
```

#### Educational Services Section
```json
{
  "title": "Educational Services",
  "services": [
    {
      "title": "Scholarship Programs Management",
      "description": "With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor's vision and targets.",
      "link": "/services/education-and-scholarship"
    },
    {
      "title": "STEM Education and Innovation Centers",
      "description": "Providing innovative STEM education approaches and establishing cutting-edge innovation centers to foster creativity and practical skills.",
      "link": "/services/education-and-scholarship"
    },
    {
      "title": "K-12 International Schools",
      "description": "Development and management of international standard K-12 schools with globally recognized curricula and excellent teaching staff.",
      "link": "/services/education-and-scholarship"
    }
  ]
}
```

#### Statistics Section
```json
{
  "show_statistics": true,
  "stats": [
    {
      "number": "15 Institutes",
      "text": "Cross disciplinary boundaries"
    },
    {
      "number": "20 Libraries",
      "text": "Hold over 12 million items"
    },
    {
      "number": "$2.2 Billion",
      "text": "Sponsored research budget"
    }
  ]
}
```

---

# VUE COMPONENTS

## HEADER COMPONENT (Vue Version)
**Location:** `resources/js/components/Header.vue`
**API:** `/api/header/sections`
**Database:** Sections (name:'header')

### Content Structure
```json
{
  "headings": [
    "From Education and Training to Innovation",
    "The Latest AI and Technologies",
    "Innovative Efforts in Revolutionizing the eSport Industry",
    "Bringing the global Arts and Entertainment Events to town"
  ],
  "aboutHeading": "We Listen, design your vision and bring it to life... Let's talk",
  "serviceHeadings": {
    "education": "From Education and Training to Innovation",
    "ai": "The Latest AI and Technologies",
    "egaming": "Innovative Efforts in Revolutionizing the eSport Industry",
    "arts": "Bringing the Global Arts and Entertainment Events to town",
    "training": "From Education and Training to Innovation"
  },
  "errorHeading": "JADCO Error page",
  "servicesTitle": "SERVICES",
  "talkButtonText": "Let's Talk",
  "slides": [
    { "src": "/images/Header/01_EDU_Home.jpg", "alt": "Education" },
    { "src": "/images/Header/02_AI_Home.jpg", "alt": "AI" },
    { "src": "/images/Header/03_Games_Home.jpg", "alt": "Gaming" },
    { "src": "/images/Header/04_Arts_Header.jpg", "alt": "Arts" }
  ],
  "services": [
    { "title": "Education and Scholarship", "link": "/services/education-and-scholarship" },
    { "title": "Training and Professional Development", "link": "/services/training-and-professional-development" },
    { "title": "AI and Advanced Technologies", "link": "/services/ai-and-advanced-technologies" },
    { "title": "E-Gaming and eSport", "link": "/services/egaming-and-esport" },
    { "title": "Arts and Entertainment", "link": "/services/arts-and-entertainment" }
  ]
}
```

## ABOUT PAGE
**Location:** `resources/js/pages/About.vue`
**API:** `/api/about/sections`
**Database:** Pages (id:2, slug:'about'), Sections (id:5, name:'about')

### Content Structure
```json
{
  "content": {
    "title": "ABOUT",
    "logo": "/images/jadoo-logo 2.png",
    "main_text": "After more than 20 years of experience in the Saudi Arabia's Human Capital Development market, JAD Consulting (JADCO) was established to continue supporting the industry with a new inspired vision by the great Saudi Vision 2030.",
    "description1": "JADCO and its highly ranked international partners of Companies, Universities and SMEs are forming together an exclusive and innovative consortium to serve and be part of the revolution and development and support the transformation for the next levels.",
    "description2": "JADCO in collaboration with the best partners in the globe, customize and Tailor projects to bridge the gap and providing the latest technologies to ensure the max level of quality of deliverables, support local content and transform knowledge to meet the objectives of our clients.",
    "image1": "/images/About_01.jpg", 
    "image2": "/images/About_02.jpg"
  }
}
```

## SERVICES PAGE
**Location:** `resources/js/pages/Services.vue`
**API:** `/api/services/sections`
**Database:** Pages (id:3, slug:'services')

### Content Structure
```json
{
  "services": [
    {
      "id": 1,
      "title": "Education & Scholarship",
      "slug": "education-and-scholarship",
      "description": "Comprehensive education and scholarship management services.",
      "image": "/images/services/education.jpg",
      "is_featured": true,
      "order": 1
    },
    {
      "id": 2,
      "title": "Training & Professional Development",
      "slug": "training-and-professional-development",
      "description": "Professional training and development programs.",
      "image": "/images/services/training.jpg",
      "is_featured": true,
      "order": 2
    }
  ]
}
```

## EDUCATION & SCHOLARSHIP PAGE
**Location:** `resources/js/pages/services/EducationAndScholarship.vue`
**API:** `/api/education/sections`
**Database:** Services (id:1, slug:'education-and-scholarship')

### Content Structure
```json
{
  "content": {
    "scholarship": {
      "title": "Scholarship Programs Management",
      "description": "With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor's vision and targets. Services include but not limited to:",
      "image": "/images/02_Education/01.jpg",
      "services": [
        "Candidate Selection Criteria",
        "Universities Selection Criteria",
        "Program bylaws and regulations",
        "Student Journey plan",
        "Counseling & students development plans",
        "Pre-Departure preparatory programs",
        "Enroll students into international universities",
        "Workshops, awareness sessions and summits",
        "Pre travel and post travel logistics",
        "Overseas continual support",
        "And more"
      ]
    },
    "stem": {
      "title": "STEM Education and Innovation Centers",
      "description": "Education is changing, STEM (Since, Technology, Engineering and Math), is the pathway to reshaping the ecosystem to rethinking and broadly reimagining. STEM is the future workforce skills, steps toward a new generation of innovators, creative leaders for now and the future.",
      "image": "/images/03_EDU.jpg",
      "listTitle": "Our services including:",
      "services": [
        "STEM Teacher Training.",
        "STEM Students Training.",
        "STEM Labs.",
        "Summer STEM Camps.",
        "Students Workshops.",
        "Research and development.",
        "Innovation Parks.",
        "K-12 after school programs (Coding, Robotics, Drones, AI, Cybersecurity, AR & VR, 3D Printing & more).",
        "Establishing new STEM Schools or Shift to STEM."
      ]
    },
    "k12": {
      "title": "K-12 International Schools",
      "description": "JADCO in partnership with the 1st international School Services Worldwide with more than 60 years of experience with track records, is supporting the global education community.",
      "image": "/images/04_EDU.jpg",
      "services": [
        "School Operation and Management.",
        "Teacher Recruitment.",
        "Leadership Search.",
        "Professional Development.",
        "School Supply Services.",
        "Accounting and Finance."
      ]
    }
  }
}
```

## TRAINING & PROFESSIONAL DEVELOPMENT PAGE
**Location:** `resources/js/pages/services/TrainingAndProfessionalDevelopment.vue`
**API:** `/api/training/sections`
**Database:** Services (id:2, slug:'training-and-professional-development')

### Content Structure
```json
{
  "content": {
    "fellowship": {
      "title": "Fellowship, Internship and Work Experience",
      "description": "For a new talented and future skilled generation, we draw the map and open the door for understanding the contemporary workplace and environment that promote ready to innovate employees.",
      "image": "/images/03_Trining/01.jpg",
      "mainContent": "JADCO, in association with prestigious organizations from all sizes, take your personal and professional development in an international company in a global city to learn new skills, reskill or upskill yourself and accelerate your career advancement.<br><br>Gain experience, new skills, network, knowledge, culture, friends and prepare yourself for a new workplace challenge:",
      "services": [
        "In-person internship.",
        "Any career field.",
        "Central Cities around the world.",
        "Boost your employability."
      ]
    },
    "technical": {
      "title": "Technical and Vocational Training and Education (TVTE)",
      "description": "Studying overseas at leading colleges that provide students with academic, technical skills, Knowledge, career training necessary to succeed in future jobs, make them ready to go for labor market.",
      "image": "/images/03_Trining/03_Training.jpg",
      "mainContent": "In collaboration with our best TVTE providers in the U.S, UK and other countries. These programs are hands-on diploma or associate degrees of 2 years study and training of majors from all fields and sectors, that make students equipped by all skills needed for the career job market.",
      "listTitle": "Example majors including:",
      "services": [
        "Advanced Transportation Technologies",
        "Automotive Technology",
        "Architecture",
        "Baking & Pastry Arts",
        "Construction Technology",
        "Culinary Arts",
        "Cybersecurity",
        "Fashion",
        "Game Design",
        "Horticulture",
        "Nursing & Medical Assisting",
        "And more."
      ]
    },
    "online": {
      "title": "Online Professional Programs",
      "description": "Flexible learning for students and busy professionals with high quality teaching, blended learning activities with experts' instructors, SMEs, professional and leaders from the industry.",
      "image": "/images/03_Trining/04_Training.jpg",
      "mainContent": "JADCO in partnership with top ranked universities in the world will serve to provide your organization the best online learning experience and quality of knowledge in all sectors, delivering programs that develop the skills necessary to success in a changing and challenging work environment."
    }
  }
}
```

## AI & ADVANCED TECHNOLOGIES PAGE
**Location:** `resources/js/pages/services/AiAndAdvancedTechnologies.vue`
**API:** `/api/ai/sections`
**Database:** Services (id:3, slug:'ai-and-advanced-technologies')

### Content Structure
```json
{
  "content": {
    "title": "Artificial Intelligence (AI) and Advanced Tech",
    "description": "AI represents a transformative technology with the potential to revolutionize organizations services and operations.<br>By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.<br>By investing in AI education, training and projects, organizations can better meet the needs of their people, drive innovation and ensure sustainable development.",
    "image": "/images/04_AI/01.jpg",
    "subtitle": "We Customize Transformation Projects",
    "mainContent": "JADCO and partners support to harness the power of AI and digital technologies to help improve business operations and organization thrive.<br>We help to explore ways to leverage new advances in digital-tech to re-invent how things get done and boost your organization positioning in its sector.<br>We analyze the existing structure, navigate challenges and evaluate ways that technology can affect factors in your organization and identify new business models enabled by AI and explore opportunities presented by AI.<br>Learn how to shape your AI business strategy, organizational dynamics, products, services innovation and evolving workforce skills and discover practical solutions to build business advantage."
  }
}
```

## EGAMING & ESPORT PAGE
**Location:** `resources/js/pages/services/EgamingAndEsport.vue`
**API:** `/api/egaming/sections`
**Database:** Services (id:4, slug:'egaming-and-esport')

### Content Structure
```json
{
  "content": {
    "title": "eGaming and eSport",
    "description": "JADCO and international partners in gaming and eSport, USA highly ranked universities in gaming and simulation development and integrated e-Arts programs and a Consortium firm supported by the U.S department of Commerce (International Trade Administration), are together forming a consortium to propose a broad-based support package and plans for e-gaming and eSport to help and greatly accelerate the Kingdom's positioning as a leader in this industry worldwide by leveraging international relevant partners, SMEs, and other resources to support developing the sector's entire value chain and achieve the objectives of the Saudi Arabia's newly gaming, eSport and AI strategy.",
    "image": "/images/05_eGame/01.jpg",
    "listTitle": "What we do:",
    "services": [
      "Industry Analysis",
      "Policy and Regulatory Infrastructure",
      "Economic Impact",
      "Infrastructure and Facilities planning",
      "Education and Talent Development Strategy",
      "AI Engagement in e-gaming and esport",
      "Community engagement and outreach",
      "Event Management and Marketing Support",
      "Evaluation and Monitoring Framework"
    ]
  }
}
```

## ARTS & ENTERTAINMENT PAGE
**Location:** `resources/js/pages/services/ArtsAndEntertainment.vue`
**API:** `/api/arts/sections`
**Database:** Services (id:5, slug:'arts-and-entertainment')

### Content Structure
```json
{
  "content": {
    "title": "Arts and Entertainment",
    "description": "Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.",
    "image": "/images/06_Arts/01.jpg",
    "subtitle": "From Training and education in Arts & Entertainment subjects, to customizing projects and live events in association with our local and international partners.",
    "services": []
  }
}
```

## SERVICE DETAIL PAGE
**Location:** `resources/js/pages/ServiceDetail.vue`
**API:** `/api/services/slug/{slug}/sections`
**Database:** Services table with related sections

### Content Structure
```json
{
  "service": {
    "id": 1,
    "title": "Education & Scholarship",
    "slug": "education-and-scholarship",
    "hero_image": "/images/services/education-hero.jpg",
    "description": "Comprehensive description of education services...",
    "features": [
      "International university partnerships",
      "Scholarship management",
      "Student placement services"
    ],
    "cta": {
      "text": "Contact us to learn more",
      "link": "/contact"
    }
  }
}
``` 