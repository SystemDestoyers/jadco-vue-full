<template>
  <div class="login-page">
    <div class="login-layout">
      <div class="login-sidebar">
        <div class="logo-container">
          <img src="/images/logo.png" alt="Logo" class="login-logo">
        </div>
        
        <div class="login-content">
          <h1>Login</h1>
          <p class="login-description">
            Step into the world of digital assets and decentralized systems.
          </p>
          
          <form @submit.prevent="login" class="login-form">
            <div class="form-group">
              <label for="email">Email</label>
              <input 
                v-model="email" 
                type="email" 
                id="email" 
                placeholder="mail@example.com" 
                required
              />
            </div>
            
            <div class="form-group">
              <label for="password">Password</label>
              <input 
                v-model="password" 
                type="password" 
                id="password" 
                placeholder="••••••••••" 
                required
              />
            </div>
            
            <div class="form-options">
              <label class="remember-me">
                <input type="checkbox" /> 
                <span>Remember me?</span>
              </label>
              <a href="#" class="forgot-password">Forgot password?</a>
            </div>
            
            <div v-if="errorMessage" class="error-message">
              {{ errorMessage }}
            </div>
            
            <button type="submit" :disabled="isLoading" class="login-button">
              {{ isLoading ? 'Logging in...' : 'Login' }}
            </button>
          </form>
          
          <div class="account-hints">
            <p>Not registered yet? <a href="#" class="create-account-link">Create an account</a></p>
            
            <div class="credentials-hint">
              <p>Default credentials:</p>
              <p>Email: admin@jadco.co</p>
              <p>Password: 111</p>
            </div>
          </div>
          
          <div class="login-footer">
            <p>&copy; {{ new Date().getFullYear() }} JADCO. All rights reserved.</p>
          </div>
        </div>
      </div>
      
      <!-- Right side with background image -->
      <div class="login-graphic">
        <!-- Background image is loaded via CSS -->
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
  try {
    errorMessage.value = ''
    isLoading.value = true
    
    // Attempt login with admin API
    const response = await axios.post('/api/admin/login', { 
      email: email.value, 
      password: password.value 
    });
    
    console.log('Login response:', response.data);
    
    // Store the token in localStorage
    if (response.data && response.data.token) {
      const token = response.data.token;
      localStorage.setItem('auth_token', token);
      
      // Set the default Authorization header for all future requests
      const bearerToken = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
      axios.defaults.headers.common['Authorization'] = bearerToken;
      
      console.log('Token stored. Debug info:', response.data.debug_info || {});
      
      // If successful, redirect to dashboard - use window.location for more direct approach
      const redirectPath = route.query.redirect || '/admin';
      console.log('Login successful. Redirecting to:', redirectPath);
      
      // Use hard redirect which is more reliable than router.push
      window.location.href = redirectPath;
    } else {
      errorMessage.value = 'Login successful but no token was received.';
    }
    
  } catch (error) {
    console.error('Login error:', error);
    
    // Show detailed error message when available
    if (error.response) {
      if (error.response.data && error.response.data.errors) {
        const errors = error.response.data.errors;
        if (errors.email) {
          errorMessage.value = errors.email[0];
        } else {
          errorMessage.value = Object.values(errors)[0][0];
        }
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
:root {
  --primary: #11A864;
  --secondary: #566D83;
  --dark: #0D1E2E;
  --medium-dark: #213243;
  --medium: #475B6D;
  --light: #CED4DA;
}

.login-page {
  display: flex;
  min-height: 100vh;
  background-color: var(--dark);
  color: white;
}

.login-layout {
  display: flex;
  width: 100%;
  height: 100vh;
}

.login-sidebar {
  width: 25%;
  min-width: 400px;
  background-color: #0D1E2E;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  position: relative;
  z-index: 1;
  height: 100%;
  /* overflow-y: auto; */
}

.login-graphic {
  flex: 1;
  background-image: url('/images/login-background.png');
  background-size: cover;
  background-position: center;
}

.logo-container {
  margin-bottom: 2.5rem;
}

.login-logo {
  max-width: 120px;
  height: auto;
}

.login-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

h1 {
  font-size: 2rem;
  font-weight: 600;
  color: white;
  margin-bottom: 0.5rem;
}

.login-description {
  color: var(--secondary);
  margin-bottom: 2rem;
  font-size: 0.95rem;
  max-width: 90%;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  color: var(--light);
}

.form-group input {
  padding: 0.75rem;
  background-color: #1A2A3A;
  border: 1px solid #2A3A4A;
  border-radius: 4px;
  color: white;
  font-size: 0.95rem;
}

.form-group input:focus {
  border-color: var(--primary);
  outline: none;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--light);
  cursor: pointer;
}

.remember-me input {
  accent-color: var(--primary);
}

.forgot-password {
  color: var(--primary);
  text-decoration: none;
}

.error-message {
  color: #e74c3c;
  padding: 0.75rem;
  background-color: rgba(231, 76, 60, 0.1);
  border-radius: 4px;
  font-size: 0.875rem;
  border-left: 3px solid #e74c3c;
}

.login-button {
  background-color: #475B6D;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.8rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
  font-weight: 500;
}

.login-button:hover {
  background-color: #0D9255;
}

.login-button:disabled {
  background-color: #475B6D;
  cursor: not-allowed;
}

.account-hints {
  margin-top: auto;
  margin-bottom: 1.5rem;
  color: var(--secondary);
  font-size: 0.9rem;
}

.create-account-link {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
}

.credentials-hint {
  margin-top: 1.5rem;
  padding: 1rem;
  background-color: rgba(86, 109, 131, 0.2);
  border-radius: 6px;
  font-size: 0.875rem;
  color: var(--light);

}

.credentials-hint p {
  margin: 0.25rem 0;
  visibility: hidden;
}

.login-footer {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.8rem;
  color: var(--medium);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .login-layout {
    flex-direction: column;
    height: auto;
  }
  
  .login-sidebar {
    width: 100%;
    min-width: 100%;
    padding: 1.5rem;
  }
  
  .login-graphic {
    display: none;
  }
}
</style> 