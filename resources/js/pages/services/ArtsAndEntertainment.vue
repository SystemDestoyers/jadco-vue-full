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
                title: '',
                description: '',
                image: '',
                subtitle: '',
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
                const response = await axios.get('/api/arts-and-entertainment/sections');
                console.log('API Response:', response.data);
                
                if (response.data && response.data.success && response.data.data && Array.isArray(response.data.data)) {
                    const sections = response.data.data;
                    
                    // Process each section
                    sections.forEach(section => {
                        if (section.name === 'arts' || section.name === 'entertainment') {
                            let sectionContent = section.content;
                            
                            // Parse content if it's a string
                            if (typeof sectionContent === 'string') {
                                try {
                                    sectionContent = JSON.parse(sectionContent);
                                } catch (error) {
                                    console.error('Error parsing section content:', error);
                                }
                            }
                            
                            // Update content with section data
                            if (sectionContent) {
                                this.content = sectionContent;
                            }
                        }
                    });
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