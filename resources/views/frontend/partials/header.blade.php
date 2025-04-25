<!-- Main Header Content -->
<div class="heading">
    @php
    
    // dd($headerContent);
    use Illuminate\Support\Facades\Route;
    $currentRouteName = Route::currentRouteName();
    $isServicePage = str_starts_with($currentRouteName, 'services.') || request()->is('services/*');
    $isHomePage = $currentRouteName == 'home' || request()->is('/');
    
    // Get header content from the sections collection
    $headerSection = isset($sections) ? $sections->where('name', 'header')->first() : null;
    
    // Check if content is already an array or needs to be decoded
    if ($headerSection) {
        if (is_string($headerSection->content)) {
            $headerContent = json_decode($headerSection->content, true);
        } else {
            $headerContent = $headerSection->content;
        }
    } else {
        $headerContent = [];
    }
    
    // Determine which image to show based on current page
    $headerImage = isset($headerContent['slides'][0]['src']) ? $headerContent['slides'][0]['src'] : 'images/Header/01_EDU_Home.jpg';
    
    if ($currentRouteName == 'about') {
        $headerImage = isset($headerContent['about_image']) ? $headerContent['about_image'] : 'images/About_Page.jpg';
    } elseif (request()->is('services/education-and-scholarship*')) {
        $headerImage = isset($headerContent['service_images']['education']) ? $headerContent['service_images']['education'] : 'images/Header/01_EDU_Home.jpg';
    } elseif (request()->is('services/ai-and-advanced-technologies*')) {
        $headerImage = isset($headerContent['service_images']['ai']) ? $headerContent['service_images']['ai'] : 'images/Header/02_AI_Home.jpg';
    } elseif (request()->is('services/egaming-and-esport*')) {
        $headerImage = isset($headerContent['service_images']['egaming']) ? $headerContent['service_images']['egaming'] : 'images/Header/03_Games_Home.jpg';
    } elseif (request()->is('services/arts-and-entertainment*')) {
        $headerImage = isset($headerContent['service_images']['arts']) ? $headerContent['service_images']['arts'] : 'images/Header/04_Arts_Header.jpg';
    } elseif (request()->is('services/training-and-professional-development*')) {
        $headerImage = isset($headerContent['service_images']['training']) ? $headerContent['service_images']['training'] : 'images/01_Training_Header.jpg';
    }
    @endphp
    <div class="row">
        <!-- Left Column: Headings and Services -->
        <div class="left-col col-sm-6 col-lg-6 order-lg-1 order-2">
            <!-- Dynamic Heading Text -->
            <h1 class="main-heading">
                @if($isHomePage)
                    @if(isset($headerContent['headings']) && is_array($headerContent['headings']))
                        @foreach($headerContent['headings'] as $index => $heading)
                            <span class="heading-text {{ $index == 0 ? 'active' : '' }}" data-slide="{{ $index }}">{!! $heading !!}</span>
                        @endforeach
                    @else
                        <span class="heading-text active" data-slide="0">FROM EDUCATION AND TRAINING TO INNOVATION</span>
                        <span class="heading-text" data-slide="1">THE LATEST AI AND TECHNOLOGIES</span>
                        <span class="heading-text" data-slide="2">INNOVATIVE EFFORTS IN REVOLUTIONIZING THE ESPORT INDUSTRY</span>
                        <span class="heading-text" data-slide="3">BRINGING THE GLOBAL ARTS AND ENTERTAINMENT EVENTS TO TOWN</span>
                    @endif
                @elseif(request()->is('about*'))
                    @if(isset($headerContent['aboutPageHeading']))
                        <span class="heading-text active">{!! $headerContent['aboutPageHeading'] !!}</span>
                    @endif
                @elseif(request()->is('services/education-and-scholarship*'))
                    @if(isset($headerContent['serviceHeadings']['education']))
                        <span class="heading-text active">{!! $headerContent['serviceHeadings']['education'] !!}</span>
                    @endif
                @elseif(request()->is('services/ai-and-advanced-technologies*'))
                    @if(isset($headerContent['serviceHeadings']['ai']))
                        <span class="heading-text active">{!! $headerContent['serviceHeadings']['ai'] !!}</span>
                    @endif
                @elseif(request()->is('services/egaming-and-esport*'))
                    @if(isset($headerContent['serviceHeadings']['egaming']))
                        <span class="heading-text active">{!! $headerContent['serviceHeadings']['egaming'] !!}</span>
                    @endif
                @elseif(request()->is('services/arts-and-entertainment*'))
                    @if(isset($headerContent['serviceHeadings']['arts']))
                        <span class="heading-text active">{!! $headerContent['serviceHeadings']['arts'] !!}</span>
                    @endif
                @elseif(request()->is('services/training-and-professional-development*'))
                    @if(isset($headerContent['serviceHeadings']['training']))
                        <span class="heading-text active">{!! $headerContent['serviceHeadings']['training'] !!}</span>
                    @endif
                @else
                    @if(isset($headerContent['errorHeading']))
                        <span class="heading-text active">{!! $headerContent['errorHeading'] !!}</span>
                    @endif
                @endif
            </h1>
            
            <!-- Services Menu -->
            <div class="services-menu {{ $isServicePage ? 'active' : '' }}">
                @if(isset($headerContent['servicesMenuTitle']))
                    <h3>{!! $headerContent['servicesMenuTitle'] !!}</h3>
                @endif
                <ul class="service-list">
                    @if(isset($headerContent['servicesMenuLinks']) && is_array($headerContent['servicesMenuLinks']))
                        @foreach($headerContent['servicesMenuLinks'] as $service)
                            <li>
                                <div class="link-container">
                                    <a href="{{ url($service['link'] ?? '#') }}" class="{{ request()->is(ltrim($service['link'] ?? '', '/')) ? 'active' : '' }}">
                                        {{ $service['title'] ?? '' }}
                                        <i class="fas fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    @else
                        <li>
                            <div class="link-container">
                                <a href="{{ url('/services/education-and-scholarship') }}" class="{{ request()->is('services/education-and-scholarship') ? 'active' : '' }}">
                                    Education and Scholarship
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="link-container">
                                <a href="{{ url('/services/training-and-professional-development') }}" class="{{ request()->is('services/training-and-professional-development') ? 'active' : '' }}">
                                    Training and Professional Development
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="link-container">
                                <a href="{{ url('/services/ai-and-advanced-technologies') }}" class="{{ request()->is('services/ai-and-advanced-technologies') ? 'active' : '' }}">
                                    AI and Advanced Technologies
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="link-container">
                                <a href="{{ url('/services/egaming-and-esport') }}" class="{{ request()->is('services/egaming-and-esport') ? 'active' : '' }}">
                                    E-Gaming and eSport
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="link-container">
                                <a href="{{ url('/services/arts-and-entertainment') }}" class="{{ request()->is('services/arts-and-entertainment') ? 'active' : '' }}">
                                    Arts and Entertainment
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
                <!-- Copy of Let's Talk button for mobile view -->
                <a href="{{ url('/#contact') }}" class="btn btn-talk">
                    @if(isset($headerContent['talkButtonText']))
                        {!! $headerContent['talkButtonText'] !!}
                    @endif
                </a>
            </div>
        </div>
        
        <!-- Right Column: Image Carousel or Static Image -->
        <div class="col-lg-6 col-sm-6 order-lg-2 order-1 mb-4 mb-lg-0 header-main-carousel">
            <div class="header-image">
                @if($isHomePage)
                <!-- Bootstrap Carousel for Home Page -->
                <div id="headerCarousel" class="carousel slide custom-transition" data-bs-ride="carousel">
                    <!-- Hidden Default Indicators -->
                    <div class="carousel-indicators">
                        @if(isset($headerContent['slides']) && is_array($headerContent['slides']))
                            @foreach($headerContent['slides'] as $index => $slide)
                                <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        @else
                            <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                        @endif
                    </div>
                    
                    <!-- Carousel Slides -->
                    <div class="carousel-inner">
                        @if(isset($headerContent['slides']) && is_array($headerContent['slides']))
                            @foreach($headerContent['slides'] as $index => $slide)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset($slide['src']) }}" class="d-block w-100" alt="{{ $slide['alt'] ?? 'Slide ' . ($index + 1) }}">
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img src="{{ asset('images/Header/01_EDU_Home.jpg') }}" class="d-block w-100" alt="Education">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/Header/02_AI_Home.jpg') }}" class="d-block w-100" alt="AI">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/Header/03_Games_Home.jpg') }}" class="d-block w-100" alt="Gaming">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/Header/04_Arts_Header.jpg') }}" class="d-block w-100" alt="Arts">
                            </div>
                        @endif
                    </div>
                    
                    <!-- Custom Carousel Navigation -->
                    <div class="carousel-nav-numbers">
                        @if(isset($headerContent['slides']) && is_array($headerContent['slides']))
                            @foreach($headerContent['slides'] as $index => $slide)
                                <div class="nav-number {{ $index == 0 ? 'active' : '' }}" data-slide="{{ $index }}">{{ sprintf('%02d', $index + 1) }}</div>
                                @if($index < count($headerContent['slides']) - 1)
                                    <div class="nav-line">
                                        <div class="nav-line-fill"></div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="nav-number active" data-slide="0">01</div>
                            <div class="nav-line">
                                <div class="nav-line-fill"></div>
                            </div>
                            <div class="nav-number" data-slide="1">02</div>
                            <div class="nav-line">
                                <div class="nav-line-fill"></div>
                            </div>
                            <div class="nav-number" data-slide="2">03</div>
                            <div class="nav-line">
                                <div class="nav-line-fill"></div>
                            </div>
                            <div class="nav-number" data-slide="3">04</div>
                            <div class="nav-line">
                                <div class="nav-line-fill"></div>
                            </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Static Image for Other Pages -->
                <div class="static-header-image">
                    <img src="{{ asset($headerImage) }}" class="d-block w-100" alt="Page Header">
                </div>
                @endif
            </div>
        </div>
    </div>
</div> 