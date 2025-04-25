<template>
  <div class="messages-page">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-envelope me-2"></i> Messages Inbox</h1>
            <div class="inbox-search">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Search messages..." v-model="searchQuery" @input="searchMessages">
                <span class="input-group-text bg-primary text-white">
                  <i class="fas fa-search"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="messages-container">
            <!-- Toolbar -->
            <div class="messages-toolbar bg-light border p-2 d-flex align-items-center">
              <div class="form-check me-2">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="selectAll" 
                  :checked="selectedAll" 
                  @change="toggleSelectAll"
                >
                <label class="form-check-label" for="selectAll"></label>
              </div>

              <div class="btn-group me-2">
                <button 
                  class="btn btn-sm btn-outline-secondary" 
                  :disabled="!hasSelected" 
                  @click="markAsRead"
                >
                  <i class="fas fa-envelope-open"></i> Mark as Read
                </button>
                <button 
                  class="btn btn-sm btn-outline-secondary" 
                  :disabled="!hasSelected" 
                  @click="markAsUnread"
                >
                  <i class="fas fa-envelope"></i> Mark as Unread
                </button>
                <button 
                  class="btn btn-sm btn-outline-danger" 
                  :disabled="!hasSelected" 
                  @click="deleteSelected"
                >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>

              <div class="ms-auto">
                <span v-if="totalMessages > 0">
                  {{ startIndex + 1 }}-{{ Math.min(endIndex, totalMessages) }} of {{ totalMessages }}
                </span>
                <button class="btn btn-sm btn-outline-secondary ms-2" :disabled="currentPage === 1" @click="prevPage">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary ms-1" :disabled="currentPage >= totalPages" @click="nextPage">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>

            <!-- Messages List -->
            <div class="messages-list">
              <div v-if="loading" class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
              
              <div v-else-if="filteredMessages.length === 0" class="text-center p-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No messages found</p>
              </div>
              
              <template v-else>
                <div 
                  v-for="message in paginatedMessages" 
                  :key="message.id" 
                  :class="['message-item', 'border-bottom', 'p-2', { 'unread': !message.read, 'selected': isSelected(message.id) }]"
                  @click="viewMessage(message)"
                >
                  <div class="d-flex align-items-center">
                    <div class="form-check me-2" @click.stop>
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        :id="`message-${message.id}`" 
                        :checked="isSelected(message.id)" 
                        @change="toggleSelect(message.id)"
                      >
                      <label class="form-check-label" :for="`message-${message.id}`"></label>
                    </div>
                    
                    <div class="message-indicator me-2">
                      <i v-if="!message.read" class="fas fa-circle text-primary"></i>
                    </div>
                    
                    <div class="sender fw-bold col-2 text-truncate">{{ message.full_name }}</div>
                    
                    <div class="subject col text-truncate pe-3">
                      <span class="me-2">{{ message.first_name }} {{ message.last_name }}</span>
                      <span class="text-muted text-truncate">{{ truncateMessage(message.message, 80) }}</span>
                    </div>
                    
                    <div class="date text-muted">{{ formatDate(message.created_at) }}</div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Message View Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" v-if="currentMessage">
          <div class="modal-header">
            <h5 class="modal-title" id="messageModalLabel">
              Message from {{ currentMessage.first_name }} {{ currentMessage.last_name }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="message-header mb-3">
              <div class="sender mb-1">
                <span class="fw-bold">From:</span> {{ currentMessage.first_name }} {{ currentMessage.last_name }} &lt;{{ currentMessage.email }}&gt;
              </div>
              <div v-if="currentMessage.phone" class="phone mb-1">
                <span class="fw-bold">Phone:</span> {{ currentMessage.phone }}
              </div>
              <div class="date mb-1">
                <span class="fw-bold">Date:</span> {{ formatFullDate(currentMessage.created_at) }}
              </div>
            </div>
            <div class="message-content p-3 bg-light rounded">
              <p style="white-space: pre-line">{{ currentMessage.message }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-outline-secondary" 
              @click="toggleReadStatus(currentMessage)"
            >
              <i :class="[currentMessage.read ? 'fas fa-envelope' : 'fas fa-envelope-open']"></i>
              {{ currentMessage.read ? 'Mark as Unread' : 'Mark as Read' }}
            </button>
            <button 
              type="button" 
              class="btn btn-outline-danger" 
              @click="deleteMessage(currentMessage.id)"
            >
              <i class="fas fa-trash"></i> Delete
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete {{ selectedIds.length }} message(s)?</p>
            <p class="text-danger"><small>This action cannot be undone.</small></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default {
  name: 'MessagesPage',
  data() {
    return {
      messages: [],
      filteredMessages: [],
      selectedIds: [],
      loading: true,
      currentMessage: null,
      searchQuery: '',
      currentPage: 1,
      itemsPerPage: 15,
      messageModal: null,
      deleteModal: null,
      deleteType: 'single', // 'single' or 'multiple'
      deleteId: null,
      totalMessages: 0,
      unreadCount: 0
    };
  },
  computed: {
    hasSelected() {
      return this.selectedIds.length > 0;
    },
    selectedAll: {
      get() {
        return this.paginatedMessages.length > 0 && this.selectedIds.length === this.paginatedMessages.length;
      },
      set(value) {
        this.selectedIds = value 
          ? this.paginatedMessages.map(message => message.id) 
          : [];
      }
    },
    paginatedMessages() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredMessages.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredMessages.length / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    endIndex() {
      return this.startIndex + this.paginatedMessages.length;
    }
  },
  mounted() {
    this.fetchMessages();
    this.initModals();
  },
  methods: {
    async fetchMessages() {
      this.loading = true;
      try {
        const response = await axios.get('/api/admin/messages');
        const data = response.data;
        
        if (data.success) {
          this.messages = data.data.data || data.data;
          this.filteredMessages = [...this.messages];
          this.totalMessages = this.messages.length;
          this.unreadCount = data.unread_count || 0;
          
          // Update the sidebar unread count if it exists
          this.updateSidebarUnreadCount(this.unreadCount);
        } else {
          toast.error('Error fetching messages');
        }
      } catch (error) {
        console.error('Error fetching messages:', error);
        toast.error('Failed to retrieve messages. Please try again.');
      } finally {
        this.loading = false;
      }
    },
    
    initModals() {
      this.messageModal = new Modal(document.getElementById('messageModal'));
      this.deleteModal = new Modal(document.getElementById('deleteModal'));
    },
    
    viewMessage(message) {
      this.currentMessage = { ...message };
      
      // Mark as read if it's unread
      if (!message.read) {
        this.markMessageAsRead(message.id);
      }
      
      this.messageModal.show();
    },
    
    toggleSelect(id) {
      const index = this.selectedIds.indexOf(id);
      if (index === -1) {
        this.selectedIds.push(id);
      } else {
        this.selectedIds.splice(index, 1);
      }
    },
    
    toggleSelectAll() {
      if (this.selectedAll) {
        this.selectedIds = [];
      } else {
        this.selectedIds = this.paginatedMessages.map(message => message.id);
      }
    },
    
    isSelected(id) {
      return this.selectedIds.includes(id);
    },
    
    async markMessageAsRead(id) {
      try {
        const response = await axios.put(`/api/admin/messages/${id}/mark-as-read`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = response.data;
        
        if (data.success) {
          // Update local data
          const messageIndex = this.messages.findIndex(m => m.id === id);
          if (messageIndex !== -1) {
            this.messages[messageIndex].read = true;
            
            // Update filtered messages too
            const filteredIndex = this.filteredMessages.findIndex(m => m.id === id);
            if (filteredIndex !== -1) {
              this.filteredMessages[filteredIndex].read = true;
            }
            
            this.unreadCount = Math.max(0, this.unreadCount - 1);
            
            // Update the sidebar unread count
            this.updateSidebarUnreadCount(this.unreadCount);
          }
        }
      } catch (error) {
        console.error('Error marking message as read:', error);
        toast.error('Failed to mark message as read');
      }
    },
    
    async markMessageAsUnread(id) {
      try {
        const response = await axios.put(`/api/admin/messages/${id}/mark-as-unread`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = response.data;
        
        if (data.success) {
          // Update local data
          const messageIndex = this.messages.findIndex(m => m.id === id);
          if (messageIndex !== -1) {
            this.messages[messageIndex].read = false;
            
            // Update filtered messages too
            const filteredIndex = this.filteredMessages.findIndex(m => m.id === id);
            if (filteredIndex !== -1) {
              this.filteredMessages[filteredIndex].read = false;
            }
            
            this.unreadCount++;
            
            // Update the sidebar unread count
            this.updateSidebarUnreadCount(this.unreadCount);
          }
        }
      } catch (error) {
        console.error('Error marking message as unread:', error);
        toast.error('Failed to mark message as unread');
      }
    },
    
    toggleReadStatus(message) {
      if (message.read) {
        this.markMessageAsUnread(message.id);
      } else {
        this.markMessageAsRead(message.id);
      }
    },
    
    markAsRead() {
      this.selectedIds.forEach(id => {
        const message = this.messages.find(m => m.id === id);
        if (message && !message.read) {
          this.markMessageAsRead(id);
        }
      });
    },
    
    markAsUnread() {
      this.selectedIds.forEach(id => {
        const message = this.messages.find(m => m.id === id);
        if (message && message.read) {
          this.markMessageAsUnread(id);
        }
      });
    },
    
    deleteMessage(id) {
      this.deleteType = 'single';
      this.deleteId = id;
      this.deleteModal.show();
    },
    
    deleteSelected() {
      if (this.selectedIds.length === 0) return;
      
      this.deleteType = 'multiple';
      this.deleteModal.show();
    },
    
    async confirmDelete() {
      this.deleteModal.hide();
      
      const idsToDelete = this.deleteType === 'single' 
        ? [this.deleteId] 
        : [...this.selectedIds];
      
      let unreadDeleted = 0;
      let successCount = 0;
      
      for (const id of idsToDelete) {
        try {
          // First check if the message is unread before deleting
          const message = this.messages.find(m => m.id === id);
          const isUnread = message && !message.read;
          
          const response = await axios.delete(`/api/admin/messages/${id}`, {
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          });
          
          const data = response.data;
          
          if (data.success) {
            successCount++;
            // Update local data
            this.messages = this.messages.filter(m => m.id !== id);
            this.filteredMessages = this.filteredMessages.filter(m => m.id !== id);
            this.selectedIds = this.selectedIds.filter(selectedId => selectedId !== id);
            
            // Check if current message is being deleted
            if (this.currentMessage && this.currentMessage.id === id) {
              this.messageModal.hide();
              this.currentMessage = null;
            }
            
            // Count unread messages that were deleted
            if (isUnread) {
              unreadDeleted++;
            }
          }
        } catch (error) {
          console.error('Error deleting message:', error);
          toast.error(`Failed to delete message ID: ${id}`);
        }
      }
      
      this.totalMessages = this.messages.length;
      
      // Adjust unread count if unread messages were deleted
      if (unreadDeleted > 0) {
        this.unreadCount = Math.max(0, this.unreadCount - unreadDeleted);
        this.updateSidebarUnreadCount(this.unreadCount);
      }
      
      // Show success message
      if (successCount > 0) {
        toast.success(`Successfully deleted ${successCount} message(s)`);
      }
      
      // Adjust current page if needed
      if (this.paginatedMessages.length === 0 && this.currentPage > 1) {
        this.currentPage--;
      }
    },
    
    searchMessages() {
      if (!this.searchQuery.trim()) {
        this.filteredMessages = [...this.messages];
        return;
      }
      
      const query = this.searchQuery.toLowerCase();
      this.filteredMessages = this.messages.filter(message => 
        message.first_name.toLowerCase().includes(query) ||
        message.last_name.toLowerCase().includes(query) ||
        message.email.toLowerCase().includes(query) ||
        (message.phone && message.phone.toLowerCase().includes(query)) ||
        message.message.toLowerCase().includes(query)
      );
      
      // Reset to first page when searching
      this.currentPage = 1;
    },
    
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
    
    truncateMessage(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const yesterday = new Date(now);
      yesterday.setDate(yesterday.getDate() - 1);
      
      if (date.toDateString() === now.toDateString()) {
        // Today - show time
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      } else if (date.toDateString() === yesterday.toDateString()) {
        // Yesterday
        return 'Yesterday';
      } else if (now.getFullYear() === date.getFullYear()) {
        // This year - show month and day
        return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
      } else {
        // Different year - show full date
        return date.toLocaleDateString([], { year: 'numeric', month: 'short', day: 'numeric' });
      }
    },
    
    formatFullDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString([], {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    updateSidebarUnreadCount(count) {
      // Try to update the unreadCount ref in AdminLayout if it exists
      if (window.updateAdminUnreadCount) {
        window.updateAdminUnreadCount(count);
      }
    }
  }
};
</script>

<style scoped>
.messages-page {
  padding: 20px 0;
}

.messages-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.messages-list {
  max-height: calc(100vh - 240px);
  overflow-y: auto;
}

.message-item {
  cursor: pointer;
  transition: background-color 0.15s;
}

.message-item:hover {
  background-color: #f8f9fa;
}

.message-item.unread {
  background-color: #f0f7ff;
  font-weight: 500;
}

.message-item.selected {
  background-color: #e9ecef;
}

.message-indicator {
  width: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.message-indicator i {
  font-size: 8px;
}

.inbox-search {
  width: 300px;
}

/* Customize checkboxes */
.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Message content */
.message-content {
  min-height: 150px;
}
</style> 