<template>
  <AdminLayout>
    <div class="settings-page">
      <div class="page-header">
        <h1 class="page-title">{{ t('settings') }}</h1>
      </div>
      
      <div class="settings-container">
        <!-- Language Settings Card -->
        <div class="settings-card">
          <div class="card-header">
            <h2>{{ t('languageSettings') }}</h2>
          </div>
          <div class="card-content">
            <div class="settings-section">
              <p class="section-description">
                {{ t('chooseLanguage') }}
              </p>
              
              <div class="language-options">
                <div 
                  class="language-option" 
                  :class="{ 'active': currentLanguage === 'en' }"
                  @click="changeLanguage('en')"
                >
                  <div class="language-icon">
                    <span class="language-letter">EN</span>
                  </div>
                  <div class="language-info">
                    <div class="language-name">English</div>
                  </div>
                  <div class="selection-indicator" v-if="currentLanguage === 'en'">
                    <i class="fas fa-check"></i>
                  </div>
                </div>
                
                <div 
                  class="language-option" 
                  :class="{ 'active': currentLanguage === 'ar' }"
                  @click="changeLanguage('ar')"
                >
                  <div class="language-icon">
                    <span class="language-letter">عر</span>
                  </div>
                  <div class="language-info">
                    <div class="language-name">{{ currentLanguage === 'ar' ? 'العربية' : 'Arabic' }}</div>
                  </div>
                  <div class="selection-indicator" v-if="currentLanguage === 'ar'">
                    <i class="fas fa-check"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="settings-actions" v-if="languageChanged">
              <button @click="saveLanguageSettings" class="btn btn-primary save-btn" :disabled="isSaving">
                <span v-if="isSaving">
                  <i class="fas fa-spinner fa-spin"></i> 
                  {{ t('saving') }}
                </span>
                <span v-else>
                  {{ t('save') }}
                </span>
              </button>
              <button @click="resetLanguage" class="btn btn-secondary cancel-btn">
                {{ t('cancel') }}
              </button>
            </div>
          </div>
        </div>
        
        <!-- Display Settings Card -->
        <div class="settings-card">
          <div class="card-header">
            <h2>{{ t('displaySettings') }}</h2>
          </div>
          <div class="card-content">
            <div class="settings-section">
              <div class="setting-item">
                <div class="setting-label">
                  {{ t('colorMode') }}
                </div>
                <div class="setting-control">
                  <select v-model="colorMode" class="form-select">
                    <option value="light">{{ t('light') }}</option>
                    <option value="dark">{{ t('dark') }}</option>
                    <option value="auto">{{ t('auto') }}</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item" v-if="currentLanguage === 'ar'">
                <div class="setting-label">
                  {{ t('textDirection') }}
                </div>
                <div class="setting-control">
                  <select v-model="textDirection" class="form-select">
                    <option value="rtl">{{ t('rtl') }}</option>
                    <option value="ltr">{{ t('ltr') }}</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="settings-actions" v-if="displaySettingsChanged">
              <button @click="saveDisplaySettings" class="btn btn-primary save-btn" :disabled="isSaving">
                <span v-if="isSaving">
                  <i class="fas fa-spinner fa-spin"></i> 
                  {{ t('saving') }}
                </span>
                <span v-else>
                  {{ t('save') }}
                </span>
              </button>
              <button @click="resetDisplaySettings" class="btn btn-secondary cancel-btn">
                {{ t('cancel') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import AdminLayout from './AdminLayout.vue';
import { toast } from 'vue3-toastify';
import axios from 'axios';
import { t, getCurrentLanguage, setLanguage } from '../i18n';

// Language settings
const currentLanguage = ref(getCurrentLanguage());
const originalLanguage = ref(getCurrentLanguage());
const languageChanged = computed(() => currentLanguage.value !== originalLanguage.value);

// Display settings 
const colorMode = ref('light');
const originalColorMode = ref('light');
const textDirection = ref('ltr'); 
const originalTextDirection = ref('ltr');
const isSaving = ref(false);

const displaySettingsChanged = computed(() => {
  return colorMode.value !== originalColorMode.value || 
         textDirection.value !== originalTextDirection.value;
});

// Load settings from localStorage or API
const loadSettings = () => {
  try {
    // Try to load from localStorage first
    const savedLanguage = localStorage.getItem('admin_language');
    const savedColorMode = localStorage.getItem('admin_color_mode');
    const savedTextDirection = localStorage.getItem('admin_text_direction');
    
    if (savedLanguage) {
      currentLanguage.value = savedLanguage;
      originalLanguage.value = savedLanguage;
    }
    
    if (savedColorMode) {
      colorMode.value = savedColorMode;
      originalColorMode.value = savedColorMode;
    }
    
    if (savedTextDirection) {
      textDirection.value = savedTextDirection;
      originalTextDirection.value = savedTextDirection;
    }
    
    // Apply the settings
    applyLanguageSettings();
    applyDisplaySettings();
    
  } catch (error) {
    console.error('Error loading settings:', error);
  }
};

// Change language
const changeLanguage = (lang) => {
  currentLanguage.value = lang;
  
  // If it's Arabic, set text direction to RTL
  if (lang === 'ar' && textDirection.value !== 'rtl') {
    textDirection.value = 'rtl';
  }
  
  // If switching back to English, set text direction to LTR
  if (lang === 'en' && textDirection.value !== 'ltr') {
    textDirection.value = 'ltr';
  }
};

// Save language settings
const saveLanguageSettings = async () => {
  isSaving.value = true;
  
  try {
    // Save to localStorage and apply via i18n helper
    setLanguage(currentLanguage.value);
    
    // Save to API (if implemented)
    // await axios.post('/api/admin/settings/language', {
    //   language: currentLanguage.value
    // });
    
    originalLanguage.value = currentLanguage.value;
    
    toast.success(t('settingsSaved'));
      
  } catch (error) {
    console.error('Error saving language settings:', error);
    toast.error(t('settingsError'));
  } finally {
    isSaving.value = false;
  }
};

// Reset language
const resetLanguage = () => {
  currentLanguage.value = originalLanguage.value;
};

// Apply language settings
const applyLanguageSettings = () => {
  document.documentElement.lang = currentLanguage.value;
  
  // Add a data attribute to help with CSS selectors
  document.documentElement.setAttribute('data-language', currentLanguage.value);
};

// Save display settings
const saveDisplaySettings = async () => {
  isSaving.value = true;
  
  try {
    // Save to localStorage
    localStorage.setItem('admin_color_mode', colorMode.value);
    localStorage.setItem('admin_text_direction', textDirection.value);
    
    // Apply the settings
    applyDisplaySettings();
    
    // Trigger color mode change event
    window.dispatchEvent(new CustomEvent('colorModeChanged', {
      detail: { mode: colorMode.value }
    }));
    
    // Save to API (if implemented)
    // await axios.post('/api/admin/settings/display', {
    //   colorMode: colorMode.value,
    //   textDirection: textDirection.value
    // });
    
    originalColorMode.value = colorMode.value;
    originalTextDirection.value = textDirection.value;
    
    toast.success(t('settingsSaved'));
      
  } catch (error) {
    console.error('Error saving display settings:', error);
    toast.error(t('settingsError'));
  } finally {
    isSaving.value = false;
  }
};

// Reset display settings
const resetDisplaySettings = () => {
  colorMode.value = originalColorMode.value;
  textDirection.value = originalTextDirection.value;
};

// Apply display settings
const applyDisplaySettings = () => {
  // Apply color mode
  document.documentElement.setAttribute('data-color-mode', colorMode.value);
  
  // Apply text direction
  document.documentElement.dir = textDirection.value;
};

// Watch for direct changes in language to apply RTL/LTR
watch(currentLanguage, (newLang) => {
  // When language changes, update the document's lang attribute
  document.documentElement.lang = newLang;
  document.documentElement.setAttribute('data-language', newLang);
  
  // Auto-switch direction based on language if not manually set otherwise
  if (newLang === 'ar' && textDirection.value !== 'rtl') {
    textDirection.value = 'rtl';
    document.documentElement.dir = 'rtl';
  } else if (newLang === 'en' && textDirection.value !== 'ltr') {
    textDirection.value = 'ltr';
    document.documentElement.dir = 'ltr';
  }
});

// Initialize
onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.settings-page {
  padding: 1.5rem;
}

.page-header {
  margin-bottom: 1.5rem;
}

.page-title {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
}

.settings-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 992px) {
  .settings-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

.settings-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f1f1;
}

.card-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
  font-weight: 600;
}

