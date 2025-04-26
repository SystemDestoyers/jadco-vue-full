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
        
        <!-- Email Settings Card -->
        <div class="settings-card">
          <div class="card-header">
            <h2>Email Settings</h2>
          </div>
          <div class="card-content">
            <div class="settings-section">
              <p class="section-description">
                Configure how emails are processed and sent from the application.
              </p>
              
              <div class="setting-item">
                <div class="setting-label">
                  Email Queue Processing
                </div>
                <div class="setting-description">
                  When enabled, emails will be processed in the background for faster form submissions. 
                  Requires a queue worker to be running.
                </div>
                <div class="setting-control toggle-control">
                  <label class="toggle-switch">
                    <input type="checkbox" v-model="useEmailQueue">
                    <span class="toggle-slider"></span>
                  </label>
                  <span class="toggle-label">{{ useEmailQueue ? 'Enabled' : 'Disabled' }}</span>
                </div>
              </div>
              
              <div class="setting-info" v-if="useEmailQueue">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i>
                  <div>
                    <strong>Queue Worker Required</strong>
                    <p>To process emails in the background, make sure a queue worker is running:</p>
                    <code>php artisan queue:work</code>
                  </div>
                </div>
              </div>

              <hr class="settings-divider">
              
              <div class="setting-group">
                <div class="setting-group-title">
                  <button type="button" class="btn-collapse" @click="showSmtpSettings = !showSmtpSettings">
                    <i :class="showSmtpSettings ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                    SMTP Configuration
                  </button>
                </div>
                
                <div class="collapsible-content" v-show="showSmtpSettings">
                  <div class="setting-item">
                    <div class="setting-label">SMTP Host</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="smtpHost" placeholder="e.g., smtp.example.com">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">SMTP Port</div>
                    <div class="setting-control">
                      <input type="number" class="form-control" v-model="smtpPort" placeholder="e.g., 587 or 465">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Encryption</div>
                    <div class="setting-control">
                      <select class="form-select" v-model="smtpEncryption">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Username</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="smtpUsername" placeholder="Email username">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Password</div>
                    <div class="setting-control">
                      <input type="password" class="form-control" v-model="smtpPassword" placeholder="Email password">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">From Address</div>
                    <div class="setting-control">
                      <input type="email" class="form-control" v-model="smtpFromAddress" placeholder="noreply@example.com">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">From Name</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="smtpFromName" placeholder="Your Company Name">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Admin Email</div>
                    <div class="setting-description">Email address where contact form submissions will be sent</div>
                    <div class="setting-control">
                      <input type="email" class="form-control" v-model="adminEmail" placeholder="jad@jadco.co">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Test Connection</div>
                    <div class="setting-control">
                      <button type="button" class="btn btn-outline-primary" @click="testSmtpConnection" :disabled="isTestingSmtp">
                        <span v-if="isTestingSmtp">
                          <i class="fas fa-spinner fa-spin"></i> Testing...
                        </span>
                        <span v-else>
                          <i class="fas fa-paper-plane"></i> Send Test Email
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <hr class="settings-divider">
              
              <div class="setting-group">
                <div class="setting-group-title">
                  <button type="button" class="btn-collapse" @click="showTemplateSettings = !showTemplateSettings">
                    <i :class="showTemplateSettings ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                    Email Templates
                  </button>
                </div>
                
                <div class="collapsible-content" v-show="showTemplateSettings">
                  <div class="setting-item">
                    <div class="setting-label">Contact Form Subject</div>
                    <div class="setting-description">Subject line for contact form emails. Use {name} for sender name.</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="emailSubject" placeholder="New Message from {name}">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Email Greeting</div>
                    <div class="setting-description">Greeting line for emails. Use {owner} for recipient name.</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="emailGreeting" placeholder="Hello {owner}">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Form Success Message</div>
                    <div class="setting-description">Message shown to users after successful form submission.</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="formSuccessMessage" placeholder="Thank you for your message!">
                    </div>
                  </div>
                  
                  <div class="setting-item">
                    <div class="setting-label">Email Signature</div>
                    <div class="setting-description">Signature line for emails.</div>
                    <div class="setting-control">
                      <input type="text" class="form-control" v-model="emailSignature" placeholder="Regards, JADCO Team">
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            
            <div class="settings-actions" v-if="emailSettingsChanged">
              <button @click="saveEmailSettings" class="btn btn-primary save-btn" :disabled="isSaving">
                <span v-if="isSaving">
                  <i class="fas fa-spinner fa-spin"></i> 
                  {{ t('saving') }}
                </span>
                <span v-else>
                  {{ t('save') }}
                </span>
              </button>
              <button @click="resetEmailSettings" class="btn btn-secondary cancel-btn">
                {{ t('cancel') }}
              </button>
            </div>
          </div>
        </div>

        <!-- System Settings Card (New) -->
        <div class="settings-card">
          <div class="card-header">
            <h2>System Settings</h2>
          </div>
          <div class="card-content">
            <div class="settings-section">
              <p class="section-description">
                Configure global system settings. These settings are stored in the database.
              </p>
              
              <div v-if="loadingDatabaseSettings" class="settings-loading">
                <i class="fas fa-spinner fa-spin"></i> Loading database settings...
              </div>
              
              <div v-else-if="databaseSettingsError" class="settings-error">
                <div class="alert alert-danger">
                  <i class="fas fa-exclamation-circle"></i>
                  <div>
                    <strong>Error loading settings</strong>
                    <p>{{ databaseSettingsError }}</p>
                    <button @click="loadDatabaseSettings" class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-sync"></i> Retry
                    </button>
                  </div>
                </div>
              </div>
              
              <div v-else>
                <!-- Group settings by category -->
                <div v-for="(settings, group) in groupedDatabaseSettings" :key="group" class="setting-group">
                  <div class="setting-group-title">
                    <button type="button" class="btn-collapse" @click="toggleSettingGroup(group)">
                      <i :class="expandedGroups[group] ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                      {{ formatGroupName(group) }}
                    </button>
                  </div>
                  
                  <div class="collapsible-content" v-show="expandedGroups[group]">
                    <div v-for="setting in settings" :key="setting.key" class="setting-item">
                      <div class="setting-label">{{ setting.name }}</div>
                      <div v-if="setting.description" class="setting-description">{{ setting.description }}</div>
                      
                      <!-- Different input types based on setting type -->
                      <div class="setting-control">
                        <!-- String type -->
                        <input v-if="setting.type === 'string'" 
                               type="text" 
                               class="form-control" 
                               v-model="databaseSettingsValues[setting.key]">
                        
                        <!-- Boolean type -->
                        <div v-else-if="setting.type === 'boolean'" class="toggle-control">
                          <label class="toggle-switch">
                            <input type="checkbox" v-model="databaseSettingsValues[setting.key]">
                            <span class="toggle-slider"></span>
                          </label>
                          <span class="toggle-label">{{ databaseSettingsValues[setting.key] ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                        
                        <!-- Integer type -->
                        <input v-else-if="setting.type === 'integer'" 
                               type="number" 
                               class="form-control" 
                               v-model.number="databaseSettingsValues[setting.key]">
                        
                        <!-- JSON/Array type -->
                        <JsonEditor v-else-if="setting.type === 'json' || setting.type === 'array'" 
                                  v-model="databaseSettingsValues[setting.key]"
                                  :rows="4"
                                  @error="jsonErrors[setting.key] = $event"
                                  :placeholder="`Enter JSON data for ${setting.name}`"></JsonEditor>
                        
                        <!-- Default text input for other types -->
                        <input v-else
                               type="text" 
                               class="form-control" 
                               v-model="databaseSettingsValues[setting.key]">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="settings-actions" v-if="databaseSettingsChanged">
              <button @click="saveDatabaseSettings" class="btn btn-primary save-btn" :disabled="isSaving">
                <span v-if="isSaving">
                  <i class="fas fa-spinner fa-spin"></i> 
                  {{ t('saving') }}
                </span>
                <span v-else>
                  {{ t('save') }}
                </span>
              </button>
              <button @click="resetDatabaseSettings" class="btn btn-secondary cancel-btn">
                {{ t('cancel') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Database Backup Card -->
        <div class="settings-card">
          <div class="card-header">
            <h2>{{ t('databaseBackup') }}</h2>
          </div>
          <div class="card-content">
            <div class="settings-section">
              <p class="section-description">
                {{ t('databaseBackupDescription') }}
              </p>
              
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                {{ t('databaseBackupWarning') }}
              </div>
              
              <div class="backup-actions">
                <button @click="runDatabaseBackup" class="btn btn-danger" :disabled="isBackupRunning">
                  <i class="fas" :class="isBackupRunning ? 'fa-spinner fa-spin' : 'fa-database'"></i>
                  {{ isBackupRunning ? t('runningBackup') : t('runBackup') }}
                </button>
              </div>
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
import JsonEditor from './components/JsonEditor.vue';
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

// Email settings
const useEmailQueue = ref(true);
const originalUseEmailQueue = ref(true);

// SMTP settings
const showSmtpSettings = ref(false);
const showTemplateSettings = ref(false);
const isTestingSmtp = ref(false);

const smtpHost = ref('');
const smtpPort = ref('');
const smtpUsername = ref('');
const smtpPassword = ref('');
const smtpEncryption = ref('tls');
const smtpFromAddress = ref('');
const smtpFromName = ref('');
const adminEmail = ref('');

const originalSmtpHost = ref('');
const originalSmtpPort = ref('');
const originalSmtpUsername = ref('');
const originalSmtpPassword = ref('');
const originalSmtpEncryption = ref('tls');
const originalSmtpFromAddress = ref('');
const originalSmtpFromName = ref('');
const originalAdminEmail = ref('');

// Email template settings
const emailSubject = ref('New Message from {name}');
const emailGreeting = ref('Hello {owner}');
const formSuccessMessage = ref('Thank you for your message! We will get back to you soon.');
const emailSignature = ref('Regards, JADCO Team');

const originalEmailSubject = ref('New Message from {name}');
const originalEmailGreeting = ref('Hello {owner}');
const originalFormSuccessMessage = ref('Thank you for your message! We will get back to you soon.');
const originalEmailSignature = ref('Regards, JADCO Team');

const emailSettingsChanged = computed(() => {
  return useEmailQueue.value !== originalUseEmailQueue.value ||
    smtpHost.value !== originalSmtpHost.value ||
    smtpPort.value !== originalSmtpPort.value ||
    smtpUsername.value !== originalSmtpUsername.value ||
    smtpPassword.value !== originalSmtpPassword.value ||
    smtpEncryption.value !== originalSmtpEncryption.value ||
    smtpFromAddress.value !== originalSmtpFromAddress.value ||
    smtpFromName.value !== originalSmtpFromName.value ||
    adminEmail.value !== originalAdminEmail.value ||
    emailSubject.value !== originalEmailSubject.value ||
    emailGreeting.value !== originalEmailGreeting.value ||
    formSuccessMessage.value !== originalFormSuccessMessage.value ||
    emailSignature.value !== originalEmailSignature.value;
});

// Display settings
const displaySettingsChanged = computed(() => {
  return colorMode.value !== originalColorMode.value || 
         textDirection.value !== originalTextDirection.value;
});

// Database settings (new)
const databaseSettings = ref([]);
const originalDatabaseSettings = ref([]);
const databaseSettingsValues = ref({});
const originalDatabaseSettingsValues = ref({});
const loadingDatabaseSettings = ref(true);
const databaseSettingsError = ref(null);
const expandedGroups = ref({});
const jsonErrors = ref({});

// Group database settings by category
const groupedDatabaseSettings = computed(() => {
  const groups = {};
  
  databaseSettings.value.forEach(setting => {
    if (!groups[setting.group]) {
      groups[setting.group] = [];
    }
    
    groups[setting.group].push(setting);
  });
  
  return groups;
});

// Check if database settings have changed
const databaseSettingsChanged = computed(() => {
  for (const key in databaseSettingsValues.value) {
    const currentValue = databaseSettingsValues.value[key];
    const originalValue = originalDatabaseSettingsValues.value[key];
    
    // Special handling for JSON/array types
    if (typeof currentValue === 'object' || typeof originalValue === 'object') {
      if (JSON.stringify(currentValue) !== JSON.stringify(originalValue)) {
        return true;
      }
    } 
    // For regular values
    else if (currentValue !== originalValue) {
      return true;
    }
  }
  
  return false;
});

// Toggle setting group expansion
const toggleSettingGroup = (group) => {
  expandedGroups.value[group] = !expandedGroups.value[group];
};

// Format group name for display
const formatGroupName = (group) => {
  return group.charAt(0).toUpperCase() + group.slice(1);
};

// Load database settings
const loadDatabaseSettings = async () => {
  loadingDatabaseSettings.value = true;
  databaseSettingsError.value = null;
  
  try {
    const response = await axios.get('/api/admin/database-settings');
    
    if (response.data.success) {
      databaseSettings.value = response.data.data;
      
      // Initialize expanded groups
      databaseSettings.value.forEach(setting => {
        if (!expandedGroups.value.hasOwnProperty(setting.group)) {
          expandedGroups.value[setting.group] = false;
        }
      });
      
      // Expand the first group by default
      const firstGroup = Object.keys(expandedGroups.value)[0];
      if (firstGroup) {
        expandedGroups.value[firstGroup] = true;
      }
      
      // Map settings to key-value pairs for easier editing
      const values = {};
      databaseSettings.value.forEach(setting => {
        if (setting.type === 'json' || setting.type === 'array') {
          try {
            // Pretty format JSON for display in textarea
            const value = typeof setting.value === 'string' 
              ? JSON.parse(setting.value) 
              : setting.value;
              
            values[setting.key] = JSON.stringify(value, null, 2);
          } catch (e) {
            values[setting.key] = setting.value || '';
          }
        } else if (setting.type === 'boolean') {
          // Convert to actual boolean
          values[setting.key] = setting.value === '1' || 
                               setting.value === 1 || 
                               setting.value === true || 
                               setting.value === 'true';
        } else {
          values[setting.key] = setting.value;
        }
      });
      
      databaseSettingsValues.value = values;
      originalDatabaseSettingsValues.value = JSON.parse(JSON.stringify(values));
    } else {
      databaseSettingsError.value = response.data.message || 'Unknown error occurred';
    }
  } catch (error) {
    console.error('Error loading database settings:', error);
    databaseSettingsError.value = error.response?.data?.message || error.message || 'Failed to load settings';
  } finally {
    loadingDatabaseSettings.value = false;
  }
};

// Save database settings
const saveDatabaseSettings = async () => {
  isSaving.value = true;
  
  // Check for JSON errors
  const hasJsonErrors = Object.values(jsonErrors.value).some(error => error !== '');
  if (hasJsonErrors) {
    toast.error('Please fix JSON format errors before saving');
    isSaving.value = false;
    return;
  }
  
  try {
    // Prepare settings for API
    const settingsToSave = {};
    
    // Process each setting based on its type
    for (const key in databaseSettingsValues.value) {
      const setting = databaseSettings.value.find(s => s.key === key);
      if (!setting) continue;
      
      let value = databaseSettingsValues.value[key];
      
      // Handle JSON/Array types - convert from formatted string back to JSON string
      if (setting.type === 'json' || setting.type === 'array') {
        try {
          // Parse the JSON text from textarea, then stringify it without formatting
          value = JSON.stringify(JSON.parse(value));
        } catch (e) {
          toast.error(`Invalid JSON in field: ${setting.name}`);
          isSaving.value = false;
          return;
        }
      }
      // Convert boolean to proper format
      else if (setting.type === 'boolean') {
        value = value ? 1 : 0;
      }
      
      settingsToSave[key] = value;
    }
    
    const response = await axios.post('/api/admin/database-settings', {
      settings: settingsToSave
    });
    
    if (response.data.success) {
      // Update original values to reflect saved state
      originalDatabaseSettingsValues.value = JSON.parse(JSON.stringify(databaseSettingsValues.value));
      
      toast.success('Settings saved successfully');
      
      // Check if there were any errors
      if (response.data.errors && response.data.errors.length > 0) {
        toast.info(`Note: ${response.data.errors.length} setting(s) had issues. Check console for details.`);
        console.warn('Settings update issues:', response.data.errors);
      }
    } else {
      toast.error('Failed to save settings: ' + response.data.message);
    }
  } catch (error) {
    console.error('Error saving database settings:', error);
    toast.error('Failed to save settings: ' + (error.response?.data?.message || error.message));
  } finally {
    isSaving.value = false;
  }
};

// Reset database settings to original values
const resetDatabaseSettings = () => {
  databaseSettingsValues.value = JSON.parse(JSON.stringify(originalDatabaseSettingsValues.value));
};

// Load settings from localStorage or API
const loadSettings = async () => {
  try {
    // First try to load from API
    const response = await axios.get('/api/admin/settings');
    if (response.data.success) {
      const settings = response.data.data;
      
      // Email settings
      if (settings.email) {
        if (settings.email.use_email_queue !== undefined) {
          useEmailQueue.value = settings.email.use_email_queue;
          originalUseEmailQueue.value = settings.email.use_email_queue;
        }
        
        // SMTP settings
        if (settings.email.smtp) {
          const smtp = settings.email.smtp;
          smtpHost.value = smtp.host || '';
          smtpPort.value = smtp.port || '';
          smtpUsername.value = smtp.username || '';
          smtpPassword.value = smtp.password || '';
          smtpEncryption.value = smtp.encryption || 'tls';
          smtpFromAddress.value = smtp.from_address || '';
          smtpFromName.value = smtp.from_name || '';
          adminEmail.value = smtp.admin_email || '';
          
          originalSmtpHost.value = smtp.host || '';
          originalSmtpPort.value = smtp.port || '';
          originalSmtpUsername.value = smtp.username || '';
          originalSmtpPassword.value = smtp.password || '';
          originalSmtpEncryption.value = smtp.encryption || 'tls';
          originalSmtpFromAddress.value = smtp.from_address || '';
          originalSmtpFromName.value = smtp.from_name || '';
          originalAdminEmail.value = smtp.admin_email || '';
        }
        
        // Template settings
        if (settings.email.templates) {
          const templates = settings.email.templates;
          emailSubject.value = templates.subject || 'New Message from {name}';
          emailGreeting.value = templates.greeting || 'Hello {owner}';
          formSuccessMessage.value = templates.success_message || 'Thank you for your message! We will get back to you soon.';
          emailSignature.value = templates.signature || 'Regards, JADCO Team';
          
          originalEmailSubject.value = templates.subject || 'New Message from {name}';
          originalEmailGreeting.value = templates.greeting || 'Hello {owner}';
          originalFormSuccessMessage.value = templates.success_message || 'Thank you for your message! We will get back to you soon.';
          originalEmailSignature.value = templates.signature || 'Regards, JADCO Team';
        }
      }
    }
    
    // Then try to load other settings from localStorage
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
    // If API fails, try to load use_email_queue from localStorage as fallback
    const savedUseEmailQueue = localStorage.getItem('use_email_queue');
    if (savedUseEmailQueue !== null) {
      useEmailQueue.value = savedUseEmailQueue === 'true';
      originalUseEmailQueue.value = savedUseEmailQueue === 'true';
    }
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

// Save email settings
const saveEmailSettings = async () => {
  isSaving.value = true;
  
  try {
    // Save to localStorage (just for queue setting)
    localStorage.setItem('use_email_queue', useEmailQueue.value);
    
    // Save all settings to API
    await axios.post('/api/admin/settings/email', {
      use_email_queue: useEmailQueue.value,
      smtp: {
        host: smtpHost.value,
        port: smtpPort.value,
        username: smtpUsername.value,
        password: smtpPassword.value,
        encryption: smtpEncryption.value,
        from_address: smtpFromAddress.value,
        from_name: smtpFromName.value,
        admin_email: adminEmail.value
      },
      templates: {
        subject: emailSubject.value,
        greeting: emailGreeting.value,
        success_message: formSuccessMessage.value,
        signature: emailSignature.value
      }
    });
    
    // Update original values
    originalUseEmailQueue.value = useEmailQueue.value;
    
    originalSmtpHost.value = smtpHost.value;
    originalSmtpPort.value = smtpPort.value;
    originalSmtpUsername.value = smtpUsername.value;
    originalSmtpPassword.value = smtpPassword.value;
    originalSmtpEncryption.value = smtpEncryption.value;
    originalSmtpFromAddress.value = smtpFromAddress.value;
    originalSmtpFromName.value = smtpFromName.value;
    originalAdminEmail.value = adminEmail.value;
    
    originalEmailSubject.value = emailSubject.value;
    originalEmailGreeting.value = emailGreeting.value;
    originalFormSuccessMessage.value = formSuccessMessage.value;
    originalEmailSignature.value = emailSignature.value;
    
    toast.success('Email settings saved successfully');
      
  } catch (error) {
    console.error('Error saving email settings:', error);
    toast.error('Failed to save email settings. Please try again.');
  } finally {
    isSaving.value = false;
  }
};

// Test SMTP connection
const testSmtpConnection = async () => {
  isTestingSmtp.value = true;
  
  try {
    const response = await axios.post('/api/admin/settings/email/test', {
      smtp: {
        host: smtpHost.value,
        port: smtpPort.value,
        username: smtpUsername.value,
        password: smtpPassword.value,
        encryption: smtpEncryption.value,
        from_address: smtpFromAddress.value,
        from_name: smtpFromName.value,
        admin_email: adminEmail.value
      }
    });
    
    if (response.data.success) {
      toast.success('Test email sent successfully!');
    } else {
      toast.error('Failed to send test email: ' + response.data.message);
    }
  } catch (error) {
    console.error('Error testing SMTP connection:', error);
    toast.error('Failed to send test email. Please check your settings and try again.');
  } finally {
    isTestingSmtp.value = false;
  }
};

// Reset email settings
const resetEmailSettings = () => {
  useEmailQueue.value = originalUseEmailQueue.value;
  
  smtpHost.value = originalSmtpHost.value;
  smtpPort.value = originalSmtpPort.value;
  smtpUsername.value = originalSmtpUsername.value;
  smtpPassword.value = originalSmtpPassword.value;
  smtpEncryption.value = originalSmtpEncryption.value;
  smtpFromAddress.value = originalSmtpFromAddress.value;
  smtpFromName.value = originalSmtpFromName.value;
  adminEmail.value = originalAdminEmail.value;
  
  emailSubject.value = originalEmailSubject.value;
  emailGreeting.value = originalEmailGreeting.value;
  formSuccessMessage.value = originalFormSuccessMessage.value;
  emailSignature.value = originalEmailSignature.value;
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

// Database backup
const isBackupRunning = ref(false);
const backupError = ref(null);

const runDatabaseBackup = async () => {
  if (isBackupRunning.value) return;
  
  // Ask for confirmation
  if (!confirm(t('confirmDatabaseBackup'))) {
    return;
  }
  
  isBackupRunning.value = true;
  backupError.value = null;
  
  try {
    const response = await axios.post('/api/admin/database-backup');
    
    if (response.data.success) {
      // Show success message
      alert(t('backupSuccess'));
      
      // Log details to console for debugging
      console.log('Backup details:', response.data.details);
      
      // Show detailed alert with command output
      if (response.data.details) {
        const detailsWindow = window.open('', '_blank', 'width=800,height=600');
        if (detailsWindow) {
          detailsWindow.document.write(`
            <html>
              <head>
                <title>Database Reset Details</title>
                <style>
                  body { font-family: monospace; padding: 20px; background: #f5f5f5; }
                  pre { background: #fff; padding: 15px; border: 1px solid #ddd; overflow: auto; }
                  h2 { color: #333; }
                </style>
              </head>
              <body>
                <h2>Database Reset Details</h2>
                <pre>${response.data.details}</pre>
              </body>
            </html>
          `);
          detailsWindow.document.close();
        }
      }
    } else {
      backupError.value = response.data.message;
      alert(t('backupFailed') + ': ' + response.data.message);
    }
  } catch (error) {
    console.error('Backup error:', error);
    backupError.value = error.response?.data?.message || error.message;
    alert(t('backupFailed') + ': ' + (error.response?.data?.message || error.message));
  } finally {
    isBackupRunning.value = false;
  }
};

// Initialize
onMounted(() => {
  loadSettings();
  loadDatabaseSettings();
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

.setting-description {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.toggle-control {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
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
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #3d8bfd;
}

input:checked + .toggle-slider:before {
  transform: translateX(26px);
}

.toggle-label {
  font-weight: 500;
}

.setting-info {
  margin-top: 1rem;
}

.alert {
  padding: 1rem;
  border-radius: 6px;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.alert-info {
  background-color: #cfe2ff;
  color: #084298;
  border: 1px solid #b6d4fe;
}

.alert i {
  font-size: 1.25rem;
  margin-top: 0.125rem;
}

.alert p {
  margin: 0.5rem 0 0;
  font-size: 0.875rem;
}

.alert code {
  display: block;
  margin-top: 0.5rem;
  background-color: rgba(255, 255, 255, 0.5);
  padding: 0.5rem;
  border-radius: 4px;
  font-family: monospace;
  font-size: 0.875rem;
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

[dir="rtl"] .alert {
  flex-direction: row-reverse;
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

[data-color-mode="dark"] .setting-label,
[data-color-mode="dark"] .toggle-label {
  color: #e9ecef;
}

[data-color-mode="dark"] .setting-description {
  color: #adb5bd;
}

[data-color-mode="dark"] .alert-info {
  background-color: rgba(207, 226, 255, 0.1);
  color: #9ec5fe;
  border-color: rgba(182, 212, 254, 0.2);
}

[data-color-mode="dark"] .alert code {
  background-color: rgba(255, 255, 255, 0.1);
}

.settings-divider {
  margin: 1.5rem 0;
  border: 0;
  border-top: 1px solid #f1f1f1;
}

.setting-group {
  margin-bottom: 1.25rem;
}

.setting-group-title {
  margin-bottom: 1rem;
}

.btn-collapse {
  background: none;
  border: none;
  padding: 0;
  font-weight: 600;
  color: #495057;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1rem;
}

.btn-collapse:hover {
  color: #3d8bfd;
}

.btn-collapse i {
  font-size: 0.875rem;
  transition: transform 0.2s ease;
}

.collapsible-content {
  padding-left: 0.5rem;
  border-left: 2px solid #f1f1f1;
  margin-left: 0.5rem;
  padding-top: 0.5rem;
}

.btn-outline-primary {
  background-color: transparent;
  border: 1px solid #3d8bfd;
  color: #3d8bfd;
  transition: all 0.2s;
}

.btn-outline-primary:hover:not(:disabled) {
  background-color: #3d8bfd;
  color: white;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="number"],
select.form-select {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  transition: border-color 0.2s;
  background-color: white;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="number"]:focus,
select.form-select:focus {
  border-color: #3d8bfd;
  outline: none;
}

/* Dark mode adjustments */
[data-color-mode="dark"] .settings-divider {
  border-top-color: #2d3238;
}

[data-color-mode="dark"] .collapsible-content {
  border-left-color: #2d3238;
}

[data-color-mode="dark"] .btn-collapse {
  color: #e9ecef;
}

[data-color-mode="dark"] .btn-collapse:hover {
  color: #3d8bfd;
}

[data-color-mode="dark"] input[type="text"],
[data-color-mode="dark"] input[type="email"],
[data-color-mode="dark"] input[type="password"],
[data-color-mode="dark"] input[type="number"],
[data-color-mode="dark"] select.form-select {
  background-color: #2d3238;
  border-color: #343a40;
  color: #e9ecef;
}

[data-color-mode="dark"] input[type="text"]:focus,
[data-color-mode="dark"] input[type="email"]:focus,
[data-color-mode="dark"] input[type="password"]:focus,
[data-color-mode="dark"] input[type="number"]:focus,
[data-color-mode="dark"] select.form-select:focus {
  border-color: #3d8bfd;
}

.settings-loading, .settings-error {
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.settings-loading {
  color: #6c757d;
}

.alert-danger {
  background-color: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}

[data-color-mode="dark"] .alert-danger {
  background-color: rgba(248, 215, 218, 0.15);
  border-color: rgba(245, 194, 199, 0.2);
  color: #ea868f;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-outline-danger {
  color: #dc3545;
  border: 1px solid #dc3545;
  background-color: transparent;
}

.btn-outline-danger:hover {
  color: white;
  background-color: #dc3545;
}

[data-color-mode="dark"] .btn-outline-danger {
  color: #ea868f;
  border-color: #ea868f;
}

[data-color-mode="dark"] .btn-outline-danger:hover {
  color: #212529;
  background-color: #ea868f;
}

.backup-actions {
  margin-top: 1.5rem;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.alert-warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

.alert i {
  margin-right: 0.5rem;
}
</style> 