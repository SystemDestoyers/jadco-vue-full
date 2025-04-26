<template>
  <div class="admin-layout backend-ui" :dir="currentDirection" :class="{ 'dark-mode': isDarkMode }" :data-color-mode="colorMode">
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
        <div 
          class="user-profile" 
          @click.stop="toggleDropdown"
          ref="userProfileRef"
        >
          <img 
            class="user-avatar" 
            :src="userInfo.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userInfo.name || 'User')" 
            alt="User avatar"
          />
          <div class="user-info">
            <div class="user-name">{{ userInfo.name || 'Loading...' }}</div>
            <div class="user-role">{{ userInfo.role || 'Admin' }}</div>
          </div>
          <i class="las la-angle-down dropdown-icon" :class="{ 'dropdown-open': isDropdownOpen }"></i>
          
          <!-- User Dropdown Menu -->
          <div v-if="isDropdownOpen" class="user-dropdown">
            <div class="dropdown-header">
              <strong>{{ userInfo.name || 'User' }}</strong>
              <div>{{ userInfo.email || 'loading@example.com' }}</div>
            </div>
            
            <router-link class="dropdown-item" to="/admin/profile">
              <i class="las la-user-circle"></i>
              <span>Profile Settings</span>
            </router-link>
            
            <router-link class="dropdown-item" to="/admin/notifications">
              <i class="las la-bell"></i>
              <span>Notifications</span>
            </router-link>
            
            <div class="dropdown-divider"></div>
            
            <a href="#" class="dropdown-item" @click.prevent="logout">
              <i class="las la-sign-out-alt"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <div class="admin-content">
      <!-- Sidebar Navigation -->
      <aside class="admin-sidebar" :class="{ 'rtl-sidebar': rtlMode }">
        <div class="sidebar-header">
          <div class="sidebar-icon dashboard-icon">
            <i class="fas fa-th-large"></i>
          </div>
          <div class="sidebar-label">{{ t('dashboard').toUpperCase() }}</div>
        </div>
        
        <nav class="sidebar-nav">
          <div class="nav-group">
            <div class="nav-group-title">{{ t('content') }}</div>
            <ul>
              <li>
                <router-link to="/admin" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-tachometer-alt"></i>
                  </div>
                  <span>{{ t('dashboard') }}</span>
                </router-link>
              </li>
              <li>
                <router-link to="/admin/pages" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-file-alt"></i>
                  </div>
                  <span>{{ t('pages') }}</span>
                </router-link>
              </li>
              <li>
                <router-link to="/admin/media" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-photo-video"></i>
                  </div>
                  <span>{{ t('media') }}</span>
                </router-link>
              </li>
            </ul>
          </div>
          
          <div class="nav-group">
            <div class="nav-group-title">{{ t('communication') }}</div>
            <ul>
              <li>
                <router-link to="/admin/messages" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <span>{{ t('messages') }}</span>
                  <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
                </router-link>
              </li>
            </ul>
          </div>
          
          <div class="nav-group">
            <div class="nav-group-title">{{ t('system') }}</div>
            <ul>
              <li>
                <router-link to="/admin/settings" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-cog"></i>
                  </div>
                  <span>{{ t('settings') }}</span>
                </router-link>
              </li>
              <li>
                <a href="/" target="_blank" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-external-link-alt"></i>
                  </div>
                  <span>{{ t('viewSite') }}</span>
                </a>
              </li>
              <li>
                <a href="#" @click.prevent="logout" class="nav-link">
                  <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                  </div>
                  <span>{{ t('logout') }}</span>
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
import { ref, onMounted, watch, computed, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import './assets/css/backend-global.css';
import './assets/css/admin-theme.css';
import { getCurrentLanguage, isRTL, t } from '../i18n';

const router = useRouter();
const route = useRoute();
const unreadCount = ref(0);
const currentLang = ref(getCurrentLanguage());
const rtlMode = ref(isRTL());
const colorMode = ref('light');
const isDarkMode = computed(() => colorMode.value === 'dark');
const currentDirection = computed(() => rtlMode.value ? 'rtl' : 'ltr');
const userInfo = ref({});
const isDropdownOpen = ref(false);
const userProfileRef = ref(null);

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
    // Ensure token is in the Authorization header
    const token = localStorage.getItem('auth_token');
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    
    await axios.post('/api/admin/logout');
    
    // Remove token from localStorage and Authorization header
    localStorage.removeItem('auth_token');
    delete axios.defaults.headers.common['Authorization'];
    
    router.push('/admin/login');
  } catch (error) {
    // Silent fail
    console.error('Logout error:', error);
    
    // Still clear token and redirect on error
    localStorage.removeItem('auth_token');
    delete axios.defaults.headers.common['Authorization'];
    
    router.push('/admin/login');
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

// Watch for language changes
watch(() => getCurrentLanguage(), (newLang) => {
  currentLang.value = newLang;
  rtlMode.value = isRTL();
});

// Check for dark mode preference
const loadColorMode = () => {
  const savedMode = localStorage.getItem('admin_color_mode');
  if (savedMode) {
    colorMode.value = savedMode;
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    // Use dark mode if user's system prefers dark
    colorMode.value = 'dark';
  }

  // Apply theme to document and body
  document.documentElement.setAttribute('data-color-mode', colorMode.value);
  
  // Force body to apply dark mode class
  if (colorMode.value === 'dark') {
    document.body.classList.add('dark-mode');
  } else {
    document.body.classList.remove('dark-mode');
  }
};

// Listen for system color scheme changes
const listenForColorSchemeChanges = () => {
  if (window.matchMedia) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener('change', e => {
        if (colorMode.value === 'auto') {
          colorMode.value = e.matches ? 'dark' : 'light';
          document.documentElement.setAttribute('data-color-mode', colorMode.value);
          
          // Update body class for dark mode
          if (colorMode.value === 'dark') {
            document.body.classList.add('dark-mode');
          } else {
            document.body.classList.remove('dark-mode');
          }
        }
      });
    }
  }
};

