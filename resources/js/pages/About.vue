<template>
  <!-- About Section -->
  <section id="about" class="about-section py-5 section">
      <div class="container" v-if="!loading">
          <div class="about-heading">
              <h2 class="section-title">{{ content.title }}</h2>
              <img :src="content.logo" alt="JADCO Logo" class="about-logo">
          </div>
          <div class="about-text-container">
              <p class="about-text">
                  {{ content.main_text }}
              </p>
          </div>

          <div class="row">
              <div class="col-lg-6 order-lg-1 order-2 about-main-description">
                  <p class="about-description">
                      {{ content.description1 }}
                  </p>
                  <p class="about-description mt-4">
                      {{ content.description2 }}
                  </p>
              </div>
              <div class="col-lg-6 order-lg-2 order-1 about-image-wrapper">
                  <div class="about-image-container">
                      <div class="about-image-main">
                          <img :src="content.image1" alt="Graduate student" class="img-fluid">
                      </div>
                      <div class="about-image-secondary">
                          <img :src="content.image2" alt="Library and books" class="img-fluid">
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div v-else class="container loading-container">
          <div class="loading-spinner"></div>
          <p>Loading content...</p>
      </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      loading: true,
      content: {
        title: 'ABOUT',
        logo: '/images/jadoo-logo 2.png',
        main_text: 'After more than 20 years of experience in the Saudi Arabia\'s Human Capital Development market, JAD Consulting (JADCO) was established to continue supporting the industry with a new inspired vision by the great Saudi Vision 2030.',
        description1: 'JADCO and its highly ranked international partners of Companies, Universities and SMEs are forming together an exclusive and innovative consortium to serve and be part of the revolution and development and support the transformation for the next levels.',
        description2: 'JADCO in collaboration with the best partners in the globe, customize and Tailor projects to bridge the gap and providing the latest technologies to ensure the max level of quality of deliverables, support local content and transform knowledge to meet the objectives of our clients.',
        image1: '/images/About_01.jpg',
        image2: '/images/About_02.jpg'
      }
    }
  },
  methods: {
    async fetchAboutData() {
      try {
        // Get about content from API
        const response = await axios.get('/api/about/sections');
        
        if (response.data && response.data.content) {
          // Parse content if needed
          let content = response.data.content;
          if (typeof content === 'string') {
            content = JSON.parse(content);
          }
          
          // Update content properties
          if (content.title) this.content.title = content.title;
          if (content.logo) this.content.logo = content.logo;
          if (content.main_text) this.content.main_text = content.main_text;
          if (content.description1) this.content.description1 = content.description1;
          if (content.description2) this.content.description2 = content.description2;
          if (content.image1) this.content.image1 = content.image1;
          if (content.image2) this.content.image2 = content.image2;
        }
      } catch (error) {
        console.error('Error fetching about data:', error);
        // Use default content on error
      } finally {
        this.loading = false;
      }
    }
  },
  async created() {
    await this.fetchAboutData();
  },
  mounted() {
    // Load services.js if needed
    const script = document.createElement('script');
    script.src = '/js/services.js';
    document.head.appendChild(script);
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