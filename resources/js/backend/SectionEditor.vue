<template>
  <AdminLayout>
    <div class="section-editor">
      <header class="page-header">
        <div class="header-left">
          <router-link :to="`/admin/pages/${section?.page_id}/sections`" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Sections
          </router-link>
          <h1>{{ section ? `Edit Content: ${section.name}` : 'Section Editor' }}</h1>
        </div>
        <div class="header-actions">
          <button 
            @click="saveContent" 
            class="btn btn-primary" 
            :disabled="isLoading || isSaving"
          >
            <i class="fas fa-save"></i> {{ isSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </header>
      
      <div class="content-container">
        <div v-if="isLoading" class="loading">
          Loading section content...
        </div>
        
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>
        
        <div v-else-if="!section" class="error-message">
          Section not found
        </div>
        
        <div v-else class="editor-container">
          <div class="editor-sidebar">
            <div class="section-info">
              <h3>Section Information</h3>
              <div class="info-item">
                <span class="label">Type:</span>
                <span class="value">{{ formatSectionType(section.type) }}</span>
              </div>
              <div class="info-item">
                <span class="label">Status:</span>
                <span :class="['status-badge', section.is_active ? 'active' : 'inactive']">
                  {{ section.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="info-item">
                <span class="label">Last Updated:</span>
                <span class="value">{{ formatDate(section.updated_at) }}</span>
              </div>
            </div>
            
            <div class="schema-info">
              <h3>Schema Information</h3>
              <p class="description">
                Edit the JSON content below according to the section type. Fields will vary based on the selected type.
              </p>
              <div class="schema-fields">
                <h4>Available Fields:</h4>
                <ul>
                  <li v-for="(field, key) in getSchemaFields(section.type)" :key="key">
                    <strong>{{ key }}</strong>: {{ field.description }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="editor-main">
            <div v-if="currentSchema">
              <JsonEditor 
                ref="jsonEditor"
                :schema="currentSchema"
                v-model="contentData"
                @input="handleEditorInput"
              >
                <div class="editor-actions">
                  <button 
                    @click="saveContent" 
                    class="btn btn-primary" 
                    :disabled="isSaving"
                  >
                    {{ isSaving ? 'Saving...' : 'Save Changes' }}
                  </button>
                  <button 
                    @click="resetContent"
                    class="btn btn-secondary"
                  >
                    Reset Changes
                  </button>
                </div>
              </JsonEditor>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AdminLayout from './AdminLayout.vue';
import JsonEditor from './components/JsonEditor.vue';

export default defineComponent({
  components: {
    AdminLayout,
    JsonEditor
  },
  
  setup() {
    const route = useRoute();
    const router = useRouter();
    const sectionId = route.params.id;
    const jsonEditor = ref(null);

    const section = ref(null);
    const contentData = ref({});
    const isLoading = ref(true);
    const isSaving = ref(false);
    const error = ref(null);
    const hasChanges = ref(false);

    const fetchSection = async () => {
      isLoading.value = true;
      error.value = null;
      
      try {
        const response = await axios.get(`/api/admin/sections/${sectionId}`);
        section.value = response.data;
        
        // Initialize the content with current content
        contentData.value = { ...section.value.content };
      } catch (err) {
        console.error('Error fetching section:', err);
        error.value = 'Failed to load section. Please try again.';
      } finally {
        isLoading.value = false;
      }
    };

    const saveContent = async () => {
      isSaving.value = true;
      error.value = null;
      
      try {
        await axios.put(`/api/admin/sections/${sectionId}/content`, {
          content: contentData.value
        });
        
        // Update section with the new content
        section.value.content = { ...contentData.value };
        
        // Show success message
        showSavedMessage();
      } catch (err) {
        console.error('Error saving content:', err);
        error.value = 'Failed to save content. Please try again.';
      } finally {
        isSaving.value = false;
      }
    };

    const resetContent = () => {
      if (jsonEditor.value) {
        jsonEditor.value.reset();
      }
    };

    const handleEditorInput = () => {
      hasChanges.value = true;
    };

    const showSavedMessage = () => {
      // Display a success message (could use a toast notification here)
      alert('Content saved successfully!');
      hasChanges.value = false;
    };

    const formatSectionType = (type) => {
      const types = {
        hero: 'Hero Section',
        content: 'Content Block',
        cards: 'Card Grid',
        features: 'Feature List',
        cta: 'Call to Action',
        testimonials: 'Testimonials',
        custom: 'Custom Section'
      };
      
      return types[type] || type;
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString();
    };

    const getSchemaFields = (type) => {
      const schemas = {
        hero: {
          title: { description: 'Main heading text' },
          subtitle: { description: 'Subheading text' },
          cta_text: { description: 'Call-to-action button text' },
          cta_link: { description: 'Button link URL' },
          background_image: { description: 'Background image URL' }
        },
        content: {
          title: { description: 'Section title' },
          content: { description: 'Main content (HTML allowed)' }
        },
        cards: {
          title: { description: 'Section title' },
          cards: { description: 'Array of card objects with title, content, and image' }
        },
        features: {
          title: { description: 'Section title' },
          features: { description: 'Array of feature objects with title, description, and icon' }
        },
        cta: {
          title: { description: 'Call-to-action title' },
          description: { description: 'Description text' },
          button_text: { description: 'Button text' },
          button_link: { description: 'Button link URL' }
        },
        testimonials: {
          title: { description: 'Section title' },
          testimonials: { description: 'Array of testimonial objects with quote, author, and role' }
        },
        custom: { description: 'Custom JSON structure' }
      };
      
      return schemas[type] || {};
    };

    const currentSchema = computed(() => {
      if (!section.value) return null;
      
      const type = section.value.type;
      
      const schemas = {
        hero: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'Title' },
            subtitle: { type: 'string', title: 'Subtitle' },
            cta_text: { type: 'string', title: 'CTA Button Text' },
            cta_link: { type: 'string', title: 'CTA Button Link' },
            background_image: { type: 'string', title: 'Background Image URL' }
          }
        },
        content: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'Title' },
            content: { type: 'string', title: 'Content (HTML)', format: 'textarea' }
          }
        },
        cards: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'Section Title' },
            cards: {
              type: 'array',
              title: 'Cards',
              items: {
                type: 'object',
                properties: {
                  title: { type: 'string', title: 'Card Title' },
                  content: { type: 'string', title: 'Card Content' },
                  image: { type: 'string', title: 'Card Image URL' }
                }
              }
            }
          }
        },
        features: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'Section Title' },
            features: {
              type: 'array',
              title: 'Features',
              items: {
                type: 'object',
                properties: {
                  title: { type: 'string', title: 'Feature Title' },
                  description: { type: 'string', title: 'Feature Description' },
                  icon: { type: 'string', title: 'Feature Icon' }
                }
              }
            }
          }
        },
        cta: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'CTA Title' },
            description: { type: 'string', title: 'Description', format: 'textarea' },
            button_text: { type: 'string', title: 'Button Text' },
            button_link: { type: 'string', title: 'Button Link' }
          }
        },
        testimonials: {
          type: 'object',
          properties: {
            title: { type: 'string', title: 'Section Title' },
            testimonials: {
              type: 'array',
              title: 'Testimonials',
              items: {
                type: 'object',
                properties: {
                  quote: { type: 'string', title: 'Quote', format: 'textarea' },
                  author: { type: 'string', title: 'Author Name' },
                  role: { type: 'string', title: 'Author Role/Company' }
                }
              }
            }
          }
        },
        custom: {
          type: 'object',
          properties: {}
        }
      };
      
      return schemas[type] || schemas.custom;
    });

    // Handle page leave with unsaved changes
    onMounted(() => {
      fetchSection();
      
      window.addEventListener('beforeunload', (e) => {
        if (hasChanges.value) {
          e.preventDefault();
          e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
      });
    });

    return {
      section,
      contentData,
      isLoading,
      isSaving,
      error,
      jsonEditor,
      currentSchema,
      fetchSection,
      saveContent,
      resetContent,
      handleEditorInput,
      formatSectionType,
      formatDate,
      getSchemaFields
    };
  }
});
</script>