// Watch for dark mode changes to apply to body
watch(colorMode, (newValue) => {
  if (newValue === 'dark') {
    document.body.classList.add('dark-mode');
  } else {
    document.body.classList.remove('dark-mode');
  }
});

// Listen for changes to color mode from settings page
window.addEventListener('colorModeChanged', (e) => {
  colorMode.value = e.detail.mode;
  document.documentElement.setAttribute('data-color-mode', colorMode.value);
  
  // Update body class for dark mode
  if (colorMode.value === 'dark') {
    document.body.classList.add('dark-mode');
  } else {
    document.body.classList.remove('dark-mode');
  }
});

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

onMounted(() => {
  fetchUnreadCount();
  // Refresh unread count every minute
  setInterval(fetchUnreadCount, 60000);
  
  // Listen for language changes
  window.addEventListener('languageChanged', (e) => {
    currentLang.value = e.detail.language;
    rtlMode.value = e.detail.language === 'ar';
  });

  // Initialize color mode
  loadColorMode();
  listenForColorSchemeChanges();
  
  // Get user information
  fetchUserInfo();
  
  // Close dropdown when clicking outside
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (userProfileRef.value && !userProfileRef.value.contains(event.target)) {
    isDropdownOpen.value = false;
  }
};

// Fetch user information
const fetchUserInfo = async () => {
  try {
    const response = await axios.get('/api/admin/user');
    if (response.data && response.data.user) {
      userInfo.value = response.data.user;
    }
  } catch (error) {
    console.error('Error fetching user info:', error);
  }
};
</script>

<style scoped>
/* Common variables */
:root {
  --primary-color: #3d8bfd;
  --primary-hover: #2d7af2;
  --light-bg: #f8f9fa;
  --light-card: #ffffff;
  --light-text: #212529;
  --light-text-secondary: #6c757d;
  --light-border: #f1f1f1;
  --light-input-bg: #ffffff;
  --light-hover: #f8f9fa;
  --light-active: #f0f7ff;
  --light-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  
  --dark-bg: #121212;
  --dark-card: #1e1e1e;
  --dark-text: #e9ecef;
  --dark-text-secondary: #adb5bd;
  --dark-border: #333333;
  --dark-input-bg: #333333;
  --dark-hover: #2a2a2a;
  --dark-active: rgba(61, 139, 253, 0.15);
  --dark-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

/* Apply dark mode variables to body and html when dark mode is active */
body.dark-mode {
  background-color: var(--dark-bg) !important;
  color: var(--dark-text) !important;
}

/* Base styles */
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-family: 'Outfit', sans-serif;
  background-color: var(--light-bg);
  color: var(--light-text);
}

/* Dark mode styles */
.dark-mode {
  background-color: var(--dark-bg);
  color: var(--dark-text);
}

/* Header styles */
.admin-header {
  background-color: var(--light-card);
  color: var(--light-text);
  padding: 0.75rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid var(--light-border);
  box-shadow: var(--light-shadow);
  height: 64px;
}

.dark-mode .admin-header {
  background-color: var(--dark-card);
  color: var(--dark-text);
  border-bottom-color: var(--dark-border);
  box-shadow: var(--dark-shadow);
}

.logo h1 {
  margin: 0;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
}

.logo-text {
  font-weight: 500;
  color: var(--light-text);
}

.dark-mode .logo-text {
  color: var(--dark-text);
}

.logo-highlight {
  font-weight: 700;
  color: var(--primary-color);
}

.logo-tagline {
  font-size: 0.75rem;
  color: var(--light-text-secondary);
  margin-left: 0.5rem;
  font-weight: 400;
  align-self: flex-end;
  padding-bottom: 0.1rem;
}

.dark-mode .logo-tagline {
  color: var(--dark-text-secondary);
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
  color: var(--light-text-secondary);
  font-size: 1rem;
}

.dark-mode .search-icon {
  color: var(--dark-text-secondary);
}

.notifications {
  color: var(--light-text-secondary);
  font-size: 1rem;
  position: relative;
}

.dark-mode .notifications {
  color: var(--dark-text-secondary);
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
  padding: 0.5rem 1rem;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
}

.user-profile:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.dark-mode .user-profile:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.user-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--text-primary);
}

