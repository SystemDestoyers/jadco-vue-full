<template>
  <div class="media-selector">
    <!-- Controls -->
    <div class="media-controls">
      <div class="search-bar">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Search media..." 
          class="search-input"
          @input="filterMedia"
        />
      </div>
      
      <div class="filter-options">
        <select v-model="mediaType" class="filter-select" @change="filterMedia">
          <option value="all">All Media</option>
          <option value="image">Images</option>
          <option value="video">Videos</option>
          <option value="document">Documents</option>
        </select>
      </div>
      
      <div class="upload-btn-wrapper">
        <button class="upload-btn" @click="showUploadModal = true">
          <i class="fas fa-upload"></i> Upload
        </button>
      </div>
      
      <div class="debug-toggle">
        <button 
          class="debug-btn" 
          @click="debugMode = !debugMode" 
          :class="{ 'active': debugMode }"
        >
          <i class="fas fa-bug"></i>
        </button>
      </div>
    </div>
    
    <!-- Media items grid -->
    <div class="media-loading" v-if="isLoading">
      <div class="spinner"></div>
      <p>Loading media...</p>
    </div>
    
    <div class="media-error" v-else-if="error">
      <p>{{ error }}</p>
      <button class="btn btn-primary" @click="fetchMedia">Retry</button>
    </div>
    
    <div class="empty-state" v-else-if="filteredMedia.length === 0">
      <div class="empty-icon">
        <i class="fas fa-photo-video"></i>
      </div>
      <p>No media found</p>
      <button class="btn btn-primary" @click="showUploadModal = true">Upload Media</button>
    </div>
    
    <div class="media-grid" v-else>
      <div 
        v-for="item in filteredMedia" 
        :key="item.id" 
        class="media-item"
        :class="{ 'selected': selectedMedia && selectedMedia.id === item.id }"
        @click="selectMedia(item)"
      >
        <!-- Thumbnail preview -->
        <div class="media-thumbnail">
          <img 
            v-if="item.type === 'image'" 
            :src="item.thumbnail || item.url" 
            :alt="item.name"
            @error="handleImageError($event, item)" 
            @load="handleImageLoad($event, item)"
          />
          <div v-else-if="item.type === 'video'" class="video-thumbnail">
            <i class="fas fa-play"></i>
          </div>
          <div v-else class="document-thumbnail">
            <i class="fas fa-file"></i>
          </div>
          
          <!-- Debug info -->
          <div v-if="debugMode" class="debug-info">
            <small>URL: {{ item.thumbnail || item.url }}</small>
            <small>Type: {{ item.type || 'unknown' }}</small>
          </div>
        </div>
        
        <!-- Media info -->
        <div class="media-info">
          <div class="media-name" :title="item.name">{{ item.name }}</div>
          <div class="media-meta">
            <span class="media-type">{{ formatFileType(item.mime_type) }}</span>
            <span class="media-size">{{ formatFileSize(item.size) }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Pagination -->
    <div class="media-pagination" v-if="totalPages > 1">
      <button 
        class="pagination-btn" 
        :class="{ 'disabled': currentPage === 1 }"
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        <i class="fas fa-chevron-left"></i>
      </button>
      
      <div class="pagination-info">
        Page {{ currentPage }} of {{ totalPages }}
      </div>
      
      <button 
        class="pagination-btn" 
        :class="{ 'disabled': currentPage === totalPages }"
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
    
    <!-- Selected media preview and actions -->
    <div class="selected-media-panel" v-if="selectedMedia">
      <div class="selected-media-preview">
        <img v-if="selectedMedia.type === 'image'" :src="selectedMedia.url" :alt="selectedMedia.name" />
        <div v-else-if="selectedMedia.type === 'video'" class="video-preview">
          <i class="fas fa-film"></i>
          <span>Video Preview</span>
        </div>
        <div v-else class="document-preview">
          <i class="fas fa-file-alt"></i>
          <span>Document Preview</span>
        </div>
      </div>
      
      <div class="selected-media-details">
        <h3>{{ selectedMedia.name }}</h3>
        <div class="media-details-row">
          <span class="detail-label">Type:</span>
          <span class="detail-value">{{ formatFileType(selectedMedia.mime_type) }}</span>
        </div>
        <div class="media-details-row">
          <span class="detail-label">Size:</span>
          <span class="detail-value">{{ formatFileSize(selectedMedia.size) }}</span>
        </div>
        <div class="media-details-row">
          <span class="detail-label">Dimensions:</span>
          <span class="detail-value">
            {{ selectedMedia.dimensions ? selectedMedia.dimensions : 'N/A' }}
          </span>
        </div>
        <div class="media-details-row">
          <span class="detail-label">Uploaded:</span>
          <span class="detail-value">{{ formatDate(selectedMedia.created_at) }}</span>
        </div>
        
        <div class="media-actions">
          <button class="btn btn-primary" @click="confirmSelection">
            Select
          </button>
          <button class="btn btn-secondary" @click="selectedMedia = null">
            Cancel
          </button>
        </div>
      </div>
    </div>
    
    <!-- Upload Modal -->
    <div class="modal" v-if="showUploadModal">
      <div class="modal-content upload-modal">
        <div class="modal-header">
          <h3>Upload Media</h3>
          <button class="close-btn" @click="showUploadModal = false">&times;</button>
        </div>
        <div class="modal-body">
          <div class="upload-area" 
            :class="{ 'dragging': isDragging }"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleFileDrop"
          >
            <div class="upload-message" v-if="!uploadFiles.length">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Drop files here or click to select</p>
              <input type="file" ref="fileInput" @change="handleFileSelect" multiple class="file-input" />
              <button class="btn btn-primary" @click="triggerFileInput">Browse Files</button>
            </div>
            
            <div class="upload-files" v-else>
              <div v-for="(file, index) in uploadFiles" :key="index" class="upload-file-item">
                <div class="upload-file-info">
                  <div class="file-name">{{ file.name }}</div>
                  <div class="file-size">{{ formatFileSize(file.size) }}</div>
                </div>
                <div class="upload-progress" v-if="file.uploading">
                  <div class="progress-bar" :style="{ width: file.progress + '%' }"></div>
                </div>
                <div class="upload-status" v-if="file.uploaded">
                  <i class="fas fa-check"></i>
                </div>
                <div class="upload-status" v-if="file.error">
                  <i class="fas fa-times"></i>
                </div>
                <button class="btn-icon remove-file" @click="removeFile(index)" v-if="!file.uploading">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
              
              <div class="upload-actions">
                <button class="btn btn-primary" @click="uploadAllFiles" :disabled="isUploading">
                  {{ isUploading ? 'Uploading...' : 'Upload All' }}
                </button>
                <button class="btn btn-secondary" @click="resetUpload" :disabled="isUploading">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default defineComponent({
  name: 'MediaSelector',
  
  emits: ['select'],
  
  setup(props, { emit }) {
    // Media items state
    const mediaItems = ref([]);
    const isLoading = ref(true);
    const error = ref(null);
    
    // Selected media
    const selectedMedia = ref(null);
    
    // Filters
    const searchQuery = ref('');
    const mediaType = ref('all');
    
    // Pagination
    const currentPage = ref(1);
    const totalPages = ref(1);
    const perPage = ref(16);
    
    // Upload state
    const showUploadModal = ref(false);
    const isDragging = ref(false);
    const uploadFiles = ref([]);
    const isUploading = ref(false);
    const fileInput = ref(null);
    
    // Debug mode for troubleshooting
    const debugMode = ref(false);
    
    // Fetch media from server
    const fetchMedia = async (page = 1) => {
      isLoading.value = true;
      error.value = null;
      
      try {
        const response = await axios.get('/api/admin/media', {
          params: {
            page: page,
            per_page: perPage.value,
            type: mediaType.value !== 'all' ? mediaType.value : null,
            search: searchQuery.value || null
          }
        });
        
        // console.log('Media API response:', response.data);
        
        // Ensure media items have proper URL paths
        const items = response.data.data;
        mediaItems.value = items.map(item => {
          // Make sure we have absolute URLs for images
          if (item.url && !item.url.startsWith('http') && !item.url.startsWith('/')) {
            item.url = '/' + item.url;
          }
          if (item.thumbnail && !item.thumbnail.startsWith('http') && !item.thumbnail.startsWith('/')) {
            item.thumbnail = '/' + item.thumbnail;
          }
          
          // If no type is specified, try to determine it from mime_type
          if (!item.type && item.mime_type) {
            item.type = getFileType(item.mime_type);
          }
          
          // Ensure path property is set
          if (!item.path && item.url) {
            // Store the original file path without domain and leading slash
            let path = item.url;
            
            // Handle full URLs
            if (path.startsWith('http')) {
              try {
                const urlObj = new URL(path);
                path = urlObj.pathname;
              } catch (e) {
                console.error('Error parsing URL:', e);
              }
            }
            
            // Remove leading slash if present
            if (path.startsWith('/')) {
              path = path.substring(1);
            }
            
            item.path = path;
          }
          
          return item;
        });
        
        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
      } catch (err) {
        console.error('Failed to fetch media', err);
        error.value = 'Failed to load media. Please try again.';
      } finally {
        isLoading.value = false;
      }
    };
    
    // Computed property for filtered media
    const filteredMedia = computed(() => {
      return mediaItems.value;
    });
    
    // Filter media based on search query and type
    const filterMedia = () => {
      currentPage.value = 1;
      fetchMedia(1);
    };
    
    // Change pagination page
    const changePage = (page) => {
      if (page < 1 || page > totalPages.value) return;
      fetchMedia(page);
    };
    
    // Select a media item
    const selectMedia = (media) => {
      selectedMedia.value = media;
    };
    
    // Confirm the selection and emit to parent
    const confirmSelection = () => {
      if (selectedMedia.value) {
        // Ensure path is available in the media object
        if (!selectedMedia.value.path && selectedMedia.value.url) {
          // Extract path from URL if not explicitly provided
          const url = selectedMedia.value.url;
          
          // Remove domain if present (handle both absolute and relative URLs)
          let path = url;
          if (url.startsWith('http')) {
            // Extract path portion from full URL
            try {
              const urlObj = new URL(url);
              path = urlObj.pathname;
            } catch (e) {
              console.error('Error parsing URL:', e);
            }
          }
          
          // Remove leading slash if present
          if (path.startsWith('/')) {
            path = path.substring(1);
          }
          
          // Set the path property
          selectedMedia.value.path = path;
        }
        
        emit('select', selectedMedia.value);
      }
    };
    
    // File upload methods
    const triggerFileInput = () => {
      fileInput.value.click();
    };
    
    const handleFileSelect = (event) => {
      const files = Array.from(event.target.files);
      addFilesToUpload(files);
    };
    
    const handleFileDrop = (event) => {
      isDragging.value = false;
      const files = Array.from(event.dataTransfer.files);
      addFilesToUpload(files);
    };
    
    const addFilesToUpload = (files) => {
      const newFiles = files.map(file => ({
        file,
        name: file.name,
        size: file.size,
        type: getFileType(file.type),
        progress: 0,
        uploading: false,
        uploaded: false,
        error: false
      }));
      
      uploadFiles.value = [...uploadFiles.value, ...newFiles];
    };
    
    const removeFile = (index) => {
      uploadFiles.value.splice(index, 1);
    };
    
    const resetUpload = () => {
      uploadFiles.value = [];
      showUploadModal.value = false;
    };
    
    const uploadAllFiles = async () => {
      if (isUploading.value) return;
      isUploading.value = true;
      
      try {
        for (let i = 0; i < uploadFiles.value.length; i++) {
          const fileObj = uploadFiles.value[i];
          if (fileObj.uploaded || fileObj.uploading) continue;
          
          fileObj.uploading = true;
          await uploadFile(fileObj, i);
        }
        
        // Refresh media list after uploading
        fetchMedia(currentPage.value);
        
        // Close modal after a short delay
        setTimeout(() => {
          showUploadModal.value = false;
          uploadFiles.value = [];
        }, 1500);
      } finally {
        isUploading.value = false;
      }
    };
    
    const uploadFile = async (fileObj, index) => {
      const formData = new FormData();
      formData.append('file', fileObj.file);
      
      try {
        const response = await axios.post('/api/admin/media/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (event) => {
            const progress = Math.round((event.loaded * 100) / event.total);
            fileObj.progress = progress;
          }
        });
        
        fileObj.uploading = false;
        fileObj.uploaded = true;
      } catch (err) {
        console.error('Upload failed', err);
        fileObj.uploading = false;
        fileObj.error = true;
      }
    };
    
    // Helper methods
    const getFileType = (mimeType) => {
      if (mimeType.startsWith('image/')) return 'image';
      if (mimeType.startsWith('video/')) return 'video';
      return 'document';
    };
    
    const formatFileType = (mimeType) => {
      if (!mimeType) return 'Unknown';
      
      if (mimeType.startsWith('image/')) {
        return mimeType.replace('image/', '').toUpperCase();
      } else if (mimeType.startsWith('video/')) {
        return mimeType.replace('video/', '').toUpperCase();
      } else if (mimeType.startsWith('application/')) {
        return mimeType.replace('application/', '').toUpperCase();
      }
      
      return mimeType;
    };
    
    const formatFileSize = (bytes) => {
      if (!bytes) return '0 B';
      
      const units = ['B', 'KB', 'MB', 'GB'];
      let i = 0;
      while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
      }
      
      return `${bytes.toFixed(1)} ${units[i]}`;
    };
    
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString();
    };
    
    // Image error handler
    const handleImageError = (event, item) => {
      console.error(`Failed to load image: ${item.thumbnail || item.url}`, item);
      // Try to replace with a fallback image
      event.target.src = '/images/no-image.png';
    };
    
    // Image load success
    const handleImageLoad = (event, item) => {
      // console.log(`Successfully loaded image: ${item.thumbnail || item.url}`);
    };
    
    // Initialize
    onMounted(() => {
      fetchMedia();
    });
    
    return {
      mediaItems,
      filteredMedia,
      isLoading,
      error,
      selectedMedia,
      searchQuery,
      mediaType,
      currentPage,
      totalPages,
      showUploadModal,
      isDragging,
      uploadFiles,
      isUploading,
      fileInput,
      debugMode,
      fetchMedia,
      filterMedia,
      changePage,
      selectMedia,
      confirmSelection,
      triggerFileInput,
      handleFileSelect,
      handleFileDrop,
      removeFile,
      resetUpload,
      uploadAllFiles,
      formatFileType,
      formatFileSize,
      formatDate,
      handleImageError,
      handleImageLoad
    };
  }
});
</script>

