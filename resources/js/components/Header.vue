<template>
    <!-- Main Header Content -->
    <div class="heading">
        <div class="row">
            <!-- Left Column: Headings and Services -->
            <div class="left-col col-sm-6 col-lg-6 order-lg-1 order-2">
                <!-- Dynamic Heading Text -->
                <h1 class="main-heading">
                    <span v-if="isHomePage" class="heading-text" :class="{ 'active': currentSlide === 0 }" data-slide="0">{{ content.headings?.[0] || 'From Education and Training to Innovation' }}</span>
                    <span v-if="isHomePage" class="heading-text" :class="{ 'active': currentSlide === 1 }" data-slide="1">{{ content.headings?.[1] || 'The Latest AI and Technologies' }}</span>
                    <span v-if="isHomePage" class="heading-text" :class="{ 'active': currentSlide === 2 }" data-slide="2">{{ content.headings?.[2] || 'Innovative Efforts in Revolutionizing the eSport Industry' }}</span>
                    <span v-if="isHomePage" class="heading-text" :class="{ 'active': currentSlide === 3 }" data-slide="3">{{ content.headings?.[3] || 'Bringing the global Arts and Entertainment Events to town' }}</span>
                    
                    <span v-if="$route.path.includes('about')" class="heading-text active">{{ content.aboutHeading || 'We Listen, design your vision and bring it to life... Let\'s talk' }}</span>
                    <span v-if="$route.path.includes('services/education-and-scholarship')" class="heading-text active">{{ content.serviceHeadings?.education || 'From Education and Training to Innovation' }}</span>
                    <span v-if="$route.path.includes('services/ai-and-advanced-technologies')" class="heading-text active">{{ content.serviceHeadings?.ai || 'The Latest AI and Technologies' }}</span>
                    <span v-if="$route.path.includes('services/egaming-and-esport')" class="heading-text active">{{ content.serviceHeadings?.egaming || 'Innovative Efforts in Revolutionizing the eSport Industry' }}</span>
                    <span v-if="$route.path.includes('services/arts-and-entertainment')" class="heading-text active">{{ content.serviceHeadings?.arts || 'Bringing the Global Arts and Entertainment Events to town' }}</span>
                    <span v-if="$route.path.includes('services/training-and-professional-development')" class="heading-text active">{{ content.serviceHeadings?.training || 'From Education and Training to Innovation' }}</span>
                    <span v-if="!isHomePage && !$route.path.includes('about') && !$route.path.includes('services/')" class="heading-text active">{{ content.errorHeading || 'JADCO Error page' }}</span>
                </h1>
                
                <!-- Services Menu -->
                <div class="services-menu">
                    <h3>{{ content.servicesTitle || 'SERVICES' }}</h3>
                    <ul class="service-list">
                        <li v-for="(service, index) in services" :key="index">
                            <div class="link-container">
                                <router-link :to="service.link" :class="{ 'active': $route.path.includes(service.link) }">
                                    {{ service.title }}
                                    <i class="fas fa-arrow-right-long"></i>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                    <!-- Copy of Let's Talk button for mobile view -->
                    <a href="#contact" class="btn btn-talk" @click.prevent="handleLetsTalkClick">{{ content.talkButtonText || 'Let\'s Talk' }}</a>
                </div>
            </div>
            
            <!-- Right Column: Image Carousel or Static Image -->
            <div class="col-lg-6 col-sm-6 order-lg-2 order-1 mb-4 mb-lg-0 header-main-carousel">
                <div class="header-image">
                    <div v-if="isHomePage">
                        <!-- Bootstrap Carousel for Home Page -->
                        <div id="headerCarousel" class="carousel slide custom-transition" data-bs-ride="carousel">
                            <!-- Hidden Default Indicators -->
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                <button type="button" data-bs-target="#headerCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                            </div>
                            
                            <!-- Carousel Slides -->
                            <div class="carousel-inner">
                                <div v-for="(slide, index) in carouselSlides" :key="index" class="carousel-item" :class="{ 'active': index === 0 }">
                                    <img :src="slide.src" class="d-block w-100" :alt="slide.alt">
                                </div>
                            </div>
                            
                            <!-- Custom Carousel Navigation -->
                            <div class="carousel-nav-numbers">
                                <div 
                                    v-for="index in carouselSlides.length" 
                                    :key="index"
                                    class="nav-number" 
                                    :class="{ 'active': currentSlide === index - 1 }" 
                                    :data-slide="index - 1"
                                    @click="setSlide(index - 1)">
                                    {{ index.toString().padStart(2, '0') }}
                                </div>
                                <div v-if="index < carouselSlides.length" class="nav-line" v-for="index in (carouselSlides.length - 1)" :key="`line-${index}`">
                                    <div class="nav-line-fill"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="static-header-image">
                        <!-- Static Image for Other Pages -->
                        <img :src="headerImage" class="d-block w-100" alt="Page Header">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            currentSlide: 0,
            carousel: null,
            headerImage: '/images/Header/01_EDU_Home.jpg',
            servicesMenuInitialized: false,
            loading: true,
            content: {
                headings: [
                    'From Education and Training to Innovation',
                    'The Latest AI and Technologies',
                    'Innovative Efforts in Revolutionizing the eSport Industry',
                    'Bringing the global Arts and Entertainment Events to town'
                ],
                aboutHeading: 'We Listen, design your vision and bring it to life... Let\'s talk',
                serviceHeadings: {
                    education: 'From Education and Training to Innovation',
                    ai: 'The Latest AI and Technologies',
                    egaming: 'Innovative Efforts in Revolutionizing the eSport Industry',
                    arts: 'Bringing the Global Arts and Entertainment Events to town',
                    training: 'From Education and Training to Innovation'
                },
                errorHeading: 'JADCO Error page',
                servicesTitle: 'SERVICES',
                talkButtonText: 'Let\'s Talk'
            },
            services: [
                { title: 'Education and Scholarship', link: '/services/education-and-scholarship' },
                { title: 'Training and Professional Development', link: '/services/training-and-professional-development' },
                { title: 'AI and Advanced Technologies', link: '/services/ai-and-advanced-technologies' },
                { title: 'E-Gaming and eSport', link: '/services/egaming-and-esport' },
                { title: 'Arts and Entertainment', link: '/services/arts-and-entertainment' }
            ],
            carouselSlides: [
                { src: '/images/Header/01_EDU_Home.jpg', alt: 'Education' },
                { src: '/images/Header/02_AI_Home.jpg', alt: 'AI' },
                { src: '/images/Header/03_Games_Home.jpg', alt: 'Gaming' },
                { src: '/images/Header/04_Arts_Header.jpg', alt: 'Arts' }
            ]
        };
    },
    computed: {
        isHomePage() {
            return this.$route.path === '/';
        },
        isServicePage() {
            return this.$route.path.includes('/services/');
        }
    },
    methods: {
        async fetchHeaderData() {
            try {
                // Get header content from API
                const response = await axios.get('/api/header/sections');
                
                if (response.data && response.data.success) {
                    const headerSection = response.data.data;
                    
                    if (headerSection && headerSection.content) {
                        // Parse content if needed
                        let content = headerSection.content;
                        if (typeof content === 'string') {
                            content = JSON.parse(content);
                        }
                        
                        // Only update the content properties, preserving component structure
                        if (content.headings) this.content.headings = content.headings;
                        if (content.aboutHeading) this.content.aboutHeading = content.aboutHeading;
                        if (content.serviceHeadings) this.content.serviceHeadings = content.serviceHeadings;
                        if (content.errorHeading) this.content.errorHeading = content.errorHeading;
                        if (content.servicesTitle) this.content.servicesTitle = content.servicesTitle;
                        if (content.talkButtonText) this.content.talkButtonText = content.talkButtonText;
                        
                        // Update services if provided in content
                        if (content.services && content.services.length) {
                            this.services = content.services;
                        }
                        
                        // Update slides if provided in content
                        if (content.slides && content.slides.length) {
                            this.carouselSlides = content.slides;
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching header data:', error);
                // Use default content on error
            } finally {
                this.loading = false;
            }
        },
        setSlide(index) {
            this.currentSlide = index;
            if (this.carousel) {
                this.carousel.to(index);
            }
        },
        handleLetsTalkClick(e) {
            e.preventDefault();
            
            // Recreate the flash effect from static.js if available
            if (typeof createFlashEffect === 'function') {
                createFlashEffect();
                
                // Scroll to contact section after flash with a delay
                setTimeout(() => {
                    const contactSection = document.getElementById('contact');
                    if (contactSection) {
                        contactSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 1200); // Adjust timing based on flash effect duration
            } else {
                // Directly scroll to contact section without flash effect
                const contactSection = document.getElementById('contact');
                if (contactSection) {
                    contactSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        },
        updateHeaderImage() {
            // Set header image based on current route
            if (this.$route.path.includes('/about')) {
                this.headerImage = '/images/About_Page.jpg';
            } else if (this.$route.path.includes('/services/education-and-scholarship')) {
                this.headerImage = '/images/Header/01_EDU_Home.jpg';
            } else if (this.$route.path.includes('/services/ai-and-advanced-technologies')) {
                this.headerImage = '/images/Header/02_AI_Home.jpg';
            } else if (this.$route.path.includes('/services/egaming-and-esport')) {
                this.headerImage = '/images/Header/03_Games_Home.jpg';
            } else if (this.$route.path.includes('/services/arts-and-entertainment')) {
                this.headerImage = '/images/Header/04_Arts_Header.jpg';
            } else if (this.$route.path.includes('/services/training-and-professional-development')) {
                this.headerImage = '/images/01_Training_Header.jpg';
            }
            
            // Reset and replay the header image animation on service page navigation
            this.$nextTick(() => {
                this.animateHeaderImage();
            });
        },
        animateHeaderImage() {
            const headerImage = document.querySelector('.header-image');
            const staticImage = document.querySelector('.static-header-image');
            
            if (!headerImage) return;
            
            // Only animate when switching between service pages (not on initial load)
            if (this.isServicePage && staticImage) {
                // Remove the animate class
                headerImage.classList.remove('animate');
                
                // Force a reflow to restart the animation
                void headerImage.offsetWidth;
                
                // Add the animate class back after a short delay
                setTimeout(() => {
                    headerImage.classList.add('animate');
                }, 10);
            }
        },
        initServicesMenu() {
            // Use the centralized services menu manager
            if (window.ServicesMenuManager) {
                window.ServicesMenuManager.init();
                this.servicesMenuInitialized = true;
                return;
            }
        }
    },
    async created() {
        await this.fetchHeaderData();
    },
    mounted() {
        this.updateHeaderImage();
        
        // Load the services menu manager first
        const managerScript = document.createElement('script');
        managerScript.src = '/js/partial/services-menu-manager.js';
        managerScript.onload = () => {
            setTimeout(this.initServicesMenu, 100);
        };
        document.head.appendChild(managerScript);
        
        // Initialize carousel with Bootstrap
        if (this.isHomePage) {
            // Wait for Bootstrap to load
            setTimeout(() => {
                // Import carousel-related JavaScript
                const script = document.createElement('script');
                script.src = '/js/partial/header.js';
                document.head.appendChild(script);
                
                // Initialize the carousel manually using Bootstrap's carousel
                if (typeof bootstrap !== 'undefined') {
                    const carouselElement = document.getElementById('headerCarousel');
                    if (carouselElement) {
                        this.carousel = new bootstrap.Carousel(carouselElement, {
                            interval: 5000
                        });
                        
                        // Add slide change event listener
                        carouselElement.addEventListener('slide.bs.carousel', (event) => {
                            this.currentSlide = event.to;
                        });
                    }
                }
            }, 1000);
        }
    },
    updated() {
        // Re-check services menu initialization
        if (!this.servicesMenuInitialized) {
            this.initServicesMenu();
        }
    },
    watch: {
        $route() {
            this.updateHeaderImage();
            
            // Reset services menu manager on route change
            if (window.resetServicesMenuManager) {
                window.resetServicesMenuManager();
            }
            
            this.servicesMenuInitialized = false; // Reset initialization flag
            
            // Re-initialize services menu with a short delay
            setTimeout(this.initServicesMenu, 200);
        },
        // Watch specifically for changes in the service path segment
        '$route.path': function(newPath, oldPath) {
            // Only trigger animation when moving between service pages
            if (newPath.includes('/services/') && oldPath.includes('/services/') && newPath !== oldPath) {
                // Wait for DOM to update with new image
                this.$nextTick(() => {
                    // Force header image animation to replay
                    this.animateHeaderImage();
                });
            }
        }
    }
};
</script> 