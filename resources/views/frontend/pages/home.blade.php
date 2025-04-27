@extends('frontend.layouts.app')

@section('content')
<div class="home-page">
    <!-- Hero Section -->
   
    {{-- @php
    dd($sections);
    @endphp --}}
    <!-- About Section -->
    @php
        $aboutSection = $sections->where('name', 'about')->first();
        $aboutContent = $aboutSection ? $aboutSection->content : [];
    @endphp
    @if($aboutSection && $aboutSection->is_active)
    <section id="about" class="about-section py-5 section" style="transform: translateZ(0);"> 
        <div class="container">
            <div class="about-heading">
                <h2 class="section-title">{{ isset($aboutContent['title']) ? $aboutContent['title'] : 'ABOUT' }}</h2>
                <img src="{{ asset($aboutContent['logo'] ?? 'images/jadoo-logo 2.png') }}" alt="JADCO Logo" class="about-logo">
            </div>
            <div class="about-text-container">
                <p class="about-text">
                    @if(isset($aboutContent['main_text']))
                        {!! $aboutContent['main_text'] !!}
                    @endif
                </p>
            </div>

            <div class="row">
                <div class="col-lg-6 col-sm-6 order-lg-1 order-2 about-main-description">
                    <p class="about-description">
                        @if(isset($aboutContent['description1']))
                            {!! $aboutContent['description1'] !!}
                        @endif
                    </p>
                    <p class="about-description mt-4">
                        @if(isset($aboutContent['description2']))
                            {!! $aboutContent['description2'] !!}
                        @endif
                    </p>
                </div>
                <div class="col-lg-6 col-sm-6 order-lg-2 order-1 about-image-wrapper">
                    <div class="about-image-container">
                        <div class="about-image-main">
                            <img src="{{ asset($aboutContent['image1'] ?? 'images/About_01.jpg') }}" alt="Graduate student" class="img-fluid">
                        </div>
                        <div class="about-image-secondary">
                            <img src="{{ asset($aboutContent['image2'] ?? 'images/About_02.jpg') }}" alt="Library and books" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Services Section -->
    @php
        $servicesSection = $sections->where('name', 'services')->first();
        $servicesContent = $servicesSection ? $servicesSection->content : [];
        $services = $servicesContent['services'] ?? [];
    @endphp
    @if($servicesSection && $servicesSection->is_active)
    <section id="services" class="services-section py-5 section">
        <div class="services-layer-container">
            <!-- First Service -->
            <div class="service-stack-item" data-service="1">
                <div class="service-overlay"></div>
                <div class="service-item-wrapper">
                    <div class="container">
                        <div class="row align-items-center full-screen md-h-auto">
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-image">
                                    <img src="{{ asset($services[0]['image'] ?? 'images/Home_Serv_01.jpg') }}" alt="Classroom setting"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-content">
                                    <div class="title">
                                        @if(isset($servicesContent['title']))
                                            <h2 class="section-title">{!! $servicesContent['title'] !!}</h2>
                                        @endif
                                        <h3 class="service-number">01</h3>
                                    </div>

                                    <div class="main-content">
                                        @if(isset($services[0]['title']))
                                            <h3 class="service-title">{!! $services[0]['title'] !!}</h3>
                                        @endif
                                        <p class="service-description">
                                            @if(isset($services[0]['description']))
                                                {!! $services[0]['description'] !!}
                                            @endif
                                        </p>
                                        <div class="service-buttons">
                                            @if(isset($services[0]['buttons']) && is_array($services[0]['buttons']) && count($services[0]['buttons']) >= 2)
                                                <a href="{{ $services[0]['buttons'][0]['link'] ?? '/services/education-and-scholarship' }}"
                                                    class="btn btn-service {{ $services[0]['buttons'][0]['class'] ?? 'btn-education' }}">
                                                    @if(isset($services[0]['buttons'][0]['text']))
                                                        {!! $services[0]['buttons'][0]['text'] !!}
                                                    @endif
                                                </a>
                                                <a href="{{ $services[0]['buttons'][1]['link'] ?? '/services/training-and-professional-development' }}"
                                                    class="btn btn-service {{ $services[0]['buttons'][1]['class'] ?? '' }}">
                                                    @if(isset($services[0]['buttons'][1]['text']))
                                                        {!! $services[0]['buttons'][1]['text'] !!}
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Service -->
            <div class="service-stack-item" data-service="2">
                <div class="service-overlay"></div>
                <div class="service-item-wrapper">
                    <div class="container">
                        <div class="row align-items-center full-screen md-h-auto">
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-image">
                                    <img src="{{ asset($services[1]['image'] ?? 'images/Home_Serv_02.jpg') }}" alt="AI Technology"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-content">
                                    <div class="title">
                                        @if(isset($servicesContent['title']))
                                            <h2 class="section-title">{!! $servicesContent['title'] !!}</h2>
                                        @endif
                                        <h3 class="service-number">02</h3>
                                    </div>

                                    <div class="main-content">
                                        @if(isset($services[1]['title']))
                                            <h3 class="service-title">{!! $services[1]['title'] !!}</h3>
                                        @endif
                                        <p class="service-description">
                                            @if(isset($services[1]['description']))
                                                {!! $services[1]['description'] !!}
                                            @endif
                                        </p>
                                        <div class="service-buttons">
                                            @if(isset($services[1]['buttons']) && is_array($services[1]['buttons']) && count($services[1]['buttons']) > 0)
                                                <a href="{{ $services[1]['buttons'][0]['link'] ?? '/services/ai-and-advanced-technologies' }}"
                                                    class="learn-more {{ $services[1]['buttons'][0]['class'] ?? '' }}">
                                                    @if(isset($services[1]['buttons'][0]['text']))
                                                        {!! $services[1]['buttons'][0]['text'] !!}
                                                    @endif
                                                    <i class="fas fa-arrow-right-long"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Third Service -->
            <div class="service-stack-item" data-service="3">
                <div class="service-overlay"></div>
                <div class="service-item-wrapper">
                    <div class="container">
                        <div class="row align-items-center full-screen md-h-auto">
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-image">
                                    <img src="{{ asset($services[2]['image'] ?? 'images/Home_Serv_03.jpg') }}" alt="Gaming and Esports"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-content">
                                    <div class="title">
                                        @if(isset($servicesContent['title']))
                                            <h2 class="section-title">{!! $servicesContent['title'] !!}</h2>
                                        @endif
                                        <h3 class="service-number">03</h3>
                                    </div>

                                    <div class="main-content">
                                        @if(isset($services[2]['title']))
                                            <h3 class="service-title">{!! $services[2]['title'] !!}</h3>
                                        @endif
                                        <p class="service-description">
                                            @if(isset($services[2]['description']))
                                                {!! $services[2]['description'] !!}
                                            @endif
                                        </p>
                                        <div class="service-buttons">
                                            @if(isset($services[2]['buttons']) && is_array($services[2]['buttons']) && count($services[2]['buttons']) > 0)
                                                <a href="{{ $services[2]['buttons'][0]['link'] ?? '/services/egaming-and-esport' }}"
                                                    class="learn-more {{ $services[2]['buttons'][0]['class'] ?? '' }}">{{ $services[2]['buttons'][0]['text'] ?? 'LEARN MORE' }} <i
                                                    class="fas fa-arrow-right-long"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fourth Service -->
            <div class="service-stack-item" data-service="4">
                <div class="service-overlay"></div>
                <div class="service-item-wrapper">
                    <div class="container">
                        <div class="row align-items-center full-screen md-h-auto">
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-image">
                                    <img src="{{ asset($services[3]['image'] ?? 'images/Home_Serv_04.jpg') }}" alt="Arts and Entertainment"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="service-content">
                                    <div class="title">
                                        @if(isset($servicesContent['title']))
                                            <h2 class="section-title">{!! $servicesContent['title'] !!}</h2>
                                        @endif
                                        <h3 class="service-number">04</h3>
                                    </div>

                                    <div class="main-content">
                                        @if(isset($services[3]['title']))
                                            <h3 class="service-title">{!! $services[3]['title'] !!}</h3>
                                        @endif
                                        <p class="service-description">
                                            @if(isset($services[3]['description']))
                                                {!! $services[3]['description'] !!}
                                            @endif
                                        </p>
                                        <div class="service-buttons">
                                            @if(isset($services[3]['buttons']) && is_array($services[3]['buttons']) && count($services[3]['buttons']) > 0)
                                                <a href="{{ $services[3]['buttons'][0]['link'] ?? '/services/arts-and-entertainment' }}"
                                                    class="learn-more {{ $services[3]['buttons'][0]['class'] ?? '' }}">{{ $services[3]['buttons'][0]['text'] ?? 'LEARN MORE' }} <i
                                                    class="fas fa-arrow-right-long"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Educational Services Section -->
    @php
        $educationalSection = $sections->where('name', 'educational_services')->first();
        $educationalContent = $educationalSection ? $educationalSection->content : [];
        $eduServices = $educationalContent['services'] ?? [];
    @endphp
    @if($educationalSection && $educationalSection->is_active)
    <section id="educational-section" class="educational-services py-5 section">
        <div class="container">
            <div class="row">
                <div class="educational-services mt-5">

                    @if(isset($educationalContent['title']))
                        <h3 class="edu-services-title section-title">{!! $educationalContent['title'] !!}</h3>
                    @endif

                    <div class="service-item mt-4">
                        <div class="row">
                            <div class="col-md-3">
                                <h3 class="service-num">01</h3>
                            </div>
                            <div class="col-md-7">
                                <div class="service-content-wrapper">
                                    @if(isset($eduServices[0]['title']))
                                        <h4 class="service-name">{!! $eduServices[0]['title'] !!}</h4>
                                    @endif
                                    <p class="service-desc">
                                        @if(isset($eduServices[0]['description']))
                                            {!! $eduServices[0]['description'] !!}
                                        @endif
                                    </p>
                                    @if(isset($eduServices[0]['link']))
                                        <a href="{{ $eduServices[0]['link'] }}" class="learn-more">LEARN MORE <i class="fas fa-arrow-right-long"></i></a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="service-toggle">
                                    <i class="fas fa-arrow-right-long"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="service-item mt-4">
                        <div class="row">
                            <div class="col-md-3">
                                <h3 class="service-num">02</h3>
                            </div>
                            <div class="col-md-7">
                                <div class="service-content-wrapper">
                                    @if(isset($eduServices[1]['title']))
                                        <h4 class="service-name">{!! $eduServices[1]['title'] !!}</h4>
                                    @endif
                                    <p class="service-desc">
                                        @if(isset($eduServices[1]['description']))
                                            {!! $eduServices[1]['description'] !!}
                                        @endif
                                    </p>
                                    @if(isset($eduServices[1]['link']))
                                        <a href="{{ $eduServices[1]['link'] }}" class="learn-more">LEARN MORE <i class="fas fa-arrow-right-long"></i></a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="service-toggle">
                                    <i class="fas fa-arrow-right-long"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="service-item mt-4">
                        <div class="row">
                            <div class="col-md-3">
                                <h3 class="service-num">03</h3>
                            </div>
                            <div class="col-md-7">
                                <div class="service-content-wrapper">
                                    @if(isset($eduServices[2]['title']))
                                        <h4 class="service-name">{!! $eduServices[2]['title'] !!}</h4>
                                    @endif
                                    <p class="service-desc">
                                        @if(isset($eduServices[2]['description']))
                                            {!! $eduServices[2]['description'] !!}
                                        @endif
                                    </p>
                                    @if(isset($eduServices[2]['link']))
                                        <a href="{{ $eduServices[2]['link'] }}" class="learn-more">LEARN MORE <i class="fas fa-arrow-right-long"></i></a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="service-toggle">
                                    <i class="fas fa-arrow-right-long"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Statistics Section -->
    @php
        $statsSection = $sections->where('name', 'statistics')->first();
        $statsContent = $statsSection ? $statsSection->content : [];
        $showStats = $statsContent['show_statistics'] ?? false;
        $stats = $statsContent['stats'] ?? [];
    @endphp
    
    @if($statsSection && $statsSection->is_active && $showStats)
    <section id="statistics" class="statistics-section py-5 section">
        <div class="container">
            <div class="row">
                <div class="statistics mt-5">
                    <div class="row">
                        @foreach($stats as $stat)
                        <div class="col-md-4">
                            <div class="stat-item">
                                @if(isset($stat['number']))
                                    <h3 class="stat-number">{!! $stat['number'] !!}</h3>
                                @endif
                                @if(isset($stat['text']))
                                    <p class="stat-text">{!! $stat['text'] !!}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