<style scoped>
.media-selector {
  display: flex;
  flex-direction: column;
  height: 100%;
  position: relative;
}

/* Controls */
.media-controls {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.search-bar {
  flex: 1;
}

.search-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 0.875rem;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 0.875rem;
  background-color: white;
}

.upload-btn {
  padding: 8px 12px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.875rem;
}

.upload-btn:hover {
  background-color: #2980b9;
}

/* Media grid */
.media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
  padding: 0.5rem;
  overflow-y: auto;
  flex: 1;
}

.media-item {
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  background-color: white;
}

.media-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.media-item.selected {
  border: 2px solid #3498db;
  box-shadow: 0 0 0 1px #3498db;
}

.media-thumbnail {
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8fafc;
  overflow: hidden;
  position: relative;
  border: 1px solid #e2e8f0;
}

.media-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  max-height: 100%;
  display: block;
  position: relative;
  z-index: 1;
  background-color: #ffffff;
}

.video-thumbnail, .document-thumbnail {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
}

.video-thumbnail i, .document-thumbnail i {
  font-size: 2rem;
}

.media-info {
  padding: 0.5rem;
}

.media-name {
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 4px;
}

.media-meta {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: #64748b;
}

/* Loading and error states */
.media-loading, .media-error, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
  color: #64748b;
  flex: 1;
}

.spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #3498db;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #94a3b8;
}

/* Pagination */
.media-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  border-top: 1px solid #e9ecef;
  margin-top: auto;
}

.pagination-btn {
  background: none;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  padding: 4px 8px;
  cursor: pointer;
  margin: 0 4px;
}

.pagination-btn:hover:not(.disabled) {
  background-color: #f1f5f9;
}

.pagination-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  margin: 0 8px;
  font-size: 0.875rem;
  color: #64748b;
}

/* Selected media panel */
.selected-media-panel {
  display: flex;
  border-top: 1px solid #e9ecef;
  padding: 1rem;
  gap: 1rem;
  background-color: #f8f9fa;
}

.selected-media-preview {
  width: 150px;
  height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: white;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.selected-media-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.video-preview, .document-preview {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #64748b;
}

.video-preview i, .document-preview i {
  font-size: 3rem;
  margin-bottom: 0.5rem;
}

.selected-media-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.selected-media-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.125rem;
}

.media-details-row {
  display: flex;
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
}

.detail-label {
  width: 100px;
  color: #64748b;
  font-weight: 500;
}

.media-actions {
  margin-top: auto;
  display: flex;
  gap: 0.5rem;
}

/* Upload Modal */
.upload-modal {
  max-width: 600px;
}

