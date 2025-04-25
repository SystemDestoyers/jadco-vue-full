<template>
  <AdminLayout>
    <div class="media-library">
      <header class="page-header">
        <div class="header-left">
          <h1>Media Library</h1>
        </div>
        <div class="header-actions">
          <button 
            @click="showUploadModal = true" 
            class="btn btn-primary"
          >
            <i class="fas fa-upload"></i> Upload New Media
          </button>
        </div>
      </header>
      
      <div class="content-container">
        <div class="media-controls">
          <div class="media-filters">
            <!-- Search input -->
            <div class="search-filter">
              <input 
                type="text" 
                v-model="filters.search" 
                @input="debounceSearch" 
                placeholder="Search media..." 
              />
            </div>
            
            <!-- Collection filter -->
            <div class="collection-filter">
              <select v-model="filters.collection">
                <option value="">All Collections</option>
                <option value="general">General</option>
                <option value="services">Services</option>
                <option value="headers">Headers</option>
                <option value="education">Education</option>
                <option value="training">Training</option>
                <option value="ai">AI</option>
                <option value="gaming">Gaming</option>
                <option value="arts">Arts</option>
              </select>
            </div>
            
            <!-- Folder filter -->
            <div class="folder-filter">
              <select v-model="filters.folder">
                <option value="">All Folders / Root</option>
                <option v-for="folder in folders" :key="folder.path" :value="folder.path">
                  {{ folder.name }} ({{ folder.file_count }})
                </option>
              </select>
            </div>
            
            <!-- Type filter -->
            <div class="type-filter">
              <select v-model="filters.type">
                <option value="">All Types</option>
                <option value="image">Images</option>
              </select>
            </div>
          </div>
          
          <div class="media-actions">
            <!-- Upload button -->
            <button @click="showUploadModal = true" class="btn btn-primary">
              <span>Upload Media</span>
            </button>
            
            <!-- Create folder button -->
            <button @click="openCreateFolderModal" class="btn btn-secondary">
              <span>Create Folder</span>
            </button>
            
            <!-- Delete folder button -->
            <button 
              @click="openDeleteFolderModal" 
              class="btn btn-danger"
              :disabled="!canDeleteCurrentFolder"
              v-if="filters.folder"
            >
              <span>Delete Folder</span>
            </button>
            
            <!-- View toggle -->
            <div class="view-toggle">
              <button 
                @click="displayView = 'grid'"
                :class="{ active: displayView === 'grid' }"
                title="Grid View"
              >
                <span>Grid</span>
              </button>
              <button 
                @click="displayView = 'list'"
                :class="{ active: displayView === 'list' }"
                title="List View"
              >
                <span>List</span>
              </button>
            </div>
          </div>
        </div>
        
        <div class="loading-container" v-if="isLoading">
          <div class="spinner"></div>
          <p>Loading media items...</p>
        </div>
        
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>
        
        <div v-else-if="mediaItems.length === 0" class="empty-state">
          <i class="fas fa-photo-video"></i>
          <h3>No media found</h3>
          <p>Upload new media or try different filters</p>
        </div>
        
        <div v-else>
          <!-- Grid View -->
          <div v-if="displayView === 'grid'" class="media-grid">
            <div 
              v-for="item in mediaItems" 
              :key="item.id" 
              class="media-card"
              @click="openMediaDetail(item)"
            >
              <div class="media-thumbnail">
                <img 
                  v-if="item.mime_type && item.mime_type.startsWith('image/')" 
                  :src="item.url" 
                  :alt="item.alt_text || item.original_filename"
                />
                <div v-else class="file-icon">
                  <i 
                    :class="getFileIcon(item.mime_type)"
                  ></i>
                </div>
              </div>
              <div class="media-info">
                <h4 class="truncate">{{ item.original_filename }}</h4>
                <span class="media-type">{{ formatFileType(item.mime_type) }}</span>
              </div>
            </div>
          </div>
          
          <!-- List View -->
          <div v-else class="media-list">
            <table>
              <thead>
                <tr>
                  <th style="width: 60px"></th>
                  <th>Filename</th>
                  <th>Type</th>
                  <th>Collection</th>
                  <th>Size</th>
                  <th>Date Added</th>
                  <th style="width: 100px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="item in mediaItems" 
                  :key="item.id"
                  @click="openMediaDetail(item)"
                >
                  <td>
                    <div class="list-thumbnail">
                      <img 
                        v-if="item.mime_type && item.mime_type.startsWith('image/')" 
                        :src="item.url" 
                        :alt="item.alt_text || item.original_filename"
                      />
                      <div v-else class="file-icon-small">
                        <i :class="getFileIcon(item.mime_type)"></i>
                      </div>
                    </div>
                  </td>
                  <td class="filename-cell">{{ item.original_filename }}</td>
                  <td>{{ formatFileType(item.mime_type) }}</td>
                  <td>{{ item.collection }}</td>
                  <td>{{ formatFileSize(item.size) }}</td>
                  <td>{{ formatDate(item.created_at) }}</td>
                  <td>
                    <div class="action-buttons">
                      <button 
                        class="btn btn-sm btn-secondary" 
                        @click.stop="openMediaDetail(item)"
                        title="Edit"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button 
                        class="btn btn-sm btn-danger" 
                        @click.stop="confirmDelete(item)"
                        title="Delete"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="pagination" v-if="pagination.last_page > 1">
            <button 
              :disabled="pagination.current_page === 1" 
              @click="changePage(pagination.current_page - 1)"
              class="btn btn-sm btn-secondary"
            >
              <i class="fas fa-chevron-left"></i>
            </button>
            
            <button 
              v-for="page in paginationPages" 
              :key="page" 
              :class="['btn', 'btn-sm', pagination.current_page === page ? 'btn-primary' : 'btn-secondary']"
              @click="changePage(page)"
            >
              {{ page }}
            </button>
            
            <button 
              :disabled="pagination.current_page === pagination.last_page" 
              @click="changePage(pagination.current_page + 1)"
              class="btn btn-sm btn-secondary"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Media Detail Modal -->
      <div v-if="showDetailModal" class="modal">
        <div class="modal-content media-detail-modal">
          <div class="modal-header">
            <h3>Media Details</h3>
            <button class="close-btn" @click="showDetailModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <div class="media-detail-container">
              <div class="media-preview-section">
                <div class="media-preview">
                  <img 
                    v-if="selectedMedia.mime_type && selectedMedia.mime_type.startsWith('image/')" 
                    :src="selectedMedia.url" 
                    :alt="selectedMedia.alt_text || selectedMedia.original_filename"
                  />
                  <div v-else class="file-preview-icon">
                    <i :class="getFileIcon(selectedMedia.mime_type)"></i>
                    <span>{{ selectedMedia.original_filename }}</span>
                  </div>
                </div>
                
                <div class="media-info-list">
                  <div class="info-item">
                    <span class="label">Type:</span>
                    <span class="value">{{ formatFileType(selectedMedia.mime_type) }}</span>
                  </div>
                  
                  <div class="info-item">
                    <span class="label">Size:</span>
                    <span class="value">{{ formatFileSize(selectedMedia.size) }}</span>
                  </div>
                  
                  <div class="info-item" v-if="selectedMedia.metadata && selectedMedia.metadata.dimensions">
                    <span class="label">Dimensions:</span>
                    <span class="value">{{ selectedMedia.metadata.dimensions.width }} x {{ selectedMedia.metadata.dimensions.height }}</span>
                  </div>
                  
                  <div class="info-item">
                    <span class="label">Path:</span>
                    <div class="copyable-field">
                      <span class="value path-value">{{ selectedMedia.path }}</span>
                      <button @click="copyToClipboard(selectedMedia.path)" class="copy-btn" title="Copy to clipboard">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="info-item">
                    <span class="label">URL:</span>
                    <div class="copyable-field">
                      <span class="value url-value">
                        <a :href="selectedMedia.url" target="_blank" class="url-link">{{ selectedMedia.url }}</a>
                      </span>
                      <button @click="copyToClipboard(selectedMedia.url)" class="copy-btn" title="Copy to clipboard">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="info-item">
                    <span class="label">Uploaded:</span>
                    <span class="value">{{ formatDate(selectedMedia.created_at) }}</span>
                  </div>
                </div>
              </div>
              
              <div class="media-form">
                <div class="form-group">
                  <label for="filename">Filename</label>
                  <input 
                    type="text" 
                    id="filename" 
                    v-model="selectedMedia.original_filename" 
                    readonly
                  />
                </div>
                
                <div class="form-group">
                  <label for="alt-text">Alt Text</label>
                  <input 
                    type="text" 
                    id="alt-text" 
                    v-model="selectedMedia.alt_text"
                  />
                </div>
                
                <div class="form-group">
                  <label for="caption">Caption</label>
                  <textarea 
                    id="caption" 
                    v-model="selectedMedia.caption"
                    rows="3"
                  ></textarea>
                </div>
                
                <div class="form-group">
                  <label for="collection">Collection</label>
                  <select id="collection" v-model="selectedMedia.collection">
                    <option value="general">General</option>
                    <option value="services">Services</option>
                    <option value="headers">Headers</option>
                    <option value="education">Education</option>
                    <option value="training">Training</option>
                    <option value="ai">AI</option>
                    <option value="gaming">Gaming</option>
                    <option value="arts">Arts</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="folder">Folder</label>
                  <select id="folder" v-model="selectedMedia.metadata.folder">
                    <option value="">Root Folder</option>
                    <option v-for="folder in folders" :key="folder.path" :value="folder.path">
                      {{ folder.name }}
                    </option>
                  </select>
                </div>
                
                <div class="form-buttons">
                  <button 
                    @click="updateMedia" 
                    class="btn btn-primary"
                    :disabled="isUpdating"
                  >
                    {{ isUpdating ? 'Saving...' : 'Save Changes' }}
                  </button>
                  <button 
                    @click="deleteMedia" 
                    class="btn btn-danger"
                  >
                    Delete Media
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Upload Modal -->
      <div v-if="showUploadModal" class="modal">
        <div class="modal-content upload-modal">
          <div class="modal-header">
            <h3>Upload Media</h3>
            <button class="close-btn" @click="showUploadModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <div 
              class="upload-dropzone"
              :class="{ 'is-dragging': isDragging }"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleFileDrop"
              @click="triggerFileInput"
            >
              <input 
                type="file" 
                ref="fileInput" 
                @change="handleFileSelect" 
                style="display: none"
              />
              <i class="fas fa-cloud-upload-alt"></i>
              <h4>Drop files here or click to upload</h4>
              <p>Supported formats: JPG, PNG, GIF, PDF, DOCX, XLSX</p>
            </div>
            
            <div v-if="uploadFile" class="file-preview-container">
              <div class="file-preview">
                <img 
                  v-if="uploadFile.type.startsWith('image/')" 
                  :src="uploadPreview" 
                  alt="Preview"
                />
                <div v-else class="file-preview-icon">
                  <i :class="getFileIcon(uploadFile.type)"></i>
                  <span>{{ uploadFile.name }}</span>
                </div>
              </div>
              
              <div class="upload-form">
                <div class="form-group">
                  <label for="upload-alt-text">Alt Text</label>
                  <input 
                    type="text" 
                    id="upload-alt-text" 
                    v-model="uploadData.alt_text"
                  />
                </div>
                
                <div class="form-group">
                  <label for="upload-caption">Caption</label>
                  <textarea 
                    id="upload-caption" 
                    v-model="uploadData.caption"
                    rows="3"
                  ></textarea>
                </div>
                
                <div class="form-group">
                  <label for="upload-collection">Collection</label>
                  <select id="upload-collection" v-model="uploadData.collection">
                    <option value="general">General</option>
                    <option value="services">Services</option>
                    <option value="headers">Headers</option>
                    <option value="education">Education</option>
                    <option value="training">Training</option>
                    <option value="ai">AI</option>
                    <option value="gaming">Gaming</option>
                    <option value="arts">Arts</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="upload-folder">Folder</label>
                  <select id="upload-folder" v-model="uploadData.folder">
                    <option value="">Root Folder</option>
                    <option v-for="folder in folders" :key="folder.path" :value="folder.path">
                      {{ folder.name }}
                    </option>
                  </select>
                </div>
                
                <div class="form-buttons">
                  <button 
                    @click="uploadMedia" 
                    class="btn btn-primary"
                    :disabled="isUploading"
                  >
                    {{ isUploading ? 'Uploading...' : 'Upload' }}
                  </button>
                  <button 
                    @click="cancelUpload" 
                    class="btn btn-secondary"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteConfirmModal" class="modal">
        <div class="modal-content delete-confirm-modal">
          <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="close-btn" @click="showDeleteConfirmModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong>{{ selectedMedia.original_filename }}</strong>?</p>
            <p class="warning">This action cannot be undone.</p>
            
            <div class="form-buttons">
              <button 
                @click="confirmDeleteAction" 
                class="btn btn-danger"
                :disabled="isDeleting"
              >
                {{ isDeleting ? 'Deleting...' : 'Delete' }}
              </button>
              <button 
                @click="showDeleteConfirmModal = false" 
                class="btn btn-secondary"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Create Folder Modal -->
      <div v-if="showCreateFolderModal" class="modal">
        <div class="modal-content create-folder-modal">
          <div class="modal-header">
            <h3>Create New Folder</h3>
            <button class="close-btn" @click="showCreateFolderModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="folder-name">Folder Name</label>
              <input 
                type="text" 
                id="folder-name" 
                v-model="newFolderName"
              />
            </div>
            
            <div class="form-buttons">
              <button 
                @click="createFolder" 
                class="btn btn-primary"
                :disabled="isCreatingFolder"
              >
                {{ isCreatingFolder ? 'Creating...' : 'Create' }}
              </button>
              <button 
                @click="showCreateFolderModal = false" 
                class="btn btn-secondary"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Delete Folder Modal -->
      <div v-if="showDeleteFolderModal" class="modal">
        <div class="modal-content delete-folder-modal">
          <div class="modal-header">
            <h3>Delete Folder</h3>
            <button class="close-btn" @click="showDeleteFolderModal = false">&times;</button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the folder <strong>{{ getCurrentFolderName() }}</strong>?</p>
            <p class="warning">This action cannot be undone.</p>
            <p class="info">Note: You can only delete empty folders with no subfolders.</p>
            
            <div class="form-buttons">
              <button 
                @click="deleteFolder" 
                class="btn btn-danger"
                :disabled="isDeletingFolder"
              >
                {{ isDeletingFolder ? 'Deleting...' : 'Delete Folder' }}
              </button>
              <button 
                @click="showDeleteFolderModal = false" 
                class="btn btn-secondary"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { defineComponent, ref, reactive, computed, onMounted, watch } from 'vue';
