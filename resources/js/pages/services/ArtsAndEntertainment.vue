<template>
    <div class="sub-page">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading content...</p>
        </div>

        <div v-else>
            <!-- Service Detail Hero Section -->
            <section class="ai-pages service-hero-section py-10">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="service-title">{{ content.title }}</h1>
                            <p class="service-description">
                                {{ content.description }}
                            </p>
                        </div>
                    </div>
                    <!-- Service Details -->
                    <div class="service-details py-5">
                        <div class="row">
                            <div class="col-md-6">
                                <img :src="content.image" :alt="content.title" class="img-fluid service-hero-image">
                            </div>
                            <div class="col-md-6">
                                <div class="service-text-content">
                                    <h2>{{ content.subtitle }}</h2>
                                    <ul v-if="content.services && content.services.length > 0" class="service-list">
                                        <li v-for="(item, index) in content.services" :key="index">{{ item }}</li>
                                    </ul>
                                </div>
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
                title: 'Arts and Entertainment',
                description: 'Bringing the fine Arts, culture and entertainment from the globe to enrich the local diversity and enhance the picture of the Arabian culture overseas by adding value to the industry.',
                image: '/images/06_Arts/01.jpg',
                subtitle: 'From Training and education in Arts & Entertainment subjects, to customizing projects and live events in association with our local and international partners.',
                services: []
            }
        };
    },
    async created() {
        await this.fetchData();
    },
    mounted() {
        document.title = 'JADCO - Arts and Entertainment';
    },
    methods: {
        async fetchData() {
            try {
                // Try to get data from the API
                const response = await axios.get('/api/arts/sections');
                
                if (response.data && response.data.content) {
                    // If there's content data in the response, process it
                    let content = response.data.content;
                    
                    // If the content is a string, parse it
                    if (typeof content === 'string') {
                        content = JSON.parse(content);
                    }
                    
                    // Replace the content with data from API
                    this.content = content;
                }
            } catch (error) {
                console.error('Error fetching arts and entertainment service data:', error);
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