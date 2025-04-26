<template>
  <AdminLayout>
    <div class="dashboard">
      <div class="dashboard-header">
        <h1 class="page-title">Dashboard</h1>
        <div class="dashboard-actions">
          <button class="btn btn-primary" @click="refreshDashboard">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
          </button>
        </div>
      </div>
      
      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-file-alt"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ pagesCount }}</div>
            <div class="stat-label">Pages</div>
          </div>
          <div class="stat-footer">
            <router-link to="/admin/pages">Manage Pages</router-link>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-puzzle-piece"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ sectionsCount }}</div>
            <div class="stat-label">Sections</div>
          </div>
          <div class="stat-footer">
            <router-link to="/admin/pages">View All</router-link>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ messagesCount }}</div>
            <div class="stat-label">Messages</div>
            <div v-if="unreadMessages > 0" class="stat-badge">
              {{ unreadMessages }} unread
            </div>
          </div>
          <div class="stat-footer">
            <router-link to="/admin/messages">View Inbox</router-link>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-photo-video"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ mediaCount }}</div>
            <div class="stat-label">Media Files</div>
          </div>
          <div class="stat-footer">
            <router-link to="/admin/media">Media Library</router-link>
          </div>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="quick-actions">
        <h2 class="section-title">Quick Actions</h2>
        <div class="actions-grid">
          <router-link to="/admin/pages" class="action-card">
            <div class="action-icon">
              <i class="fas fa-plus"></i>
            </div>
            <div class="action-text">Add New Page</div>
          </router-link>
          
          <router-link to="/admin/media" class="action-card">
            <div class="action-icon">
              <i class="fas fa-upload"></i>
            </div>
            <div class="action-text">Upload Media</div>
          </router-link>
          
          <router-link to="/admin/messages" class="action-card">
            <div class="action-icon">
              <i class="fas fa-envelope-open-text"></i>
            </div>
            <div class="action-text">Check Messages</div>
          </router-link>
          
          <a href="/" target="_blank" class="action-card">
            <div class="action-icon">
              <i class="fas fa-external-link-alt"></i>
            </div>
            <div class="action-text">View Website</div>
          </a>
        </div>
      </div>
      
      <!-- Content Cards -->
      <div class="dashboard-content">
        <!-- Recent Pages -->
        <div class="content-card recent-pages">
          <div class="card-header">
            <h2>Recent Pages</h2>
            <router-link to="/admin/pages" class="btn btn-sm btn-outline-primary">
              All Pages
            </router-link>
          </div>
          
          <div v-if="isLoading" class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          
          <div v-else-if="error" class="error-message">
            <i class="fas fa-exclamation-triangle text-danger"></i> {{ error }}
          </div>
          
          <div v-else class="card-content">
            <div v-if="pages.length === 0" class="empty-state">
              <i class="fas fa-file-alt fa-3x text-muted"></i>
              <p>No pages found</p>
              <router-link to="/admin/pages/create" class="btn btn-sm btn-primary">
                Create First Page
              </router-link>
            </div>
            
            <ul v-else class="page-list">
              <li v-for="page in pages" :key="page.id" class="page-item">
                <div class="page-info">
                  <div class="page-title">{{ page.title }}</div>
                  <div class="page-meta">
                    <span class="page-slug">{{ page.slug }}</span>
                    <span class="page-sections">{{ page.sections_count }} sections</span>
                  </div>
                </div>
                <div class="page-actions">
                  <router-link :to="`/admin/pages/${page.id}/sections`" class="action-btn sections">
                    <i class="fas fa-puzzle-piece"></i>
                  </router-link>
                </div>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Recent Messages -->
        <div class="content-card recent-messages">
          <div class="card-header">
            <h2>Recent Messages</h2>
            <router-link to="/admin/messages" class="btn btn-sm btn-outline-primary">
              All Messages
            </router-link>
          </div>
          
          <div v-if="isLoadingMessages" class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          
          <div v-else-if="messageError" class="error-message">
            <i class="fas fa-exclamation-triangle text-danger"></i> {{ messageError }}
          </div>
          
          <div v-else class="card-content">
            <div v-if="recentMessages.length === 0" class="empty-state">
              <i class="fas fa-envelope fa-3x text-muted"></i>
              <p>No messages found</p>
            </div>
            
            <ul v-else class="message-list">
              <li v-for="message in recentMessages" :key="message.id" class="message-item" :class="{'unread': !message.read}">
                <div class="message-info">
                  <div class="message-sender">
                    {{ message.first_name }} {{ message.last_name }}
                    <span v-if="!message.read" class="unread-indicator"></span>
                  </div>
                  <div class="message-preview">{{ truncateText(message.message, 60) }}</div>
                  <div class="message-date">{{ formatDate(message.created_at) }}</div>
                </div>
                <div class="message-actions">
                  <router-link :to="`/admin/messages?view=${message.id}`" class="action-btn view">
                    <i class="fas fa-envelope-open"></i>
                  </router-link>
                </div>
              </li>
            </ul>
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
import { toast } from 'vue3-toastify';

// Data
const pages = ref([]);
const isLoading = ref(true);
const error = ref(null);
const recentMessages = ref([]);
const isLoadingMessages = ref(true);
const messageError = ref(null);
const unreadMessages = ref(0);
const mediaCount = ref(0);
const messagesCount = ref(0);

