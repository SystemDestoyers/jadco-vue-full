<template>
  <AdminLayout>
    <div class="dashboard">
      <h1 class="page-title">Dashboard</h1>
      
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-file"></i>
          </div>
          <div class="stat-content">
            <h3>{{ pagesCount }}</h3>
            <p>Total Pages</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-puzzle-piece"></i>
          </div>
          <div class="stat-content">
            <h3>{{ sectionsCount }}</h3>
            <p>Total Sections</p>
          </div>
        </div>
      </div>
      
      <div class="dashboard-content">
        <div class="card recent-pages">
          <div class="card-header">
            <h2>Pages Overview</h2>
            <router-link to="/admin/pages" class="btn">Manage Pages</router-link>
          </div>
          
          <div v-if="isLoading" class="loading">
            Loading pages...
          </div>
          
          <div v-else-if="error" class="error-message">
            {{ error }}
          </div>
          
          <div v-else class="page-list">
            <table>
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Slug</th>
                  <th>Sections</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="page in pages" :key="page.id">
                  <td>{{ page.title }}</td>
                  <td>{{ page.slug }}</td>
                  <td>{{ page.sections_count }}</td>
                  <td class="actions">
                    <router-link :to="`/admin/pages/${page.id}/sections`" class="action-btn">
                      <i class="fas fa-puzzle-piece"></i> Sections
                    </router-link>
                    <router-link :to="`/admin/pages/${page.id}/edit`" class="action-btn">
                      <i class="fas fa-edit"></i> Edit
                    </router-link>
                  </td>
                </tr>
                <tr v-if="pages.length === 0">
                  <td colspan="4" class="empty-message">No pages found</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AdminLayout from './AdminLayout.vue';

const pages = ref([]);
const isLoading = ref(true);
const error = ref(null);

const fetchPages = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    // Fetch recent pages
    pages.value = [
      // This is mock data until we implement the API endpoint
      { id: 1, title: 'Home Page', sections_count: 6 },
      { id: 2, title: 'About Us', sections_count: 3 },
      { id: 3, title: 'Services', sections_count: 5 },
    ];
  } catch (err) {
    error.value = 'Failed to load pages. Please try again.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchPages();
});

const pagesCount = computed(() => pages.value.length);
const sectionsCount = computed(() => {
  return pages.value.reduce((sum, page) => sum + (page.sections_count || 0), 0);
});
</script>

<style scoped>
.dashboard {
  padding: 1rem;
}

.page-title {
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
  display: flex;
  align-items: center;
}

.stat-icon {
  background-color: #3498db;
  color: white;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  font-size: 1.25rem;
}

.stat-content h3 {
  font-size: 1.5rem;
  margin: 0;
  color: #2c3e50;
}

.stat-content p {
  margin: 0;
  color: #7f8c8d;
}

.dashboard-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

.card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #ecf0f1;
}

.card-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.btn {
  display: inline-block;
  background-color: #3498db;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.875rem;
}

.page-list {
  padding: 0 1.5rem 1.5rem;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

th, td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #ecf0f1;
}

th {
  font-weight: 600;
  color: #34495e;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.75rem;
  color: #2c3e50;
  background-color: #ecf0f1;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background-color: #bdc3c7;
}

.empty-message {
  text-align: center;
  color: #7f8c8d;
  padding: 2rem 0;
}

.loading, .error-message {
  padding: 2rem;
  text-align: center;
  color: #7f8c8d;
}

.error-message {
  color: #e74c3c;
}
</style> 