<template>
  <div class="profile-page">
    <h1 class="page-title">
      <i class="fas fa-user-cog"></i> Profile Settings
    </h1>

    <div class="profile-content">
      <div class="profile-header">
        <div class="profile-avatar-container">
          <img :src="profileImage" alt="Profile Avatar" class="profile-avatar" />
          <div class="profile-avatar-overlay">
            <label for="avatar-upload" class="avatar-upload-label">
              <i class="fas fa-camera"></i>
            </label>
            <input 
              id="avatar-upload" 
              type="file" 
              accept="image/*" 
              @change="handleAvatarUpload" 
              class="avatar-upload-input" 
            />
          </div>
        </div>
        <div class="profile-header-info">
          <h2 class="profile-name">{{ user.name }}</h2>
          <p class="profile-email">{{ user.email }}</p>
          <p class="profile-role">Administrator</p>
        </div>
      </div>

      <div class="profile-tabs">
        <button 
          class="tab-button" 
          :class="{ active: activeTab === 'account' }"
          @click="activeTab = 'account'"
        >
          <i class="fas fa-user"></i> Account
        </button>
        <button 
          class="tab-button" 
          :class="{ active: activeTab === 'security' }"
          @click="activeTab = 'security'"
        >
          <i class="fas fa-shield-alt"></i> Security
        </button>
        <button 
          class="tab-button" 
          :class="{ active: activeTab === 'preferences' }"
          @click="activeTab = 'preferences'"
        >
          <i class="fas fa-sliders-h"></i> Preferences
        </button>
      </div>

      <div class="profile-tab-content">
        <!-- Account Tab -->
        <div v-if="activeTab === 'account'" class="tab-pane">
          <h3 class="tab-title">Account Information</h3>
          <form @submit.prevent="updateProfile" class="profile-form">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input
                id="name"
                v-model="user.name"
                type="text"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input
                id="email"
                v-model="user.email"
                type="email"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label for="bio">Bio</label>
              <textarea
                id="bio"
                v-model="user.bio"
                class="form-control"
                rows="4"
                placeholder="Tell us about yourself"
              ></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary" :disabled="isSaving">
                <i class="fas fa-save"></i> {{ isSaving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Security Tab -->
        <div v-if="activeTab === 'security'" class="tab-pane">
          <h3 class="tab-title">Password & Security</h3>
          <form @submit.prevent="updatePassword" class="profile-form">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input
                id="current_password"
                v-model="passwordData.current"
                type="password"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label for="new_password">New Password</label>
              <input
                id="new_password"
                v-model="passwordData.new"
                type="password"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirm New Password</label>
              <input
                id="password_confirmation"
                v-model="passwordData.confirmation"
                type="password"
                class="form-control"
                required
              />
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary" :disabled="isSavingPassword">
                <i class="fas fa-lock"></i> {{ isSavingPassword ? 'Updating...' : 'Update Password' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Preferences Tab -->
        <div v-if="activeTab === 'preferences'" class="tab-pane">
          <h3 class="tab-title">Interface Preferences</h3>
          
          <div class="preference-group">
            <h4 class="preference-title">Theme</h4>
            <div class="preference-options">
              <div 
                class="theme-option" 
                :class="{ active: colorMode === 'light' }"
                @click="updateColorMode('light')"
              >
                <div class="theme-preview light-theme"></div>
                <span>Light Mode</span>
              </div>
              <div 
                class="theme-option" 
                :class="{ active: colorMode === 'dark' }"
                @click="updateColorMode('dark')"
              >
                <div class="theme-preview dark-theme"></div>
                <span>Dark Mode</span>
              </div>
              <div 
                class="theme-option" 
                :class="{ active: colorMode === 'auto' }"
                @click="updateColorMode('auto')"
              >
                <div class="theme-preview auto-theme"></div>
                <span>System Default</span>
              </div>
            </div>
          </div>

          <div class="preference-group">
            <h4 class="preference-title">Text Direction</h4>
            <div class="toggle-option">
              <label class="toggle-switch">
                <input 
                  type="checkbox" 
                  :checked="rtlMode"
                  @change="toggleDirection"
                />
                <span class="toggle-slider"></span>
              </label>
              <span class="toggle-label">Right-to-Left Text (RTL)</span>
            </div>
          </div>

          <div class="preference-group">
            <h4 class="preference-title">Language</h4>
            <select 
              v-model="selectedLanguage" 
              @change="changeLanguage" 
              class="language-select"
            >
              <option value="en">English</option>
              <option value="ar">Arabic</option>
              <option value="fr">French</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { getCurrentLanguage, setLanguage, isRTL, setDirection } from '../i18n';

// State
const user = reactive({
  name: '',
  email: '',
  bio: ''
});

const passwordData = reactive({
  current: '',
  new: '',
  confirmation: ''
});

const activeTab = ref('account');
const isSaving = ref(false);
const isSavingPassword = ref(false);
const profileImage = ref('/images/user.jpg');
const colorMode = ref(localStorage.getItem('admin_color_mode') || 'light');
const rtlMode = ref(isRTL());
const selectedLanguage = ref(getCurrentLanguage());

// Fetch user profile data
const fetchUserProfile = async () => {
  try {
    const response = await axios.get('/api/admin/user');
    if (response.data && response.data.user) {
      user.name = response.data.user.name;
      user.email = response.data.user.email;
      user.bio = response.data.user.bio || '';
      
      // If user has a profile image
      if (response.data.user.profile_image) {
        profileImage.value = response.data.user.profile_image;
      }
    }
  } catch (error) {
    console.error('Error fetching user profile:', error);
    toast.error('Failed to load profile information');
  }
};

// Update profile information
const updateProfile = async () => {
  isSaving.value = true;
  try {
    const response = await axios.put('/api/admin/profile', {
      name: user.name,
      email: user.email,
      bio: user.bio
    });
    
    toast.success('Profile updated successfully');
  } catch (error) {
    console.error('Error updating profile:', error);
    
    if (error.response && error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors;
      Object.keys(errors).forEach(field => {
        toast.error(errors[field][0]);
      });
    } else {
      toast.error('Failed to update profile');
    }
  } finally {
    isSaving.value = false;
  }
};

// Update password
const updatePassword = async () => {
  // Validate password match
  if (passwordData.new !== passwordData.confirmation) {
    toast.error('New passwords do not match');
    return;
  }
  
  isSavingPassword.value = true;
  try {
    const response = await axios.put('/api/admin/profile/password', {
      current_password: passwordData.current,
      password: passwordData.new,
      password_confirmation: passwordData.confirmation
    });
    
    toast.success('Password updated successfully');
    
    // Clear form
    passwordData.current = '';
    passwordData.new = '';
    passwordData.confirmation = '';
  } catch (error) {
    console.error('Error updating password:', error);
    
    if (error.response && error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors;
      Object.keys(errors).forEach(field => {
        toast.error(errors[field][0]);
      });
    } else if (error.response && error.response.data && error.response.data.message) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Failed to update password');
    }
  } finally {
    isSavingPassword.value = false;
  }
};

// Handle avatar upload
const handleAvatarUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  // Validate file is an image
  if (!file.type.startsWith('image/')) {
    toast.error('Please select an image file');
    return;
  }
  
  // Create form data
  const formData = new FormData();
  formData.append('profile_image', file);
  
  try {
    const response = await axios.post('/api/admin/profile/image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    if (response.data && response.data.image_path) {
      profileImage.value = response.data.image_path;
      toast.success('Profile image updated');
    }
  } catch (error) {
    console.error('Error uploading profile image:', error);
    toast.error('Failed to upload profile image');
  }
};

// Update color mode preference
const updateColorMode = (mode) => {
  colorMode.value = mode;
  localStorage.setItem('admin_color_mode', mode);
  
  // Apply theme to document
  if (mode === 'auto') {
    // Check system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-color-mode', 'dark');
    } else {
      document.documentElement.setAttribute('data-color-mode', 'light');
    }
  } else {
    document.documentElement.setAttribute('data-color-mode', mode);
  }
  
  toast.info(`Theme changed to ${mode} mode`);
};

// Toggle RTL/LTR direction
const toggleDirection = () => {
  rtlMode.value = !rtlMode.value;
  setDirection(rtlMode.value ? 'rtl' : 'ltr');
  toast.info(`Text direction changed to ${rtlMode.value ? 'right-to-left' : 'left-to-right'}`);
};

// Change language
const changeLanguage = () => {
  setLanguage(selectedLanguage.value);
  toast.info(`Language changed to ${selectedLanguage.value === 'en' ? 'English' : selectedLanguage.value === 'ar' ? 'Arabic' : 'French'}`);
};

// Load profile data on component mount
onMounted(() => {
  fetchUserProfile();
});
</script>

<style scoped>
.profile-page {
  padding: 1.5rem;
}

.page-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.profile-content {
  background-color: var(--bg-card);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  padding: 2rem;
  background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-hover) 100%);
  color: white;
}

