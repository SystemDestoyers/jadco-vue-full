@php
use Illuminate\Support\Facades\Route;

// Get navbar content from the sections collection
$navbarSection = isset($sections) ? $sections->where('name', 'navbar')->first() : null;

// Check if content is already an array or needs to be decoded
if ($navbarSection) {
    if (is_string($navbarSection->content)) {
        $navbarContent = json_decode($navbarSection->content, true);
    } else {
        $navbarContent = $navbarSection->content;
    }
} else {
    $navbarContent = [];
}
@endphp
<!-- Header Section -->
<header class="header" id="home">
    <div class="container">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Logo Container (Left-aligned) -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if(isset($navbarContent['logo']))
                        <img src="{{ asset($navbarContent['logo']) }}" alt="JADCO Logo" class="logo">
                    @endif
                </a>
                
                <!-- Mobile Toggle Button (Right-aligned) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Items -->
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav me-3">
                        @if(isset($navbarContent['navItems']) && is_array($navbarContent['navItems']))
                            @foreach($navbarContent['navItems'] as $item)
                                <li class="nav-item">
                                    <a class="nav-link {{ strtolower($item['text']) }} {{ (Route::currentRouteName() == strtolower($item['text']) || request()->is($item['link'] == '/' ? '/' : trim($item['link'], '/'))) ? 'active' : '' }}" 
                                       href="{{ url($item['link']) }}" 
                                       @if(!empty($item['id'])) id="{{ $item['id'] }}" @endif>
                                        {!! $item['text'] !!}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <a href="{{ url('/#contact') }}" class="btn btn-talk">
                        @if(isset($navbarContent['talkButtonText']))
                            {!! $navbarContent['talkButtonText'] !!}
                        @endif
                    </a>
                </div>
            </div>
        </nav>
        
        @include('frontend.partials.header')
    </div>
</header> 