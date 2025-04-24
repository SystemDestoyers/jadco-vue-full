<template>
  <div class="login-page">
    <div class="login-container">
      <div class="logo-container">
        <img src="/images/logo.png" alt="Logo" class="login-logo">
      </div>
      <h1>Admin Login</h1>
      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            v-model="email" 
            type="email" 
            id="email" 
            placeholder="Enter your email" 
            required
          />
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            v-model="password" 
            type="password" 
            id="password" 
            placeholder="Enter your password" 
            required
          />
        </div>
        
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
        
        <button type="submit" :disabled="isLoading" class="login-button">
          {{ isLoading ? 'Logging in...' : 'Login' }}
        </button>
        
        <div class="credentials-hint">
          <p>Default credentials:</p>
          <p>Email: admin@admin.com</p>
          <p>Password: admin123</p>
        </div>
      </form>
      
      <div class="login-footer">
        <p>&copy; {{ new Date().getFullYear() }} JADCO. All rights reserved.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const login = async () => {
  errorMessage.value = ''
  isLoading.value = true
  
  try {
    // Get CSRF token from cookie
    const csrfCookieResponse = await axios.get('/sanctum/csrf-cookie');
    
    // Attempt login
    const loginResponse = await axios.post('/admin-auth/login', { 
      email: email.value, 
      password: password.value 
    });
    
    // Redirect to admin dashboard or to the requested page
    const redirectPath = route.query.redirect || '/admin'
    router.push(redirectPath)
  } catch (error) {
    // Show detailed error message when available
    if (error.response) {
      // Show validation errors if available
      if (error.response.data && error.response.data.errors) {
        const errors = error.response.data.errors;
        const firstError = Object.values(errors)[0];
        errorMessage.value = firstError[0] || 'Login failed. Please check your credentials.';
      } else if (error.response.data && error.response.data.message) {
        errorMessage.value = error.response.data.message;
      } else {
        errorMessage.value = `Login failed (${error.response.status}). Please check your credentials.`;
      }
    } else {
      errorMessage.value = 'Login failed. Please check your connection.';
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
  background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.logo-container {
  text-align: center;
  margin-bottom: 1.5rem;
}

.login-logo {
  max-width: 150px;
  height: auto;
}

.login-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  width: 100%;
  max-width: 400px;
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #2c3e50;
}

.form-group input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.error-message {
  color: #e74c3c;
  padding: 0.5rem;
  background-color: rgba(231, 76, 60, 0.1);
  border-radius: 4px;
  font-size: 0.875rem;
}

.login-button {
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.75rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

.login-button:hover {
  background-color: #2980b9;
}

.login-button:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.login-footer {
  text-align: center;
  margin-top: 2rem;
  font-size: 0.875rem;
  color: #7f8c8d;
}

.credentials-hint {
  margin-top: 1.5rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 4px;
  font-size: 0.875rem;
  color: #6c757d;
}

.credentials-hint p {
  margin: 0.25rem 0;
}
</style> 