<!-- Store sections data as JSON for JavaScript access -->
<script>
    window.pageData = @json($sections);
</script>
@endsection

{{-- @push('scripts')
<script>
    // You can access the sections data from the window.pageData object
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page data:', window.pageData);
        
        // Initialize slider functionality
        initSlider();
        
        // You can add more JavaScript functionality here
    });
    
    function initSlider() {
        const slides = document.querySelectorAll('.hero-slider .slide');
        if (slides.length === 0) return;
        
        let currentSlide = 0;
        
        // Show first slide
        slides[0].classList.add('active');
        
        // Function to move to the next slide
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        
        // Auto-advance slides every 5 seconds
        setInterval(nextSlide, 5000);
    }
</script>
@endpush

<style>
    /* Hero Slider Styles */
    .hero-slider {
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }
    
    .slide.active {
        opacity: 1;
    }
    
    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .slide-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        width: 80%;
        max-width: 800px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .cta-container {
        text-align: center;
        margin-top: 20px;
    }
    
    /* Section Styles */
    section {
        padding: 60px 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    /* Services Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .service-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .service-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .service-content {
        padding: 20px;
    }
    
    /* Educational Services */
    .educational-service-item {
        display: flex;
        margin-bottom: 30px;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .service-number {
        font-size: 2rem;
        font-weight: bold;
        margin-right: 20px;
        color: #e0285a;
    }
    
    /* Statistics */
    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        text-align: center;
    }
    
    .statistic-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #e0285a;
    }
</style> --}}