.profile-avatar-container {
  position: relative;
  width: 120px;
  height: 120px;
  margin-right: 2rem;
}

.profile-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid rgba(255, 255, 255, 0.2);
}

.profile-avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.profile-avatar-container:hover .profile-avatar-overlay {
  opacity: 1;
}

.avatar-upload-label {
  cursor: pointer;
  background-color: rgba(255, 255, 255, 0.9);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--theme-primary);
}

.avatar-upload-input {
  display: none;
}

.profile-header-info {
  flex: 1;
}

.profile-name {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.profile-email {
  font-size: 1rem;
  opacity: 0.9;
  margin-bottom: 0.25rem;
}

.profile-role {
  display: inline-block;
  background-color: rgba(255, 255, 255, 0.2);
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.profile-tabs {
  display: flex;
  border-bottom: 1px solid var(--border-color);
  background-color: var(--bg-card);
}

.tab-button {
  padding: 1rem 1.5rem;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 0.95rem;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  position: relative;
}

.tab-button.active {
  color: var(--theme-primary);
  font-weight: 500;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: var(--theme-primary);
}

.profile-tab-content {
  padding: 2rem;
}

.tab-title {
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  color: var(--text-primary);
}

.profile-form {
  max-width: 600px;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-primary);
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-md);
  background-color: var(--bg-main);
  color: var(--text-primary);
  font-size: 0.95rem;
}