// Computed properties
const pagesCount = computed(() => pages.value.length);
const sectionsCount = computed(() => {
  return pages.value.reduce((sum, page) => sum + (page.sections_count || 0), 0);
});

// Methods
const fetchPages = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get('/api/admin/pages');
    pages.value = response.data.slice(0, 5); // Get the 5 most recent pages
  } catch (err) {
    console.error('Failed to load pages:', err);
    error.value = 'Failed to load pages. Please try again.';
    pages.value = [];
  } finally {
    isLoading.value = false;
  }
};

const fetchMessages = async () => {
  isLoadingMessages.value = true;
  messageError.value = null;
  
  try {
    const response = await axios.get('/api/admin/messages', {
      params: { per_page: 5 } // Get only 5 most recent messages
    });
    
    if (response.data.success) {
      if (response.data.data && response.data.data.data) {
        recentMessages.value = response.data.data.data;
        messagesCount.value = response.data.data.total;
      } else {
        recentMessages.value = response.data.data || [];
        messagesCount.value = recentMessages.value.length;
      }
      
      unreadMessages.value = response.data.unread_count || 0;
    } else {
      messageError.value = 'Failed to load messages';
      recentMessages.value = [];
    }
  } catch (err) {
    console.error('Failed to load messages:', err);
    messageError.value = 'Failed to load messages. Please try again.';
    recentMessages.value = [];
  } finally {
    isLoadingMessages.value = false;
  }
};

const fetchMediaCount = async () => {
  try {
    const response = await axios.get('/api/admin/media/count');
    mediaCount.value = response.data.count || 0;
  } catch (err) {
    console.error('Failed to load media count:', err);
    mediaCount.value = 0;
  }
};

const refreshDashboard = async () => {
  toast.info('Refreshing dashboard data...');
  await Promise.all([
    fetchPages(),
    fetchMessages(),
    fetchMediaCount()
  ]);
  toast.success('Dashboard refreshed!');
};

const truncateText = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = Math.floor((now - date) / 1000 / 60); // Difference in minutes
  
  if (diff < 1) return 'Just now';
  if (diff < 60) return `${diff} min ago`;
  
  const hours = Math.floor(diff / 60);
  if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  
  const days = Math.floor(hours / 24);
  if (days < 7) return `${days} day${days > 1 ? 's' : ''} ago`;
  
  return date.toLocaleDateString();
};

onMounted(() => {
  fetchPages();
  fetchMessages();
  fetchMediaCount();
});
</script>

<style scoped>
.dashboard {
  padding: 1.5rem;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.page-title {
  margin: 0;
  color: #2c3e50;
  font-size: 1.75rem;
  font-weight: 600;
}

.dashboard-actions .btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

/* Statistics Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: white;
}

.bg-primary {
  background-color: #3498db;
}

.bg-success {
  background-color: #2ecc71;
}

.bg-info {
  background-color: #00bcd4;
}

.bg-warning {
  background-color: #f39c12;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.25rem;
  line-height: 1;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.95rem;
}

.stat-badge {
  display: inline-block;
  background-color: #e74c3c;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  margin-top: 0.5rem;
}

.stat-footer {
  margin-top: 1rem;
  font-size: 0.85rem;
}

.stat-footer a {
  color: #3498db;
  display: flex;
  align-items: center;
  text-decoration: none;
}

.stat-footer a:hover {
  text-decoration: underline;
}

/* Quick Actions */
.quick-actions {
  margin-bottom: 2rem;
}

.section-title {
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: #2c3e50;
  font-weight: 600;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
  text-decoration: none;
  color: #2c3e50;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

.action-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: #f5f8fa;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: #3498db;
}

.action-text {
  font-weight: 500;
  text-align: center;
}

/* Content Cards */
.dashboard-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 992px) {
  .dashboard-content {
    grid-template-columns: 3fr 2fr;
  }
}

.content-card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #ecf0f1;
}

.card-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
  font-weight: 600;
}

.card-content {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
  max-height: 400px;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  padding: 2rem;
}

.error-message {
  padding: 1rem;
  color: #e74c3c;
  text-align: center;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: #7f8c8d;
  text-align: center;
}

.empty-state i {
  margin-bottom: 1rem;
}

.empty-state p {
  margin-bottom: 1rem;
}

/* Page List */
.page-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.page-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #ecf0f1;
}

.page-item:last-child {
  border-bottom: none;
}

.page-title {
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.page-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #7f8c8d;
}

.page-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  color: #2c3e50;
  background-color: #f5f8fa;
  text-decoration: none;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background-color: #e9ecef;
}

.action-btn.sections:hover {
  color: #2ecc71;
}

.action-btn.view:hover {
  color: #f39c12;
}

/* Message List */
.message-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.message-item {
  display: flex;
  justify-content: space-between;
  align-items: start;
  padding: 1rem 0;
  border-bottom: 1px solid #ecf0f1;
  position: relative;
}

.message-item:last-child {
  border-bottom: none;
}

.message-item.unread {
  background-color: #f5faff;
}

.message-sender {
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 0.25rem;
  display: flex;
  align-items: center;
}

.unread-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #3498db;
  margin-left: 0.5rem;
}

.message-preview {
  font-size: 0.9rem;
  color: #34495e;
  margin-bottom: 0.25rem;
}

.message-date {
  font-size: 0.8rem;
  color: #7f8c8d;
}

.message-actions {
  display: flex;
  gap: 0.5rem;
}
</style> 