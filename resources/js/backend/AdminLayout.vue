<template>
  <div class="admin-layout backend-ui">
    <!-- Top Navigation -->
    <header class="admin-header">
      <div class="logo">
        <h1>
          <span class="logo-text">JAD</span><span class="logo-highlight">CO</span>
          <span class="logo-tagline">Admin</span>
        </h1>
      </div>
      <div class="user-menu">
        <div class="search-box">
          <i class="fas fa-search search-icon"></i>
        </div>
        <div class="notifications">
          <i class="fas fa-bell"></i>
        </div>
        <div class="user-profile">
          <img src="/images/user.jpg" alt="User" class="user-avatar" />
          <div class="user-info">
            <span class="user-name">David Bauer</span>
            <span class="user-role">Admin</span>
          </div>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
      </div>
    </header>

    <div class="admin-content">
      <!-- Sidebar Navigation -->
      <aside class="admin-sidebar">
        <div class="sidebar-header">
          <div class="sidebar-icon dashboard-icon">
            <i class="fas fa-th-large"></i>
          </div>
          <div class="sidebar-label">DASHBOARD</div>
        </div>
        
        <nav class="sidebar-nav">
          <div class="nav-group">
            <div class="nav-group-title">CONTENT</div>
            <ul>
              <li>
                <router-link to="/admin" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-tachometer-alt"></i>
                  </div>
                  <span>Dashboard</span>
                </router-link>
              </li>
              <li>
                <router-link to="/admin/pages" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-file-alt"></i>
                  </div>
                  <span>Pages</span>
                </router-link>
              </li>
              <li>
                <router-link to="/admin/media" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-photo-video"></i>
                  </div>
                  <span>Media Library</span>
                </router-link>
              </li>
            </ul>
          </div>
          
          <div class="nav-group">
            <div class="nav-group-title">COMMUNICATION</div>
            <ul>
              <li>
                <router-link to="/admin/messages" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <span>Messages</span>
                  <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
                </router-link>
              </li>
            </ul>
          </div>
          
          <div class="nav-group">
            <div class="nav-group-title">SYSTEM</div>
            <ul>
              <li>
                <a href="#" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-cog"></i>
                  </div>
                  <span>Settings</span>
                </a>
              </li>
              <li>
                <a href="/" target="_blank" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-external-link-alt"></i>
                  </div>
                  <span>View Site</span>
                </a>
              </li>
              <li>
                <a href="#" @click.prevent="logout" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                  </div>
                  <span>Logout</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="admin-main">
        <slot></slot>
      </main>
    </div>
    
    <!-- Vue3-Toastify will handle notifications globally -->
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import './assets/css/backend-global.css';

const router = useRouter();
const route = useRoute();
const unreadCount = ref(0);

// Expose toast to the global window for components that can't use Vue composition API
if (typeof window !== 'undefined') {
  window.$notifications = {
    success: (message, options = {}) => toast.success(message, options),
    error: (message, options = {}) => toast.error(message, options),
    warning: (message, options = {}) => toast.warning(message, options),
    info: (message, options = {}) => toast.info(message, options),
    remove: (id) => toast.dismiss(id)
  };
  
  // Expose a global function to update the unread count
  window.updateAdminUnreadCount = (count) => {
    unreadCount.value = count;
  };
}

const logout = async () => {
  try {
    await axios.post('/admin-auth/logout');
    router.push('/admin/login');
  } catch (error) {
    // Silent fail
  }
};

const fetchUnreadCount = async () => {
  try {
    const response = await axios.get('/api/admin/messages');
    if (response.data && response.data.unread_count) {
      unreadCount.value = response.data.unread_count;
    }
  } catch (error) {
    console.error('Error fetching unread count:', error);
  }
};

onMounted(() => {
  fetchUnreadCount();
  // Refresh unread count every minute
  setInterval(fetchUnreadCount, 60000);
});
</script>

<style scoped>
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-family: 'Outfit', sans-serif;
  background-color: #f8f9fa;
}

/* Header styles */
.admin-header {
  background-color: #ffffff;
  color: #333;
  padding: 0.75rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eaeaea;
  box-shadow: 0 2px 4px rgba(0,0,0,0.03);
  height: 64px;
}

.logo h1 {
  margin: 0;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
}

.logo-text {
  font-weight: 500;
  color: #333;
}

.logo-highlight {
  font-weight: 700;
  color: #3d8bfd;
}

.logo-tagline {
  font-size: 0.75rem;
  color: #6c757d;
  margin-left: 0.5rem;
  font-weight: 400;
  align-self: flex-end;
  padding-bottom: 0.1rem;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.search-box {
  display: flex;
  align-items: center;
  position: relative;
}

.search-icon {
  color: #6c757d;
  font-size: 1rem;
}

.notifications {
  color: #6c757d;
  font-size: 1rem;
  position: relative;
}

.notifications::after {
  content: '';
  position: absolute;
  top: -2px;
  right: -2px;
  width: 8px;
  height: 8px;
  background-color: #dc3545;
  border-radius: 50%;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #212529;
}

.user-role {
  font-size: 0.75rem;
  color: #6c757d;
}

.dropdown-icon {
  color: #6c757d;
  font-size: 0.75rem;
}

/* Content layout */
.admin-content {
  display: flex;
  flex: 1;
}

/* Sidebar styles */
.admin-sidebar {
  width: 240px;
  background-color: #ffffff;
  color: #6c757d;
  box-shadow: 1px 0 3px rgba(0,0,0,0.05);
  z-index: 10;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #f1f1f1;
}

.sidebar-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background-color: rgba(61, 139, 253, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: #3d8bfd;
}

.sidebar-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #333;
  letter-spacing: 0.5px;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
}

.nav-group {
  margin-bottom: 1.5rem;
}

.nav-group-title {
  padding: 0 1.5rem;
  margin-bottom: 0.5rem;
  font-size: 0.7rem;
  font-weight: 600;
  color: #6c757d;
  letter-spacing: 0.5px;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  color: #495057;
  text-decoration: none;
  transition: all 0.2s;
  position: relative;
  font-size: 0.875rem;
}

.nav-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: #6c757d;
  transition: color 0.2s;
}

.nav-link:hover {
  background-color: #f8f9fa;
  color: #3d8bfd;
}

.nav-link:hover .nav-icon {
  color: #3d8bfd;
}

.nav-link.router-link-active {
  background-color: #f0f7ff;
  color: #3d8bfd;
  font-weight: 500;
}

.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background-color: #3d8bfd;
}

.nav-link.router-link-active .nav-icon {
  color: #3d8bfd;
}

.badge {
  background-color: #dc3545;
  color: white;
  font-size: 0.7rem;
  border-radius: 1rem;
  padding: 0.15rem 0.5rem;
  margin-left: auto;
  font-weight: 500;
}

/* Main content area */
.admin-main {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
  background-color: #f8f9fa;
}
</style> 