<style scoped>
.section-editor {
  padding: 1rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-left {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: #3498db;
  text-decoration: none;
  font-size: 0.875rem;
}

.back-btn:hover {
  text-decoration: underline;
}

.page-header h1 {
  margin: 0;
  color: #2c3e50;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: #3498db;
  color: white;
}

.btn-primary:hover {
  background-color: #2980b9;
}

.btn-primary:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #95a5a6;
  color: white;
}

.btn-secondary:hover {
  background-color: #7f8c8d;
}

.content-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.loading, .error-message {
  padding: 2rem;
  text-align: center;
  color: #7f8c8d;
}

.error-message {
  color: #e74c3c;
}

.editor-container {
  display: flex;
  min-height: 600px;
}

.editor-sidebar {
  width: 300px;
  padding: 1.5rem;
  border-right: 1px solid #ecf0f1;
  background-color: #f8f9fa;
}

.section-info, .schema-info {
  margin-bottom: 2rem;
}

.section-info h3, .schema-info h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: #2c3e50;
}

.info-item {
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
}

.info-item .label {
  font-weight: 600;
  color: #7f8c8d;
  margin-right: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-badge.active {
  background-color: #2ecc71;
  color: white;
}

.status-badge.inactive {
  background-color: #95a5a6;
  color: white;
}

.description {
  color: #7f8c8d;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.schema-fields h4 {
  margin: 0 0 0.5rem;
  font-size: 0.875rem;
  color: #2c3e50;
}

.schema-fields ul {
  padding-left: 1.25rem;
  margin-top: 0;
}

.schema-fields li {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  color: #34495e;
}

.editor-main {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
}

.editor-actions {
  margin-top: 20px;
  display: flex;
  gap: 10px;
}
</style> 