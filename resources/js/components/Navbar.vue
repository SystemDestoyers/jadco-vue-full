<template>
    <!-- Header Section -->
    <header class="header" id="home">
        <div class="container">
            <!-- Navigation Bar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <!-- Logo Container (Left-aligned) -->
                    <a class="navbar-brand" href="/">
                        <img v-if="navbarContent.logo" :src="navbarContent.logo" alt="JADCO Logo" class="logo">
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
                            <template v-if="navbarContent.navItems && navbarContent.navItems.length">
                                <li v-for="(item, index) in navbarContent.navItems" :key="index" class="nav-item">
                                    <router-link 
                                        v-if="item.link.startsWith('/')" 
                                        :to="item.link" 
                                        class="nav-link"
                                        :class="item.text.toLowerCase()"
                                        :id="item.id || null"
                                        v-html="item.text">
                                    </router-link>
                                    <a 
                                        v-else 
                                        :href="item.link" 
                                        class="nav-link"
                                        :class="item.text.toLowerCase()"
                                        :id="item.id || null"
                                        v-html="item.text">
                                    </a>
                                </li>
                            </template>
                        </ul>
                        <a href="/#contact" class="btn btn-talk" @click.prevent="handleLetsTalkClick">
                            <span v-if="navbarContent.talkButtonText" v-html="navbarContent.talkButtonText"></span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <Header />
        </div>
    </header>
</template>

<script>
import Header from './Header.vue';
import { ref, onMounted, computed } from 'vue';

export default {
    components: {
        Header
    },
    setup() {
        const navbarContent = ref({});
        
        const fetchNavbarContent = async () => {
            try {
                // Fetch navbar content from API
                const response = await fetch('/api/navbar/section');
                if (response.ok) {
                    const result = await response.json();
                    if (result && result.success && result.data && result.data.content) {
                        navbarContent.value = result.data.content;
                    }
                }
            } catch (error) {
                console.error('Error fetching navbar content:', error);
            }
        };
        
        onMounted(() => {
            fetchNavbarContent();
            
            // Import static.js to get the createFlashEffect function
            const script = document.createElement('script');
            script.src = '/js/partial/static.js';
            document.head.appendChild(script);
        });
        
        const isHome = computed(() => {
            return window.location.pathname === '/';
        });
        
        const isAboutActive = computed(() => {
            return window.location.pathname === '/about';
        });
        
        const handleLetsTalkClick = (e) => {
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
        };
        
        return {
            navbarContent,
            isHome,
            isAboutActive,
            handleLetsTalkClick
        };
    }
};
</script>

<style scoped>
.nav-link.active {
    color: var(--primary-color) !important;
    font-weight: 500;
}
</style> 