.user-role {
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.dropdown-icon {
  color: var(--text-secondary);
  font-size: 0.8rem;
  transition: transform 0.3s ease;
}

.dropdown-icon.dropdown-open {
  transform: rotate(180deg);
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 240px;
  background-color: var(--bg-card);
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-color);
  z-index: 1000;
  overflow: hidden;
  animation: fadeInDown 0.2s ease-out;
}

.dropdown-header {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.9rem;
}

.dropdown-header strong {
  display: block;
  margin-bottom: 0.25rem;
  color: var(--text-primary);
}

.dropdown-header div {
  color: var(--text-secondary);
  font-size: 0.85rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-divider {
  height: 1px;
  background-color: var(--border-color);
  margin: 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: var(--text-primary);
  text-decoration: none;
  font-size: 0.9rem;
  transition: background-color 0.2s;
}

.dropdown-item:hover {
  background-color: rgba(0, 0, 0, 0.04);
}

.dark-mode .dropdown-item:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.dropdown-item i {
  color: var(--text-secondary);
  width: 1rem;
  text-align: center;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Content layout */
.admin-content {
  display: flex;
  flex: 1;
}

/* Sidebar styles */
.admin-sidebar {
  width: 240px;
  background-color: var(--light-card);
  color: var(--light-text-secondary);
  box-shadow: var(--light-shadow);
  z-index: 10;
  display: flex;
  flex-direction: column;
}

.dark-mode .admin-sidebar {
  background-color: var(--dark-card);
  color: var(--dark-text-secondary);
  box-shadow: var(--dark-shadow);
}

.sidebar-header {
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  border-bottom: 1px solid var(--light-border);
}

.dark-mode .sidebar-header {
  border-bottom-color: var(--dark-border);
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
  color: var(--primary-color);
}

.dark-mode .sidebar-icon {
  background-color: rgba(61, 139, 253, 0.2);
}

.sidebar-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--light-text);
  letter-spacing: 0.5px;
}

.dark-mode .sidebar-label {
  color: var(--dark-text);
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
  color: var(--light-text-secondary);
  letter-spacing: 0.5px;
}