.upload-area {
  border: 2px dashed #cbd5e1;
  border-radius: 4px;
  padding: 2rem;
  transition: border-color 0.2s;
  background-color: #f8fafc;
}

.upload-area.dragging {
  border-color: #3498db;
  background-color: #f0f9ff;
}

.upload-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.upload-message i {
  font-size: 3rem;
  color: #64748b;
  margin-bottom: 1rem;
}

.file-input {
  display: none;
}

.upload-files {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.upload-file-item {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  background-color: white;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
}

.upload-file-info {
  flex: 1;
}

.file-name {
  font-size: 0.875rem;
  margin-bottom: 2px;
}

.file-size {
  font-size: 0.75rem;
  color: #64748b;
}

.upload-progress {
  width: 100px;
  height: 6px;
  background-color: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
  margin: 0 0.5rem;
}

.progress-bar {
  height: 100%;
  background-color: #3498db;
  transition: width 0.2s;
}

.upload-status {
  margin: 0 0.5rem;
}

.upload-status i.fa-check {
  color: #10b981;
}

.upload-status i.fa-times {
  color: #ef4444;
}

.btn-icon {
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #64748b;
}

.btn-icon:hover {
  color: #ef4444;
}

.upload-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  justify-content: center;
}

/* General buttons */
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
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
  background-color: #93c5fd;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #e2e8f0;
  color: #4b5563;
}

.btn-secondary:hover {
  background-color: #cbd5e1;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  width: 90%;
  max-height: 90vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
  margin: 0;
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
  padding: 1rem;
  overflow-y: auto;
  flex: 1;
}

.debug-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px;
  font-size: 9px;
  z-index: 2;
  display: flex;
  flex-direction: column;
}

.debug-info small {
  word-break: break-all;
}

/* Debug toggle */
.debug-toggle {
  margin-left: 5px;
}

.debug-btn {
  background: none;
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 8px;
  cursor: pointer;
  color: #64748b;
}

.debug-btn.active {
  background-color: #fca5a5;
  color: #7f1d1d;
  border-color: #ef4444;
}

.debug-btn:hover {
  background-color: #f1f5f9;
}
</style> 