import AdminLayout from './AdminLayout.vue';
import axios from 'axios';

export default defineComponent({
  name: 'MediaLibrary',
  components: {
    AdminLayout
  },
  setup() {
    const isLoading = ref(false);
    const error = ref(null);
    const mediaItems = ref([]);
    const displayView = ref('grid');
    const showDetailModal = ref(false);
    const showUploadModal = ref(false);
    const showDeleteConfirmModal = ref(false);
    const showCreateFolderModal = ref(false);
    const showDeleteFolderModal = ref(false);
    const selectedMedia = ref({});
    const isUpdating = ref(false);
    const isUploading = ref(false);
    const isDeleting = ref(false);
    const isCreatingFolder = ref(false);
    const isDeletingFolder = ref(false);
    const isDragging = ref(false);
    const fileInput = ref(null);
    const uploadFile = ref(null);
    const uploadPreview = ref('');
    const folders = ref([]);
    const newFolderName = ref('');
    const uploadData = reactive({
      alt_text: '',
      caption: '',
      collection: 'general',
      folder: '',
    });
    
    // Pagination
    const pagination = ref({
      current_page: 1,
      per_page: 24,
      total: 0,
      last_page: 1
    });
    
    // Filters
    const filters = reactive({
      search: '',
      collection: '',
      type: '',
      folder: '',
    });
    
    // Computed pagination pages
    const paginationPages = computed(() => {
      const pages = [];
      const currentPage = pagination.value.current_page;
      const lastPage = pagination.value.last_page;
      
      // Always show the first page
      if (lastPage > 0) {
        pages.push(1);
      }
      
      // Calculate the range of pages to show
      let startPage = Math.max(2, currentPage - 1);
      let endPage = Math.min(lastPage - 1, currentPage + 1);
      
      // If we're at the start, show more pages after
      if (currentPage <= 2) {
        endPage = Math.min(lastPage - 1, 4);
      }
      
      // If we're at the end, show more pages before
      if (currentPage >= lastPage - 1) {
        startPage = Math.max(2, lastPage - 3);
      }
      
      // Add ellipsis before the range if needed
      if (startPage > 2) {
        pages.push('...');
      }
      
      // Add the range of pages
      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      
      // Add ellipsis after the range if needed
      if (endPage < lastPage - 1) {
        pages.push('...');
      }
      
      // Always show the last page if there is more than one page
      if (lastPage > 1) {
        pages.push(lastPage);
      }
      
      return pages;
    });
    
    // Fetch media items
    const fetchMedia = async () => {
      isLoading.value = true;
      error.value = null;
      
      try {
        const params = {
          page: pagination.value.current_page,
          per_page: pagination.value.per_page,
          ...filters
        };
        
        const response = await axios.get('/api/admin/media', { params });
        mediaItems.value = response.data.data.map(item => {
          // Ensure path starts with a forward slash
          if (item.path && !item.path.startsWith('/')) {
            item.path = '/' + item.path;
          }
          
          // Ensure URL starts with a forward slash
          if (item.url && !item.url.startsWith('http') && !item.url.startsWith('/')) {
            item.url = '/' + item.url;
          }
          
          return item;
        });
        
        // Update pagination
        pagination.value = {
          current_page: response.data.current_page,
          per_page: response.data.per_page,
          total: response.data.total,
          last_page: response.data.last_page
        };
      } catch (err) {
        error.value = 'Failed to fetch media. Please try again.';
        console.error(err);
      } finally {
        isLoading.value = false;
      }
    };
    
    // Change page
    const changePage = (page) => {
      if (page === '...') return;
      pagination.value.current_page = page;
      fetchMedia();
    };
    
    // Debounce search
    let searchTimeout;
    const debounceSearch = () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        pagination.value.current_page = 1;
        fetchMedia();
      }, 300);
    };
    
    // Watch for filter changes
    const watchFilters = () => {
      const propsToWatch = ['collection', 'type', 'folder'];
      
      propsToWatch.forEach(prop => {
        watch(() => filters[prop], () => {
          pagination.value.current_page = 1;
          fetchMedia();
        });
      });
    };
    
    // Format file size
    const formatFileSize = (bytes) => {
      if (!bytes) return '0 B';
      
      const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(1024));
      
      return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
    };
    
    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };
    
    // Format file type
    const formatFileType = (mimeType) => {
      if (!mimeType) return 'Unknown';
      
      if (mimeType.startsWith('image/')) {
        return mimeType.replace('image/', '').toUpperCase();
      }
      
      if (mimeType.startsWith('video/')) {
        return mimeType.replace('video/', '').toUpperCase();
      }
      
      if (mimeType === 'application/pdf') {
        return 'PDF';
      }
      
      if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) {
        return 'Excel';
      }
      
      if (mimeType.includes('document') || mimeType.includes('word')) {
        return 'Word';
      }
      
      return mimeType.split('/').pop().toUpperCase();
    };
    
    // Get file icon
    const getFileIcon = (mimeType) => {
      if (!mimeType) return 'fas fa-file';
      
      if (mimeType.startsWith('image/')) {
        return 'fas fa-file-image';
      }
      
      if (mimeType.startsWith('video/')) {
        return 'fas fa-file-video';
      }
      
      if (mimeType === 'application/pdf') {
        return 'fas fa-file-pdf';
      }
      
      if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) {
        return 'fas fa-file-excel';
      }
      
      if (mimeType.includes('document') || mimeType.includes('word')) {
        return 'fas fa-file-word';
      }
      
      return 'fas fa-file';
    };
    
    // Open media detail
    const openMediaDetail = (media) => {
      selectedMedia.value = { ...media };
      showDetailModal.value = true;
    };
    
    // Update media
    const updateMedia = async () => {
      isUpdating.value = true;
      
      try {
        const response = await axios.put(`/api/admin/media/${selectedMedia.value.id}`, {
          alt_text: selectedMedia.value.alt_text,
          caption: selectedMedia.value.caption,
          collection: selectedMedia.value.collection,
          folder: selectedMedia.value.metadata?.folder || '',
        });
        
        // Update the media item in the list
        const index = mediaItems.value.findIndex(item => item.id === selectedMedia.value.id);
        if (index !== -1) {
          mediaItems.value[index] = response.data;
        }
        
        // Show success notification
        if (window.$notifications) {
          window.$notifications.success('Media updated successfully');
        }
        
        // Close the modal
        showDetailModal.value = false;
      } catch (err) {
        error.value = 'Failed to update media. Please try again.';
        console.error(err);
        
        // Show error notification
        if (window.$notifications) {
          window.$notifications.error('Failed to update media');
        }
      } finally {
        isUpdating.value = false;
      }
    };
    
    // Confirm delete
    const confirmDelete = (media) => {
      selectedMedia.value = { ...media };
      showDeleteConfirmModal.value = true;
    };
    
    // Delete media
    const deleteMedia = () => {
      showDetailModal.value = false;
      confirmDelete(selectedMedia.value);
    };
    
    // Confirm delete action
    const confirmDeleteAction = async () => {
      isDeleting.value = true;
      
      try {
        await axios.delete(`/api/admin/media/${selectedMedia.value.id}`);
        
        // Remove the media item from the list
        mediaItems.value = mediaItems.value.filter(item => item.id !== selectedMedia.value.id);
        
        // Show success notification
        if (window.$notifications) {
          window.$notifications.success('Media deleted successfully');
        }
        
        // Close the modal
        showDeleteConfirmModal.value = false;
      } catch (err) {
        error.value = 'Failed to delete media. Please try again.';
        console.error(err);
        
        // Show error notification
        if (window.$notifications) {
          window.$notifications.error('Failed to delete media');
        }
      } finally {
        isDeleting.value = false;
      }
    };
    
    // Handle file drop
    const handleFileDrop = (event) => {
      isDragging.value = false;
      const files = event.dataTransfer.files;
      
      if (files.length > 0) {
        handleFile(files[0]);
      }
    };
    
    // Trigger file input
    const triggerFileInput = () => {
      fileInput.value.click();
    };
    
    // Handle file select
    const handleFileSelect = (event) => {
      const files = event.target.files;
      
      if (files.length > 0) {
        handleFile(files[0]);
      }
    };
    
    // Handle file
    const handleFile = (file) => {
      // Check file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        error.value = 'File is too large. Maximum size is 10MB.';
        
        if (window.$notifications) {
          window.$notifications.error('File is too large. Maximum size is 10MB.');
        }
        
        return;
      }
      
      uploadFile.value = file;
      
      // Generate preview for images
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          uploadPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        uploadPreview.value = '';
      }
      
      // Auto-populate alt text
      uploadData.alt_text = file.name.split('.').slice(0, -1).join(' ');
    };
    
    // Cancel upload
    const cancelUpload = () => {
      uploadFile.value = null;
      uploadPreview.value = '';
      uploadData.alt_text = '';
      uploadData.caption = '';
      uploadData.collection = 'general';
      uploadData.folder = '';
    };
    
    // Upload media
    const uploadMedia = async () => {
      if (!uploadFile.value) return;
      
      isUploading.value = true;
      
      const formData = new FormData();
      formData.append('file', uploadFile.value);
      formData.append('alt_text', uploadData.alt_text);
      formData.append('caption', uploadData.caption);
      formData.append('collection', uploadData.collection);
      formData.append('folder', uploadData.folder);
      
      try {
        const response = await axios.post('/api/admin/media', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        // Add the new media item to the list
        mediaItems.value.unshift(response.data);
        
        // Show success notification
        if (window.$notifications) {
          window.$notifications.success('Media uploaded successfully');
        }
        
        // Close the modal and reset
        showUploadModal.value = false;
        cancelUpload();
        
        // Refresh the list to ensure proper sorting
        fetchMedia();
      } catch (err) {
        error.value = 'Failed to upload media. Please try again.';
        console.error(err);
        
        // Show error notification
        if (window.$notifications) {
          window.$notifications.error('Failed to upload media');
        }
      } finally {
        isUploading.value = false;
      }
    };
    
    // Fetch folders
    const fetchFolders = async () => {
      try {
        const response = await axios.get('/api/admin/media/folders/list');
        folders.value = response.data;
      } catch (err) {
        console.error('Failed to fetch folders:', err);
      }
    };

    // Create a new folder
    const createFolder = async () => {
      if (!newFolderName.value.trim()) return;
      
      isCreatingFolder.value = true;
      
      try {
        await axios.post('/api/admin/media/folders', {
          folder: newFolderName.value.trim()
        });
        
        // Reset and close modal
        newFolderName.value = '';
        showCreateFolderModal.value = false;
        
        // Refresh folders
        fetchFolders();
        
        // Show success notification
        if (window.$notifications) {
          window.$notifications.success('Folder created successfully');
        }
      } catch (err) {
        console.error('Failed to create folder:', err);
        
        // Show error notification
        if (window.$notifications) {
          window.$notifications.error('Failed to create folder');
        }
      } finally {
        isCreatingFolder.value = false;
      }
    };

    // Open create folder modal
    const openCreateFolderModal = () => {
      newFolderName.value = '';
      showCreateFolderModal.value = true;
    };
    
    // Check if current folder can be deleted (empty)
    const canDeleteCurrentFolder = computed(() => {
      if (!filters.folder) return false;
      
      const folder = folders.value.find(f => f.path === filters.folder);
      return folder && folder.file_count === 0;
    });
    
    // Get the name of the current folder
    const getCurrentFolderName = () => {
      if (!filters.folder) return '';
      
      const folder = folders.value.find(f => f.path === filters.folder);
      return folder ? folder.name : filters.folder;
    };
    
    // Open delete folder modal
    const openDeleteFolderModal = () => {
      if (!canDeleteCurrentFolder.value) return;
      showDeleteFolderModal.value = true;
    };
    
    // Delete the current folder
    const deleteFolder = async () => {
      if (!filters.folder) return;
      
      isDeletingFolder.value = true;
      
      try {
        await axios.delete('/api/admin/media/folders/delete', {
          data: { folder: filters.folder }
        });
        
        // Reset folder filter
        filters.folder = '';
        
        // Refresh folders
        fetchFolders();
        
        // Close modal
        showDeleteFolderModal.value = false;
        
        // Show success notification
        if (window.$notifications) {
          window.$notifications.success('Folder deleted successfully');
        }
      } catch (err) {
        console.error('Failed to delete folder:', err);
        
        let errorMessage = 'Failed to delete folder';
        
        if (err.response) {
          if (err.response.data.has_images) {
            errorMessage = 'Cannot delete folder because it contains images';
          } else if (err.response.data.has_subfolders) {
            errorMessage = 'Cannot delete folder because it contains subfolders';
          } else if (err.response.data.error) {
            errorMessage = err.response.data.error;
          }
        }
        
        // Show error notification
        if (window.$notifications) {
          window.$notifications.error(errorMessage);
        }
      } finally {
        isDeletingFolder.value = false;
      }
    };
    
    // Copy text to clipboard
    const copyToClipboard = (text) => {
      navigator.clipboard.writeText(text)
        .then(() => {
          // Show success notification
          if (window.$notifications) {
            window.$notifications.success('Copied to clipboard');
          }
        })
        .catch(err => {
          console.error('Failed to copy: ', err);
          // Show error notification
          if (window.$notifications) {
            window.$notifications.error('Failed to copy to clipboard');
          }
        });
    };
    
    // Initialize
    onMounted(() => {
      // Load external CSS file
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = '/backend/css/media-library.css';
      document.head.appendChild(link);
      
      fetchMedia();
      fetchFolders();
      watchFilters();
    });
    
    return {
      isLoading,
      error,
      mediaItems,
      displayView,
      filters,
      pagination,
      paginationPages,
      showDetailModal,
      showUploadModal,
      showDeleteConfirmModal,
      showCreateFolderModal,
      showDeleteFolderModal,
      selectedMedia,
      isUpdating,
      isUploading,
      isDeleting,
      isCreatingFolder,
      isDeletingFolder,
      isDragging,
      fileInput,
      uploadFile,
      uploadPreview,
      uploadData,
      folders,
      newFolderName,
      canDeleteCurrentFolder,
      fetchMedia,
      changePage,
      debounceSearch,
      formatFileSize,
      formatDate,
      formatFileType,
      getFileIcon,
      openMediaDetail,
      updateMedia,
      deleteMedia,
      confirmDelete,
      confirmDeleteAction,
      handleFileDrop,
      triggerFileInput,
      handleFileSelect,
      cancelUpload,
      uploadMedia,
      fetchFolders,
      createFolder,
      openCreateFolderModal,
      openDeleteFolderModal,
      getCurrentFolderName,
      deleteFolder,
      copyToClipboard
    };
  }
});
</script> 