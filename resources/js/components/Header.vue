<template>
    <!-- Main Header Content -->
    <div class="heading" v-if="!loading">
        <div class="row">
            <!-- Left Column: Headings and Services -->
            <div class="left-col col-sm-6 col-lg-6 order-lg-1 order-2">
                <!-- Dynamic Heading Text -->
                <h1 class="main-heading">
                    <span v-if="isHomePage && content.headings?.[0]" class="heading-text" :class="{ 'active': currentSlide === 0 }" data-slide="0" v-html="content.headings[0]"></span>
                    <span v-if="isHomePage && content.headings?.[1]" class="heading-text" :class="{ 'active': currentSlide === 1 }" data-slide="1" v-html="content.headings[1]"></span>
                    <span v-if="isHomePage && content.headings?.[2]" class="heading-text" :class="{ 'active': currentSlide === 2 }" data-slide="2" v-html="content.headings[2]"></span>
                    <span v-if="isHomePage && content.headings?.[3]" class="heading-text" :class="{ 'active': currentSlide === 3 }" data-slide="3" v-html="content.headings[3]"></span>
                    
                    <span v-if="$route.path.includes('about') && content.aboutPageHeading" class="heading-text active" v-html="content.aboutPageHeading"></span>
                    <span v-if="$route.path.includes('services/education-and-scholarship') && content.serviceHeadings?.education" class="heading-text active" v-html="content.serviceHeadings.education"></span>
                    <span v-if="$route.path.includes('services/ai-and-advanced-technologies') && content.serviceHeadings?.ai" class="heading-text active" v-html="content.serviceHeadings.ai"></span>
                    <span v-if="$route.path.includes('services/egaming-and-esport') && content.serviceHeadings?.egaming" class="heading-text active" v-html="content.serviceHeadings.egaming"></span>
                    <span v-if="$route.path.includes('services/arts-and-entertainment') && content.serviceHeadings?.arts" class="heading-text active" v-html="content.serviceHeadings.arts"></span>
                    <span v-if="$route.path.includes('services/training-and-professional-development') && content.serviceHeadings?.training" class="heading-text active" v-html="content.serviceHeadings.training"></span>
                    <span v-if="!isHomePage && !$route.path.includes('about') && !$route.path.includes('services/') && content.errorHeading" class="heading-text active" v-html="content.errorHeading"></span>
                </h1>
                
                <!-- Services Menu -->
                <div class="services-menu">
                    <h3 v-if="content.servicesMenuTitle" v-html="content.servicesMenuTitle"></h3>
                    <ul class="service-list" v-if="servicesMenuLinks.length > 0">
                        <li v-for="(service, index) in servicesMenuLinks" :key="index">
                            <div class="link-container">
                                <router-link :to="service.link" :class="{ 'active': $route.path.includes(service.link) }">
                                    <span v-html="service.title || ''"></span>
                                    <i class="fas fa-arrow-right-long"></i>
                                </router-link>
                            </div>
                        </li>
                    </ul>
                    <!-- Copy of Let's Talk button for mobile view -->
                    <a href="#contact" class="btn btn-talk" @click.prevent="handleLetsTalkClick" v-html="content.talkButtonText || ''"></a>
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
                                <div class="nav-line" v-for="i in (carouselSlides.length - 1)" :key="`line-${i}`">
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
            headerImage: '',
            servicesMenuInitialized: false,
            loading: true,
            content: {
                headings: [],
                aboutPageHeading: '',
                aboutPageHeader_image: '',
                serviceHeadings: {},
                errorHeading: '',
                servicesMenuTitle: '',
                talkButtonText: '',
                service_images: {
                    education: '',
                    ai: '',
                    egaming: '',
                    arts: '',
                    training: ''
                }
            },
            servicesMenuLinks: [
                { title: '', link: '' },
            ],
            carouselSlides: [
                { src: '', alt: '' },
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
            this.loading = true;
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
                        if (content.aboutPageHeading) this.content.aboutPageHeading = content.aboutPageHeading;
                        if (content.serviceHeadings) this.content.serviceHeadings = content.serviceHeadings;
                        if (content.errorHeading) this.content.errorHeading = content.errorHeading;
                        if (content.servicesMenuTitle) this.content.servicesMenuTitle = content.servicesMenuTitle;
                        if (content.talkButtonText) this.content.talkButtonText = content.talkButtonText;
                        if (content.aboutPageHeader_image) this.content.aboutPageHeader_image = content.aboutPageHeader_image;
                        if (content.service_images) this.content.service_images = content.service_images;
                        
                        // Update services if provided in content
                        if (content.servicesMenuLinks) {
                            this.servicesMenuLinks = content.servicesMenuLinks;
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
                // Update the header image after content is loaded
                this.updateHeaderImage();
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
            // Set header image based on current route using dynamic content
            if (this.$route.path.includes('/about')) {
                // Use dynamic about page header image from content
                this.headerImage = this.content.aboutPageHeader_image;
            } else if (this.$route.path.includes('/services/education-and-scholarship')) {
                this.headerImage = this.content.service_images?.education;
            } else if (this.$route.path.includes('/services/ai-and-advanced-technologies')) {
                this.headerImage = this.content.service_images?.ai;
            } else if (this.$route.path.includes('/services/egaming-and-esport')) {
                this.headerImage = this.content.service_images?.egaming;
            } else if (this.$route.path.includes('/services/arts-and-entertainment')) {
                this.headerImage = this.content.service_images?.arts;
            } else if (this.$route.path.includes('/services/training-and-professional-development')) {
                this.headerImage = this.content.service_images?.training;
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
        // Load the header data first
        await this.fetchHeaderData();
    },
    mounted() {
        // Load the services menu manager
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
        },
        // Update header image when content changes
        'content': {
            handler: function(newContent, oldContent) {
                // Update header image if service_images or aboutPageHeader_image changed
                const serviceImagesChanged = JSON.stringify(newContent.service_images) !== JSON.stringify(oldContent.service_images);
                const aboutImageChanged = newContent.aboutPageHeader_image !== oldContent.aboutPageHeader_image;
                
                if (serviceImagesChanged || aboutImageChanged) {
                    this.updateHeaderImage();
                }
            },
            deep: true
        }
    }
};
</script> 