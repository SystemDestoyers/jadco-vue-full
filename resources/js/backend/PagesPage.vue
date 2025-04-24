<template>
  <AdminLayout>
    <div class="pages-page">
      <header class="page-header">
        <h1>Manage Pages</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add New Page
        </button>
      </header>
      
      <div class="content-container">
        <div v-if="isLoading" class="loading">
          Loading pages...
        </div>
        
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>
        
        <div v-else class="pages-list">
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="page in pages" :key="page.id">
                <td>{{ page.name }}</td>
                <td>{{ page.slug }}</td>
                <td>
                  <span :class="['status-badge', page.is_active ? 'active' : 'inactive']">
                    {{ page.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>{{ formatDate(page.created_at) }}</td>
                <td class="actions">
                  <router-link :to="`/admin/pages/${page.id}/sections`" class="action-btn">
                    <i class="fas fa-puzzle-piece"></i> Sections
                  </router-link>
                  <button @click="openEditModal(page)" class="action-btn">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button @click="confirmDelete(page)" class="action-btn delete">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
              <tr v-if="pages.length === 0">
                <td colspan="5" class="empty-message">No pages found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>{{ editMode ? 'Edit Page' : 'Create New Page' }}</h2>
            <button @click="closeModal" class="close-btn">&times;</button>
          </div>
          
          <div class="modal-body">
            <form @submit.prevent="savePage">
              <div class="form-group">
                <label for="name">Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  id="name" 
                  placeholder="Page Name" 
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="slug">Slug</label>
                <input 
                  v-model="form.slug" 
                  type="text" 
                  id="slug" 
                  placeholder="page-slug" 
                  required
                />
                <small>The URL-friendly version of the title</small>
              </div>
              
              <div class="form-group">
                <label for="template">Template</label>
                <select 
                  v-model="form.template" 
                  id="template"
                >
                  <option value="default">Default</option>
                  <option value="home">Home</option>
                  <option value="about">About</option>
                  <option value="services">Services</option>
                  <option value="contact">Contact</option>
                  <option value="service-detail">Service Detail</option>
                </select>
                <small>The template layout to use for this page</small>
              </div>
              
              <div class="form-group">
                <label for="meta_title">Meta Title (Optional)</label>
                <input 
                  v-model="form.meta_title" 
                  type="text" 
                  id="meta_title" 
                  placeholder="SEO Meta Title"
                />
              </div>
              
              <div class="form-group">
                <label for="meta_description">Meta Description (Optional)</label>
                <textarea 
                  v-model="form.meta_description" 
                  id="meta_description" 
                  placeholder="SEO Meta Description"
                ></textarea>
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
                  {{ isSubmitting ? 'Saving...' : 'Save Page' }}
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
            <p>Are you sure you want to delete the page "<strong>{{ pageToDelete?.name }}</strong>"?</p>
            <p class="warning">This action cannot be undone. All sections belonging to this page will also be deleted.</p>
            
            <div class="modal-actions">
              <button @click="closeDeleteModal" class="btn btn-secondary">Cancel</button>
              <button @click="deletePage" class="btn btn-danger" :disabled="isDeleting">
                {{ isDeleting ? 'Deleting...' : 'Delete Page' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from './AdminLayout.vue';

const pages = ref([]);
const isLoading = ref(true);
const error = ref(null);
const showModal = ref(false);
const editMode = ref(false);
const isSubmitting = ref(false);
const showDeleteModal = ref(false);
const pageToDelete = ref(null);
const isDeleting = ref(false);

const form = reactive({
  id: null,
  name: '',
  slug: '',
  template: 'default',
  meta_title: '',
  meta_description: '',
  is_active: true
});

const fetchPages = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get('/api/admin/pages');
    pages.value = response.data;
  } catch (err) {
    error.value = 'Failed to load pages. Please try again.';
  } finally {
    isLoading.value = false;
  }
};

const openCreateModal = () => {
  editMode.value = false;
  
  // Reset form
  Object.assign(form, {
    id: null,
    name: '',
    slug: '',
    template: 'default',
    meta_title: '',
    meta_description: '',
    is_active: true
  });
  
  showModal.value = true;
};

const openEditModal = (page) => {
  editMode.value = true;
  
  // Populate form with page data
  Object.assign(form, {
    id: page.id,
    name: page.name,
    slug: page.slug,
    template: page.template || 'default',
    meta_title: page.meta_title || '',
    meta_description: page.meta_description || '',
    is_active: page.is_active
  });
  
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const savePage = async () => {
  isSubmitting.value = true;
  
  try {
    if (editMode.value) {
      // Update existing page
      const response = await axios.put(`/api/admin/pages/${form.id}`, form);
      
      // Update the page in the list
      const index = pages.value.findIndex(p => p.id === form.id);
      if (index !== -1) {
        pages.value[index] = response.data;
      }
    } else {
      // Create new page
      const response = await axios.post('/api/admin/pages', form);
      pages.value.unshift(response.data);
    }
    
    // Close the modal after successful save
    closeModal();
  } catch (err) {
    error.value = 'Failed to save page. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

const confirmDelete = (page) => {
  pageToDelete.value = page;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  pageToDelete.value = null;
};

const deletePage = async () => {
  isDeleting.value = true;
  
  try {
    await axios.delete(`/api/admin/pages/${pageToDelete.value.id}`);
    
    // Remove the page from the list
    const index = pages.value.findIndex(p => p.id === pageToDelete.value.id);
    if (index !== -1) {
      pages.value.splice(index, 1);
    }
    
    // Close the modal after successful delete
    closeDeleteModal();
  } catch (err) {
    error.value = 'Failed to delete page. Please try again.';
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
  fetchPages();
});
</script>

<style scoped>
.pages-page {
  padding: 1rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
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

.pages-list {
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

.empty-message {
  text-align: center;
  color: #7f8c8d;
  padding: 2rem 0;
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