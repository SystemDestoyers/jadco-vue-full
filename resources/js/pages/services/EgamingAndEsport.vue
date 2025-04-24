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
                                    <h2>{{ content.listTitle }}</h2>
                                    <ul class="service-list">
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
                title: 'eGaming and eSport',
                description: 'JADCO and international partners in gaming and eSport, USA highly ranked universities in gaming and simulation development and integrated e-Arts programs and a Consortium firm supported by the U.S department of Commerce (International Trade Administration), are together forming a consortium to propose a broad-based support package and plans for e-gaming and eSport to help and greatly accelerate the Kingdom\'s positioning as a leader in this industry worldwide by leveraging international relevant partners, SMEs, and other resources to support developing the sector\'s entire value chain and achieve the objectives of the Saudi Arabia\'s newly gaming, eSport and AI strategy.',
                image: '/images/05_eGame/01.jpg',
                listTitle: 'What we do:',
                services: [
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
            }
        };
    },
    async created() {
        await this.fetchData();
    },
    mounted() {
        document.title = 'JADCO - eGaming and eSport';
    },
    methods: {
        async fetchData() {
            try {
                // Try to get data from the API
                const response = await axios.get('/api/egaming/sections');
                
                if (response.data && response.data.success) {
                    // If there's sections data in the response, process it
                    const serviceData = response.data.data || response.data.service;
                    
                    if (serviceData && serviceData.content) {
                        // If the content is a string, parse it
                        let content = serviceData.content;
                        if (typeof content === 'string') {
                            content = JSON.parse(content);
                        }
                        
                        // Merge the fetched content with default content
                        this.content = {
                            ...this.content,
                            ...content
                        };
                    }
                }
            } catch (error) {
                console.error('Error fetching egaming service data:', error);
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