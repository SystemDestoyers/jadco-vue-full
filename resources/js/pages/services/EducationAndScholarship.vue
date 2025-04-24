<template>
    <div class="sub-page">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading content...</p>
        </div>

        <div v-else>
            <!-- Service Detail Hero Section -->
            <section v-if="content.scholarship" class="service-hero-section py-10">
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
                                            <h4 class="service-title">{{ content.scholarship.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.scholarship.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img :src="content.scholarship.image" alt="Education"
                                class="img-fluid service-hero-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <ul class="service-list">
                                    <li v-for="(item, index) in content.scholarship.services" :key="index">
                                        {{ item }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- STEM Education Section -->
            <section v-if="content.stem" class="service-hero-section py-10">
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
                                            <h4 class="service-title">{{ content.stem.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.stem.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <h5>{{ content.stem.listTitle }}</h5>
                                <ul class="service-list">
                                    <li v-for="(item, index) in content.stem.services" :key="index" v-html="item"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img :src="content.stem.image" alt="STEM Education"
                                class="img-fluid service-hero-image">
                        </div>
                    </div>
                </div>
            </section>

            <!-- K-12 International Schools Section -->
            <section v-if="content.k12" class="service-hero-section py-10">
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
                                            <h4 class="service-title">{{ content.k12.title }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="service-desc">
                                            {{ content.k12.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <img :src="content.k12.image" alt="K-12 International Schools"
                                class="img-fluid service-hero-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-text-content">
                                <ul class="service-list">
                                    <li v-for="(item, index) in content.k12.services" :key="index">{{ item }}</li>
                                </ul>
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
                scholarship: null,
                stem: null,
                k12: null
            },
            defaultContent: {
                scholarship: {
                    title: 'Scholarship Programs Management',
                    description: 'With more than 20 years in managing scholarship programs with several Saudi governmental sponsors, we are experts of providing full and comprehensive plans and services to meet the sponsor\'s vision and targets. Services include but not limited to:',
                    image: '/images/02_Education/01.jpg',
                    services: [
                        'Candidate Selection Criteria',
                        'Universities Selection Criteria',
                        'Program bylaws and regulations',
                        'Student Journey plan',
                        'Counseling & students development plans',
                        'Pre-Departure preparatory programs',
                        'Enroll students into international universities',
                        'Workshops, awareness sessions and summits',
                        'Pre travel and post travel logistics',
                        'Overseas continual support',
                        'And more'
                    ]
                },
                stem: {
                    title: 'STEM Education and Innovation Centers',
                    description: 'Education is changing, STEM (Since, Technology, Engineering and Math), is the pathway to reshaping the ecosystem to rethinking and broadly reimagining. STEM is the future workforce skills, steps toward a new generation of innovators, creative leaders for now and the future. This approach of learning help to unlock the inherent potential of all students and equipping them with skills for the challenging work environment.',
                    image: '/images/03_EDU.jpg',
                    listTitle: 'Our services including:',
                    services: [
                        'STEM Teacher Training.',
                        'STEM Students Training.',
                        'STEM Labs.',
                        'Summer STEM Camps.',
                        'Students Workshops.',
                        'Research and development.',
                        'Innovation Parks.',
                        'K-12 after school programs (Coding, Robotics, Drones, AI, Cybersecurity, AR & VR, 3D Printing & more).',
                        'Establishing new STEM Schools or Shift to STEM.',
                        'STEM Innovation and Technology Centers: <p>Together with our STEM partners, we design, build and provide full turnkey solutions, training, programs and activities that support the STEM & Technology parks and Centers for students, teachers and community.</p>'
                    ]
                },
                k12: {
                    title: 'K-12 International Schools',
                    description: 'JADCO in partnership with the 1st international School Services Worldwide with more than 60 years of experience with track records, is supporting the global education community, promotes innovative best practice for global education through a long list of services including but not limited to:',
                    image: '/images/04_EDU.jpg',
                    services: [
                        'School Operation and Management.',
                        'Teacher Recruitment.',
                        'Leadership Search.',
                        'Professional Development.',
                        'School Supply Services.',
                        'Accounting and Finance.'
                    ]
                }
            }
        };
    },
    async created() {
        await this.fetchData();
    },
    mounted() {
        document.title = 'JADCO - Education and Scholarship';
    },
    methods: {
        async fetchData() {
            try {
                // Try to get data from the API
                const response = await axios.get('/api/education-and-scholarship/sections');
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
                        if (section.name === 'scholarship') {
                            this.content.scholarship = sectionContent;
                        } else if (section.name === 'stem') {
                            this.content.stem = sectionContent;
                        } else if (section.name === 'k12') {
                            this.content.k12 = sectionContent;
                        }
                    });
                    
                    // Fill in any missing sections with defaults
                    if (!this.content.scholarship) {
                        this.content.scholarship = this.defaultContent.scholarship;
                    }
                    if (!this.content.stem) {
                        this.content.stem = this.defaultContent.stem;
                    }
                    if (!this.content.k12) {
                        this.content.k12 = this.defaultContent.k12;
                    }
                } else {
                    // Use default content if API fails to return proper data
                    this.content = JSON.parse(JSON.stringify(this.defaultContent));
                }
            } catch (error) {
                console.error('Error fetching education service data:', error);
                // Fallback to default content defined in data()
                this.content = JSON.parse(JSON.stringify(this.defaultContent));
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