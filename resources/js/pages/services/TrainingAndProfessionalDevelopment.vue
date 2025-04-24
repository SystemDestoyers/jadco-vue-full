<template>
    <div class="sub-page">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading content...</p>
        </div>

        <div v-else>
            <!-- Service Detail Hero Section -->
            <section class="service-hero-section py-10">
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
            <section class="service-hero-section py-10">
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
            <section class="service-hero-section py-10">
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
                    title: 'Fellowship, Internship and Work Experience',
                    description: 'For a new talented and future skilled generation, we draw the map and open the door for understanding the contemporary workplace and environment that promote ready to innovate employees.',
                    image: '/images/03_Trining/01.jpg',
                    mainContent: 'JADCO, in association with prestigious organizations from all sizes, take your personal and professional development in an international company in a global city to learn new skills, reskill or upskill yourself and accelerate your career advancement.<br><br>Gain experience, new skills, network, knowledge, culture, friends and prepare yourself for a new workplace challenge:',
                    services: [
                        'In-person internship.',
                        'Any career field.',
                        'Central Cities around the world.',
                        'Boost your employability.'
                    ]
                },
                technical: {
                    title: 'Technical and Vocational Training and Education (TVTE)',
                    description: 'Studying overseas at leading colleges that provide students with academic, technical skills, Knowledge, career training necessary to succeed in future jobs, make them ready to go for labor market.',
                    image: '/images/03_Trining/03_Training.jpg',
                    mainContent: 'In collaboration with our best TVTE providers in the U.S, UK and other countries. These programs are hands-on diploma or associate degrees of 2 years study and training of majors from all fields and sectors, that make students equipped by all skills needed for the career job market.',
                    listTitle: 'Example majors including:',
                    services: [
                        'Advanced Transportation Technologies',
                        'Automotive Technology',
                        'Architecture',
                        'Baking & Pastry Arts',
                        'Construction Technology',
                        'Culinary Arts',
                        'Cybersecurity',
                        'Fashion',
                        'Game Design',
                        'Horticulture',
                        'Nursing & Medical Assisting',
                        'And more.'
                    ]
                },
                online: {
                    title: 'Online Professional Programs',
                    description: 'Flexible learning for students and busy professionals with high quality teaching, blended learning activities with experts\' instructors, SMEs, professional and leaders from the industry.',
                    image: '/images/03_Trining/04_Training.jpg',
                    mainContent: 'JADCO in partnership with top ranked universities in the world will serve to provide your organization the best online learning experience and quality of knowledge in all sectors, delivering programs that develop the skills necessary to success in a changing and challenging work environment.'
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
                const response = await axios.get('/api/training/sections');
                console.log('API Response:', response);
                
                // Check if we have a response with data
                if (response && response.data && response.data.success) {
                    console.log('Response data:', response.data);
                    
                    // Get sections data
                    const sections = response.data.data;
                    
                    if (sections && sections.length > 0) {
                        // Process each section
                        for (const section of sections) {
                            if (section.name === 'training-and-professional-development') {
                                let sectionContent = section.content;
                                
                                // Parse content if it's a string
                                if (typeof sectionContent === 'string') {
                                    try {
                                        sectionContent = JSON.parse(sectionContent);
                                        console.log('Parsed section content:', sectionContent);
                                    } catch (parseError) {
                                        console.error('Error parsing section content:', parseError);
                                    }
                                }
                                
                                // Replace the content with data from API
                                if (sectionContent) {
                                    this.content = sectionContent;
                                    break;
                                }
                            }
                        }
                    }
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