<template>
  <div class="about-page">
    <Header :heading="aboutHeading" :image="aboutImage" />
    
    <section class="about-section">
      <div class="container">
        <div class="section-header">
          <h2>{{ content.title }}</h2>
          <img :src="content.logo" alt="Logo" class="about-logo" v-if="content.logo">
        </div>
        
        <div class="about-content">
          <div class="about-text">
            <p class="main-text">{{ content.main_text }}</p>
            <p>{{ content.description1 }}</p>
            <p>{{ content.description2 }}</p>
          </div>
          
          <div class="about-images">
            <img :src="content.image1" alt="About Image 1" v-if="content.image1">
            <img :src="content.image2" alt="About Image 2" v-if="content.image2">
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import Header from '../components/Header.vue';

export default {
  name: 'AboutPage',
  components: {
    Header
  },
  data() {
    return {
      loading: true,
      error: null,
      content: {},
      aboutHeading: '',
      aboutImage: ''
    };
  },
  created() {
    this.fetchAboutData();
    this.fetchHeaderData();
  },
  methods: {
    fetchAboutData() {
      axios.get('/api/about/sections')
        .then(response => {
          this.content = response.data.content || {};
          this.loading = false;
        })
        .catch(error => {
          this.error = 'Error loading about content. Please try again.';
          this.loading = false;
          console.error('Error fetching about data:', error);
        });
    },
    fetchHeaderData() {
      axios.get('/api/header/sections')
        .then(response => {
          this.aboutHeading = response.data.aboutHeading || '';
          this.aboutImage = response.data.about_image || '';
        })
        .catch(error => {
          console.error('Error fetching header data:', error);
        });
    }
  }
};
</script>

<style scoped>
.about-page {
  padding-top: 60px;
}

.about-section {
  padding: 60px 0;
}

.section-header {
  text-align: center;
  margin-bottom: 40px;
}

.about-logo {
  max-height: 60px;
  margin-top: 20px;
}

.about-content {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}

.about-text {
  flex: 1;
  min-width: 300px;
}

.main-text {
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 20px;
}

.about-images {
  flex: 1;
  min-width: 300px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.about-images img {
  width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
  .about-content {
    flex-direction: column;
  }
}
</style> 