.form-control:focus {
  border-color: var(--theme-primary);
  outline: none;
  box-shadow: 0 0 0 3px rgba(62, 104, 255, 0.1);
}

.form-actions {
  display: flex;
  justify-content: flex-start;
  margin-top: 2rem;
}

.btn-primary {
  background-color: var(--theme-primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: var(--border-radius-md);
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: var(--theme-primary-hover);
}

.btn-primary:disabled {
  background-color: var(--theme-secondary);
  cursor: not-allowed;
}

.preference-group {
  margin-bottom: 2rem;
}

.preference-title {
  font-size: 1rem;
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.preference-options {
  display: flex;
  gap: 1.5rem;
}

.theme-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
}

.theme-preview {
  width: 100px;
  height: 60px;
  border-radius: var(--border-radius-sm);
  border: 2px solid transparent;
  transition: border 0.2s;
}

.theme-option.active .theme-preview {
  border-color: var(--theme-primary);
}

.light-theme {
  background-color: #ffffff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dark-theme {
  background-color: #1a202c;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.auto-theme {
  background: linear-gradient(to right, #ffffff 50%, #1a202c 50%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.toggle-option {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--border-color);
  transition: .4s;
  border-radius: 30px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: var(--theme-primary);
}

input:checked + .toggle-slider:before {
  transform: translateX(30px);
}

.toggle-label {
  color: var(--text-primary);
  font-size: 0.95rem;
}

.language-select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-md);
  background-color: var(--bg-main);
  color: var(--text-primary);
  width: 200px;
  font-size: 0.95rem;
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }
  
  .profile-avatar-container {
    margin-right: 0;
    margin-bottom: 1.5rem;
  }
  
  .profile-tabs {
    overflow-x: auto;
    white-space: nowrap;
  }
  
  .tab-button {
    padding: 1rem;
  }
  
  .preference-options {
    flex-direction: column;
    gap: 1rem;
  }
}
</style> 