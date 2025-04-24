<template>
    <div class="sub-page">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading content...</p>
        </div>

        <div v-else>
            <!-- Service Detail Hero Section -->
            <section v-if="content.fellowship" class="service-hero-section py-10">
                <div class="container">
                    <div class="row">
                        <div class="educational-services sub-section">                
                            <div class="service-item mt-4">
                                <div class="row">
                                    <div class="col-3">
                                        <h3 class="service-num">01</h3>
                                    </div>
                                    <div class="col-9 head-content">
                                        <div class="service-content-wrapper">
                                            <h4 class="service-title">{{ content.fellowship.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.fellowship.description }}                                   
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img :src="content.fellowship.image" alt="Training" class="img-fluid service-hero-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <p v-html="content.fellowship.mainContent">
                                </p>
                                <ul class="service-list">
                                    <li v-for="(item, index) in content.fellowship.services" :key="index">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technical and Vocational Training Section -->
            <section v-if="content.technical" class="service-hero-section py-10">
                <div class="container">
                    <div class="row">
                        <div class="educational-services sub-section">                
                            <div class="service-item mt-4">
                                <div class="row">
                                    <div class="col-3">
                                        <h3 class="service-num">02</h3>
                                    </div>
                                    <div class="col-9 head-content">
                                        <div class="service-content-wrapper">
                                            <h4 class="service-title">{{ content.technical.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.technical.description }}                                
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <p v-html="content.technical.mainContent">
                                </p>
                                <h5>{{ content.technical.listTitle }}</h5>
                                <ul class="service-list">
                                    <li v-for="(item, index) in content.technical.services" :key="index">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img :src="content.technical.image" alt="Technical and Vocational Training" class="img-fluid service-hero-image">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Online Professional Programs Section -->
            <section v-if="content.online" class="service-hero-section py-10">
                <div class="container">
                    <div class="row">
                        <div class="educational-services sub-section">                
                            <div class="service-item mt-4">
                                <div class="row">
                                    <div class="col-3">
                                        <h3 class="service-num">03</h3>
                                    </div>
                                    <div class="col-9 head-content">
                                        <div class="service-content-wrapper">
                                            <h4 class="service-title">{{ content.online.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.online.description }}                               
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <img :src="content.online.image" alt="Online Professional Programs" class="img-fluid service-hero-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <p v-html="content.online.mainContent">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            loading: true,
            content: {
                fellowship: {
                    title: '',
                    description: '',
                    image: '',
                    mainContent: '',
                    services: [
                    ]
                },
                technical: {
                    title: '',
                    description: '',
                    image: '',
                    mainContent: '',
                    listTitle: '',
                    services: [
                    ]
                },
                online: {
                    title: '',
                    description: '',
                    image: '',
                    mainContent: '',
                }
            }
        };
    },
    async created() {
        await this.fetchData();
    },
    mounted() {
        document.title = 'JADCO - Training and Professional Development';
    },
    methods: {
        async fetchData() {
            try {
                // Try to get data from the API
                const response = await axios.get('/api/training-and-professional-development/sections');
                console.log('API Response:', response.data);
                
                if (response.data && response.data.success && response.data.data && Array.isArray(response.data.data)) {
                    const sections = response.data.data;
                    
                    // Process each section and update content accordingly
                    sections.forEach(section => {
                        let sectionContent = section.content;
                        
                        // Parse content if it's a string
                        if (typeof sectionContent === 'string') {
                            try {
                                sectionContent = JSON.parse(sectionContent);
                            } catch (error) {
                                console.error('Error parsing section content:', error);
                            }
                        }
                        
                        // Map each section to the appropriate content property
                        if (section.name === 'fellowship') {
                            this.content.fellowship = sectionContent;
                        } else if (section.name === 'technical') {
                            this.content.technical = sectionContent;
                        } else if (section.name === 'online') {
                            this.content.online = sectionContent;
                        }
                    });
                }
            } catch (error) {
                console.error('Error fetching training service data:', error);
                // Fallback to default content defined in data()
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    padding: 50px 0;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #e0285a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style> 