.dark-mode .nav-group-title {
  color: var(--dark-text-secondary);
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
  color: var(--light-text);
  text-decoration: none;
  transition: all 0.2s;
  position: relative;
  font-size: 0.875rem;
}

.dark-mode .nav-link {
  color: var(--dark-text);
}

.nav-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: var(--light-text-secondary);
  transition: color 0.2s;
}

.dark-mode .nav-icon {
  color: var(--dark-text-secondary);
}

.nav-link:hover {
  background-color: var(--light-hover);
  color: var(--primary-color);
}

.dark-mode .nav-link:hover {
  background-color: var(--dark-hover);
}

.nav-link:hover .nav-icon {
  color: var(--primary-color);
}

.nav-link.router-link-active {
  background-color: var(--light-active);
  color: var(--primary-color);
  font-weight: 500;
}

.dark-mode .nav-link.router-link-active {
  background-color: var(--dark-active);
}

.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background-color: var(--primary-color);
}

.nav-link.router-link-active .nav-icon {
  color: var(--primary-color);
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
  background-color: var(--light-bg);
}

.dark-mode .admin-main {
  background-color: var(--dark-bg);
}

/* RTL sidebar styles */
.rtl-sidebar {
  border-right: none;
  border-left: 1px solid var(--light-border);
}

.dark-mode .rtl-sidebar {
  border-left-color: var(--dark-border);
}

[dir="rtl"] .admin-sidebar {
  border-right: none;
  border-left: 1px solid var(--light-border);
  direction: rtl;
}

.dark-mode[dir="rtl"] .admin-sidebar {
  border-left-color: var(--dark-border);
}

[dir="rtl"] .sidebar-icon {
  margin-right: 0;
  margin-left: 0.75rem;
}

[dir="rtl"] .nav-icon {
  margin-right: 0;
  margin-left: 0.75rem;
}

[dir="rtl"] .badge {
  margin-left: 0;
  margin-right: auto;
}

[dir="rtl"] .admin-content {
  flex-direction: row-reverse;
}

[dir="rtl"] .user-profile {
  flex-direction: row-reverse;
}

[dir="rtl"] .user-avatar {
  margin-right: 0;
  margin-left: 0.75rem;
}

[dir="rtl"] .user-info {
  text-align: right;
}

[dir="rtl"] .nav-link.router-link-active::before {
  left: auto;
  right: 0;
}

/* Ensure dropdowns are properly styled in dark mode */
.dark-mode .dropdown-menu {
  background-color: var(--dark-card);
  border-color: var(--dark-border);
}

.dark-mode .dropdown-item {
  color: var(--dark-text);
}

.dark-mode .dropdown-item:hover {
  background-color: var(--dark-hover);
}

/* Make sure forms look good in dark mode */
.dark-mode input, 
.dark-mode textarea, 
.dark-mode select {
  background-color: var(--dark-input-bg);
  color: var(--dark-text);
  border-color: var(--dark-border);
}

.dark-mode input::placeholder, 
.dark-mode textarea::placeholder {
  color: var(--dark-text-secondary);
}

/* Buttons in dark mode */
.dark-mode .btn-secondary {
  background-color: #495057;
}

.dark-mode .btn-secondary:hover {
  background-color: #343a40;
}

/* Tables in dark mode */
.dark-mode table {
  color: var(--dark-text);
  border-color: var(--dark-border);
}

.dark-mode th {
  background-color: var(--dark-card);
  color: var(--dark-text);
  border-color: var(--dark-border);
}

.dark-mode td {
  border-color: var(--dark-border);
}

.dark-mode tr:nth-child(even) {
  background-color: rgba(255, 255, 255, 0.05);
}

.dark-mode tr:hover {
  background-color: var(--dark-hover);
}

/* Cards and panels in dark mode */
.dark-mode .card,
.dark-mode .panel {
  background-color: var(--dark-card);
  border-color: var(--dark-border);
}

.dark-mode .card-header,
.dark-mode .panel-header {
  background-color: rgba(0, 0, 0, 0.2);
  border-bottom-color: var(--dark-border);
}

.dark-mode .card-footer,
.dark-mode .panel-footer {
  background-color: rgba(0, 0, 0, 0.2);
  border-top-color: var(--dark-border);
}
</style> 