<template>
  <div class="admin-layout">
    <!-- Top Navigation -->
    <header class="admin-header">
      <div class="logo">
        <h1>JADCO Admin</h1>
      </div>
      <div class="user-menu">
        <span class="user-name">
          <i class="fas fa-user-circle"></i> Admin User
        </span>
        <button @click="logout" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </div>
    </header>

    <div class="admin-content">
      <!-- Sidebar Navigation -->
      <aside class="admin-sidebar">
        <nav>
          <ul>
            <li>
              <router-link to="/admin" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
              </router-link>
            </li>
            <li>
              <router-link to="/admin/pages" class="nav-link">
                <i class="fas fa-file-alt"></i> Pages
              </router-link>
            </li>
            <li>
              <a href="#" class="nav-link">
                <i class="fas fa-image"></i> Media
              </a>
            </li>
            <li>
              <a href="#" class="nav-link">
                <i class="fas fa-cog"></i> Settings
              </a>
            </li>
            <li>
              <a href="/" target="_blank" class="nav-link">
                <i class="fas fa-external-link-alt"></i> View Site
              </a>
            </li>
          </ul>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="admin-main">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const logout = async () => {
  try {
    await axios.post('/admin-auth/logout');
    router.push('/admin/login');
  } catch (error) {
    // Replace console.error('Logout failed', error); with nothing
  }
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.admin-header {
  background-color: #2c3e50;
  color: white;
  padding: 0.5rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 {
  margin: 0;
  font-size: 1.5rem;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logout-btn {
  padding: 0.25rem 0.5rem;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.admin-content {
  display: flex;
  flex: 1;
}

.admin-sidebar {
  width: 250px;
  background-color: #34495e;
  color: white;
  padding: 1rem 0;
}

.admin-sidebar nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: block;
  padding: 0.75rem 1rem;
  color: white;
  text-decoration: none;
  transition: background-color 0.2s;
}

.nav-link:hover,
.nav-link.router-link-active {
  background-color: #2c3e50;
  color: #3498db;
}

.admin-main {
  flex: 1;
  padding: 1.5rem;
  background-color: #f5f5f5;
  overflow-y: auto;
}
</style> 