.card-content {
  padding: 1.5rem;
}

.settings-section {
  margin-bottom: 1.5rem;
}

.section-description {
  color: #6c757d;
  margin-bottom: 1.25rem;
}

.language-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.language-option {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  cursor: pointer;
  transition: all 0.2s;
}

.language-option:hover {
  border-color: #3d8bfd;
  background-color: #f8f9fa;
}

.language-option.active {
  border-color: #3d8bfd;
  background-color: #f0f7ff;
}

.language-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 1rem;
  background-color: #f0f7ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.language-letter {
  font-size: 1rem;
  font-weight: 600;
  color: #3d8bfd;
}

.language-option.active .language-icon {
  background-color: #3d8bfd;
}

.language-option.active .language-letter {
  color: white;
}

.language-info {
  flex: 1;
}

.language-name {
  font-weight: 600;
  color: #212529;
}

.selection-indicator {
  color: #3d8bfd;
  font-size: 1.25rem;
}

.settings-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f1f1f1;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background-color: #3d8bfd;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2d7af2;
}

.btn-primary:disabled {
  background-color: #a9c6fd;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.setting-item {
  margin-bottom: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.setting-label {
  font-weight: 500;
  color: #212529;
}

/* RTL specific styles */
[dir="rtl"] .language-icon {
  margin-right: 0;
  margin-left: 1rem;
}

[dir="rtl"] .settings-actions {
  flex-direction: row-reverse;
}

[dir="rtl"] .language-options {
  text-align: right;
}

/* Dark mode styles when implemented */
[data-color-mode="dark"] .settings-card {
  background-color: #1e2124;
  color: #e9ecef;
}

[data-color-mode="dark"] .card-header {
  border-bottom-color: #2d3238;
}

[data-color-mode="dark"] .card-header h2 {
  color: #e9ecef;
}

[data-color-mode="dark"] .section-description {
  color: #adb5bd;
}

[data-color-mode="dark"] .language-option {
  border-color: #2d3238;
}

[data-color-mode="dark"] .language-option:hover {
  background-color: #2d3238;
  border-color: #3d8bfd;
}

[data-color-mode="dark"] .language-option.active {
  background-color: rgba(61, 139, 253, 0.15);
  border-color: #3d8bfd;
}

[data-color-mode="dark"] .language-name {
  color: #e9ecef;
}

[data-color-mode="dark"] .settings-actions {
  border-top-color: #2d3238;
}

[data-color-mode="dark"] .btn-secondary {
  background-color: #495057;
}

[data-color-mode="dark"] .btn-secondary:hover {
  background-color: #343a40;
}
</style> 