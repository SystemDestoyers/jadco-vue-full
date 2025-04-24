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
                            <p class="service-description" v-html="content.description">
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
                                    <p v-html="content.mainContent">
                                    </p>
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
                title: 'Artificial Intelligence (AI) and Advanced Tech',
                description: 'AI represents a transformative technology with the potential to revolutionize organizations services and operations.<br>By leveraging AI, organizations can enhance efficiency, improve decision-making and deliver superior to public.<br>By investing in AI education, training and projects, organizations can better meet the needs of their people, drive innovation and ensure sustainable development.',
                image: '/images/04_AI/01.jpg',
                subtitle: 'We Customize Transformation Projects',
                mainContent: 'JADCO and partners support to harness the power of AI and digital technologies to help improve business operations and organization thrive.<br>We help to explore ways to leverage new advances in digital-tech to re-invent how things get done and boost your organization positioning in its sector.<br>We analyze the existing structure, navigate challenges and evaluate ways that technology can affect factors in your organization and identify new business models enabled by AI and explore opportunities presented by AI.<br>Learn how to shape your AI business strategy, organizational dynamics, products, services innovation and evolving workforce skills and discover practical solutions to build business advantage.'
            }
        };
    },
    async created() {
        await this.fetchData();
    },
    mounted() {
        document.title = 'JADCO - AI and Advanced Technologies';
    },
    methods: {
        async fetchData() {
            try {
                // Try to get data from the API
                const response = await axios.get('/api/ai/sections');
                
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
                console.error('Error fetching AI technologies service data:', error);
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