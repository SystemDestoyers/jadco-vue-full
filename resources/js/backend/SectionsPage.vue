<template>
  <AdminLayout>
    <div class="sections-page">
      <header class="page-header">
        <div class="header-left">
          <router-link to="/admin/pages" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Pages
          </router-link>
          <h1>{{ page ? `Sections for "${page.name}"` : 'Sections' }}</h1>
        </div>
        <button v-if="page" @click="openCreateModal" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add New Section
        </button>
      </header>
      
      <div class="content-container">
        <div v-if="isLoadingPage || isLoadingSections" class="loading">
          Loading sections...
        </div>
        
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>
        
        <div v-else-if="!page" class="error-message">
          Page not found
        </div>
        
        <div v-else class="sections-list">
          <div v-if="sections.length === 0" class="empty-state">
            <div class="empty-icon">
              <i class="fas fa-puzzle-piece"></i>
            </div>
            <h3>No Sections Yet</h3>
            <p>Start building your page by adding sections.</p>
            <button @click="openCreateModal" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add First Section
            </button>
          </div>
          
          <table v-else>
            <thead>
              <tr>
                <th style="width: 70px;">Order</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Updated At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="section in sortedSections" :key="section.id">
                <td>
                  <div class="order-controls">
                    <button 
                      @click="moveSection(section, 'up')" 
                      :disabled="section.order === 1"
                      class="order-btn"
                    >
                      <i class="fas fa-chevron-up"></i>
                    </button>
                    <span>{{ section.order }}</span>
                    <button 
                      @click="moveSection(section, 'down')" 
                      :disabled="section.order === sections.length"
                      class="order-btn"
                    >
                      <i class="fas fa-chevron-down"></i>
                    </button>
                  </div>
                </td>
                <td>{{ section.name }}</td>
                <td>{{ section.type }}</td>
                <td>
                  <span :class="['status-badge', section.is_active ? 'active' : 'inactive']">
                    {{ section.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>{{ formatDate(section.updated_at) }}</td>
                <td class="actions">
                  <button @click="openEditor(section)" class="action-btn edit">
                    <i class="fas fa-code"></i> Edit Content
                  </button>
                  <button @click="openEditModal(section)" class="action-btn">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button @click="confirmDelete(section)" class="action-btn delete">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>{{ editMode ? 'Edit Section' : 'Create New Section' }}</h2>
            <button @click="closeModal" class="close-btn">&times;</button>
          </div>
          
          <div class="modal-body">
            <form @submit.prevent="saveSection">
              <div class="form-group">
                <label for="name">Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  id="name" 
                  placeholder="Section Name" 
                  required
                />
                <small>Internal name (not shown to users)</small>
              </div>
              
              <div class="form-group">
                <label for="type">Section Type</label>
                <select v-model="form.type" id="type" required>
                  <option value="hero">Hero Section</option>
                  <option value="content">Content Block</option>
                  <option value="cards">Card Grid</option>
                  <option value="features">Feature List</option>
                  <option value="cta">Call to Action</option>
                  <option value="testimonials">Testimonials</option>
                  <option value="custom">Custom</option>
                </select>
              </div>
              
              <div class="form-group" v-if="!editMode">
                <label for="order">Position</label>
                <select v-model="form.order" id="order">
                  <option value="start">At the beginning</option>
                  <option value="end">At the end</option>
                </select>
              </div>
              
              <div class="form-group checkbox">
                <input 
                  v-model="form.is_active" 
                  type="checkbox" 
                  id="is_active"
                />
                <label for="is_active">Active</label>
              </div>
              
              <div class="modal-actions">
                <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  {{ isSubmitting ? 'Saving...' : 'Save Section' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="closeDeleteModal">
        <div class="modal-content delete-modal">
          <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button @click="closeDeleteModal" class="close-btn">&times;</button>
          </div>
          
          <div class="modal-body">
            <p>Are you sure you want to delete the section "<strong>{{ sectionToDelete?.name }}</strong>"?</p>
            <p class="warning">This action cannot be undone.</p>
            
            <div class="modal-actions">
              <button @click="closeDeleteModal" class="btn btn-secondary">Cancel</button>
              <button @click="deleteSection" class="btn btn-danger" :disabled="isDeleting">
                {{ isDeleting ? 'Deleting...' : 'Delete Section' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AdminLayout from './AdminLayout.vue';

const route = useRoute();
const router = useRouter();
const pageId = route.params.id;

const page = ref(null);
const sections = ref([]);
const isLoadingPage = ref(true);
const isLoadingSections = ref(true);
const error = ref(null);
const showModal = ref(false);
const editMode = ref(false);
const isSubmitting = ref(false);
const showDeleteModal = ref(false);
const sectionToDelete = ref(null);
const isDeleting = ref(false);

const form = reactive({
  id: null,
  name: '',
  type: 'content',
  order: 'end',
  is_active: true,
  content: {}
});

const fetchPage = async () => {
  isLoadingPage.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`/api/admin/pages/${pageId}`);
    page.value = response.data;
  } catch (err) {
    error.value = 'Failed to load page. Please try again.';
  } finally {
    isLoadingPage.value = false;
  }
};

const fetchSections = async () => {
  isLoadingSections.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`/api/admin/pages/${pageId}/sections`);
    sections.value = response.data;
  } catch (err) {
    error.value = 'Failed to load sections. Please try again.';
  } finally {
    isLoadingSections.value = false;
  }
};

const sortedSections = computed(() => {
  return [...sections.value].sort((a, b) => a.order - b.order);
});

const openCreateModal = () => {
  editMode.value = false;
  
  // Reset form
  Object.assign(form, {
    id: null,
    name: '',
    type: 'content',
    order: 'end',
    is_active: true,
    content: getDefaultContent('content')
  });
  
  showModal.value = true;
};

const openEditModal = (section) => {
  editMode.value = true;
  
  // Populate form with section data
  Object.assign(form, {
    id: section.id,
    name: section.name,
    type: section.type,
    is_active: section.is_active,
    content: section.content || {}
  });
  
  showModal.value = true;
};

const openEditor = (section) => {
  router.push(`/admin/sections/${section.id}/edit`);
};

const closeModal = () => {
  showModal.value = false;
};

const getDefaultContent = (type) => {
  const defaults = {
    hero: {
      title: 'Hero Title',
      subtitle: 'Hero Subtitle',
      cta_text: 'Learn More',
      cta_link: '#',
      background_image: ''
    },
    content: {
      title: 'Content Section',
      content: '<p>Add your content here</p>'
    },
    cards: {
      title: 'Card Section',
      cards: [
        { title: 'Card 1', content: 'Card 1 content', image: '' }
      ]
    },
    features: {
      title: 'Features',
      features: [
        { title: 'Feature 1', description: 'Feature 1 description', icon: '' }
      ]
    },
    cta: {
      title: 'Call to Action',
      description: 'CTA description here',
      button_text: 'Click Here',
      button_link: '#'
    },
    testimonials: {
      title: 'Testimonials',
      testimonials: [
        { quote: 'Testimonial text', author: 'Author Name', role: 'Role' }
      ]
    },
    custom: {}
  };
  
  return defaults[type] || {};
};

const saveSection = async () => {
  isSubmitting.value = true;
  
  try {
    if (editMode.value) {
      // Update existing section
      const response = await axios.put(`/api/admin/sections/${form.id}`, {
        name: form.name,
        type: form.type,
        is_active: form.is_active
      });
      
      // Update the section in the list
      const index = sections.value.findIndex(s => s.id === form.id);
      if (index !== -1) {
        // Preserve the order property from the response
        sections.value[index] = { ...sections.value[index], ...response.data };
      }
    } else {
      // Create new section
      const response = await axios.post(`/api/admin/pages/${pageId}/sections`, form);
      sections.value.push(response.data);
    }
    
    // Close the modal after successful save
    closeModal();
  } catch (err) {
    error.value = 'Failed to save section. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

const moveSection = async (section, direction) => {
  try {
    const response = await axios.put(`/api/admin/sections/${section.id}/order`, {
      direction: direction
    });
    
    // Refresh the sections list after reordering
    await fetchSections();
  } catch (err) {
    error.value = 'Failed to reorder section. Please try again.';
  }
};

const confirmDelete = (section) => {
  sectionToDelete.value = section;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  sectionToDelete.value = null;
};

const deleteSection = async () => {
  isDeleting.value = true;
  
  try {
    await axios.delete(`/api/admin/sections/${sectionToDelete.value.id}`);
    
    // Remove the section from the list
    const index = sections.value.findIndex(s => s.id === sectionToDelete.value.id);
    if (index !== -1) {
      sections.value.splice(index, 1);
    }
    
    // Close the modal after successful delete
    closeDeleteModal();
  } catch (err) {
    error.value = 'Failed to delete section. Please try again.';
  } finally {
    isDeleting.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString();
};

onMounted(() => {
  fetchPage();
  fetchSections();
});
</script>

<style scoped>
.sections-page {
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

.btn-secondary {
  background-color: #95a5a6;
  color: white;
}

.btn-secondary:hover {
  background-color: #7f8c8d;
}

.btn-danger {
  background-color: #e74c3c;
  color: white;
}

.btn-danger:hover {
  background-color: #c0392b;
}

.content-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.sections-list {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 0.75rem 1rem;
  text-align: left;
  border-bottom: 1px solid #ecf0f1;
}

th {
  font-weight: 600;
  color: #34495e;
  background-color: #f8f9fa;
}

.order-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.order-btn {
  background: none;
  border: none;
  color: #3498db;
  cursor: pointer;
  padding: 0.25rem;
  font-size: 0.75rem;
}

.order-btn:disabled {
  color: #95a5a6;
  cursor: not-allowed;
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

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  border: none;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.75rem;
  color: #2c3e50;
  background-color: #ecf0f1;
  cursor: pointer;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background-color: #bdc3c7;
}

.action-btn.delete:hover {
  background-color: #e74c3c;
  color: white;
}

.action-btn.edit:hover {
  background-color: #2980b9;
  color: white;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
}

.empty-icon {
  font-size: 3rem;
  color: #95a5a6;
  margin-bottom: 1rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem;
  color: #2c3e50;
}

.empty-state p {
  margin: 0 0 1.5rem;
  color: #7f8c8d;
}

.loading, .error-message {
  padding: 2rem;
  text-align: center;
  color: #7f8c8d;
}

.error-message {
  color: #e74c3c;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.delete-modal {
  max-width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #ecf0f1;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #7f8c8d;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.form-group input[type="text"],
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group textarea {
  min-height: 100px;
  resize: vertical;
}

.form-group small {
  display: block;
  margin-top: 0.25rem;
  color: #7f8c8d;
  font-size: 0.75rem;
}

.form-group.checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.form-group.checkbox label {
  margin-bottom: 0;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.warning {
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}
</style> 