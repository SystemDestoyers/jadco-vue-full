<template>
  <div class="messages-page">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <router-link to="/admin" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
              </router-link>
              <h1><i class="fas fa-envelope me-2"></i> Messages Inbox</h1>
            </div>
            <div class="d-flex align-items-center">
              <button class="btn btn-primary me-3 compose-btn" @click="showComposeModal">
                <i class="fas fa-paper-plane me-1"></i> Compose
              </button>
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
      </div>

      <div class="row">
        <div class="col-12">
          <div class="messages-container" :class="{'archived-view': showArchived}">
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
                  class="btn btn-sm btn-outline-primary" 
                  :disabled="!hasSelected" 
                  @click="archiveSelected"
                >
                  <i class="fas fa-archive"></i> Archive
                </button>
                <button 
                  class="btn btn-sm btn-outline-danger" 
                  :disabled="!hasSelected" 
                  @click="deleteSelected"
                >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
              
              <div class="btn-group me-2">
                <button 
                  class="btn btn-sm" 
                  :class="showArchived ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="toggleArchivedView"
                >
                  <i class="fas fa-archive"></i> {{ showArchived ? 'Show Inbox' : 'Show Archived' }}
                </button>
                <button 
                  class="btn btn-sm" 
                  :class="showSent ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="toggleSentView"
                >
                  <i class="fas fa-paper-plane"></i> {{ showSent ? 'Show Inbox' : 'Show Sent' }}
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
                <select v-model="itemsPerPage" class="form-select form-select-sm d-inline-block ms-2" style="width: auto;">
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
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
                <span class="fw-bold">From:</span> {{ currentMessage.first_name }} {{ currentMessage.last_name }} 
                &lt;{{ currentMessage.email }}&gt;
                <button 
                  class="btn btn-sm btn-outline-secondary ms-2" 
                  @click="copyEmail(currentMessage.email)"
                  data-bs-toggle="tooltip"
                  :title="copyTooltip"
                >
                  <i class="fas fa-copy"></i>
                </button>
              </div>
              <div v-if="currentMessage.phone" class="phone mb-1">
                <span class="fw-bold">Phone:</span> {{ currentMessage.phone }}
                <button 
                  v-if="currentMessage.phone"
                  class="btn btn-sm btn-outline-secondary ms-2" 
                  @click="copyPhone(currentMessage.phone)"
                  data-bs-toggle="tooltip"
                  title="Copy Phone"
                >
                  <i class="fas fa-copy"></i>
                </button>
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
              class="btn btn-outline-primary" 
              @click="toggleArchiveStatus(currentMessage)"
            >
              <i class="fas fa-archive"></i>
              {{ currentMessage.archived ? 'Unarchive' : 'Archive' }}
            </button>
            <button 
              type="button" 
              class="btn btn-outline-primary" 
              @click="replyToMessage"
            >
              <i class="fas fa-reply"></i> Reply
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

    <!-- Compose Message Modal -->
    <div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="composeModalLabel">Compose Message</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="sendMessage" class="compose-form">
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" v-model="composeForm.first_name" placeholder="First Name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" v-model="composeForm.last_name" placeholder="Last Name" required>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" v-model="composeForm.email" placeholder="Email Address" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input type="tel" class="form-control" id="phone" v-model="composeForm.phone" placeholder="Phone Number">
                  </div>
                </div>
              </div>
              <div class="form-group mb-3">
                <label for="messageContent" class="form-label">Message</label>
                <textarea class="form-control" id="messageContent" rows="6" v-model="composeForm.message" placeholder="Your message here..." required></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="sendMessage" :disabled="sendingMessage">
              <i class="fas" :class="sendingMessage ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
              {{ sendingMessage ? 'Sending...' : 'Send Message' }}
            </button>
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
      itemsPerPage: 20,
      messageModal: null,
      deleteModal: null,
      composeModal: null,
      deleteType: 'single', // 'single' or 'multiple'
      deleteId: null,
      totalMessages: 0,
      unreadCount: 0,
      archivedMessages: [],
      sentMessages: [],
      showArchived: false,
      showSent: false,
      copyTooltip: 'Copy Email',
      composeForm: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        message: ''
      },
      sendingMessage: false,
      searchTimeout: null
    };
  },
  computed: {
    hasSelected() {
      return this.selectedIds.length > 0;
    },
    selectedAll: {
      get() {
        return this.filteredMessages.length > 0 && this.selectedIds.length === this.filteredMessages.length;
      },
      set(value) {
        this.selectedIds = value 
          ? this.filteredMessages.map(message => message.id) 
          : [];
      }
    },
    paginatedMessages() {
      // With server-side pagination, we directly use the filtered messages
      // as they represent the current page from the server
      return this.filteredMessages;
    },
    totalPages() {
      return Math.ceil(this.totalMessages / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    endIndex() {
      return this.startIndex + this.filteredMessages.length;
    }
  },
  created() {
    // Clean up any orphaned modal backdrops
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.parentNode.removeChild(backdrop);
    });
    
    // Reset body styles in case they were set by previous modals
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Remove MediaLibrary CSS if present
    const mediaLibraryCSS = document.querySelector('link[href="/backend/css/media-library.css"]');
    if (mediaLibraryCSS) {
      mediaLibraryCSS.disabled = true;
      mediaLibraryCSS.remove();
    }
    
    // Give it a moment to execute other scripts, then clear backdrops again
    setTimeout(() => {
      document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.parentNode.removeChild(backdrop);
      });
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }, 100);
  },
  mounted() {
    this.fetchMessages();
    // Ensure DOM is fully loaded before initializing modals
    this.$nextTick(() => {
      this.initModals();
    });
  },
  beforeUnmount() {
    this.destroyModals();
    
    // Final cleanup of any stray modal backdrops
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.parentNode.removeChild(backdrop);
    });
    
    // Remove modal-related body styles
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  },
  methods: {
    initModals() {
      // If modals are already initialized, destroy them first
      this.destroyModals();
      
      const messageModalEl = document.getElementById('messageModal');
      const deleteModalEl = document.getElementById('deleteModal');
      const composeModalEl = document.getElementById('composeModal');
      
      // First make sure any existing Bootstrap modal data is cleaned up
      if (messageModalEl) {
        messageModalEl.classList.remove('show');
        messageModalEl.style.display = 'none';
        messageModalEl.setAttribute('aria-hidden', 'true');
        messageModalEl.removeAttribute('aria-modal');
        messageModalEl.removeAttribute('role');
      }
      
      if (deleteModalEl) {
        deleteModalEl.classList.remove('show');
        deleteModalEl.style.display = 'none';
        deleteModalEl.setAttribute('aria-hidden', 'true');
        deleteModalEl.removeAttribute('aria-modal');
        deleteModalEl.removeAttribute('role');
      }
      
      if (composeModalEl) {
        composeModalEl.classList.remove('show');
        composeModalEl.style.display = 'none';
        composeModalEl.setAttribute('aria-hidden', 'true');
        composeModalEl.removeAttribute('aria-modal');
        composeModalEl.removeAttribute('role');
      }
      
      // Initialize modals
      if (messageModalEl) {
        this.messageModal = new Modal(messageModalEl);
        messageModalEl.addEventListener('hidden.bs.modal', this.onMessageModalHidden);
        messageModalEl.addEventListener('hide.bs.modal', () => {
          // Remove focus from any active element before modal is hidden
          if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
          }
        });
      }
      
      if (deleteModalEl) {
        this.deleteModal = new Modal(deleteModalEl);
        deleteModalEl.addEventListener('hide.bs.modal', () => {
          // Remove focus from any active element before modal is hidden
          if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
          }
        });
      }
      
      if (composeModalEl) {
        this.composeModal = new Modal(composeModalEl);
        composeModalEl.addEventListener('hide.bs.modal', () => {
          // Remove focus from any active element before modal is hidden
          if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
          }
        });
      }
    },
    
    destroyModals() {
      const messageModalEl = document.getElementById('messageModal');
      const deleteModalEl = document.getElementById('deleteModal');
      const composeModalEl = document.getElementById('composeModal');
      
      // Remove event listeners
      if (messageModalEl) {
        messageModalEl.removeEventListener('hidden.bs.modal', this.onMessageModalHidden);
      }
      
      // Properly dispose of Bootstrap modal instances
      if (this.messageModal) {
        try {
          this.messageModal.hide();
          this.messageModal.dispose();
        } catch (e) {
          console.error('Error disposing message modal:', e);
        }
      }
      
      if (this.deleteModal) {
        try {
          this.deleteModal.hide();
          this.deleteModal.dispose();
        } catch (e) {
          console.error('Error disposing delete modal:', e);
        }
      }
      
      if (this.composeModal) {
        try {
          this.composeModal.hide();
          this.composeModal.dispose();
        } catch (e) {
          console.error('Error disposing compose modal:', e);
        }
      }
      
      // Clean up modal DOM elements
      if (messageModalEl) {
        messageModalEl.classList.remove('show');
        messageModalEl.style.display = 'none';
        messageModalEl.setAttribute('aria-hidden', 'true');
        messageModalEl.removeAttribute('aria-modal');
        messageModalEl.removeAttribute('role');
      }
      
      if (deleteModalEl) {
        deleteModalEl.classList.remove('show');
        deleteModalEl.style.display = 'none';
        deleteModalEl.setAttribute('aria-hidden', 'true');
        deleteModalEl.removeAttribute('aria-modal');
        deleteModalEl.removeAttribute('role');
      }
      
      if (composeModalEl) {
        composeModalEl.classList.remove('show');
        composeModalEl.style.display = 'none';
        composeModalEl.setAttribute('aria-hidden', 'true');
        composeModalEl.removeAttribute('aria-modal');
        composeModalEl.removeAttribute('role');
      }
      
      this.messageModal = null;
      this.deleteModal = null;
      this.composeModal = null;
    },

    hideAllModals() {
      // Safely hide any open modals
      if (this.messageModal) {
        try {
          this.hideModal(this.messageModal);
        } catch (e) {
          console.error('Error hiding message modal:', e);
        }
      }
      
      if (this.deleteModal) {
        try {
          this.deleteModal.hide();
        } catch (e) {
          console.error('Error hiding delete modal:', e);
        }
      }
      
      if (this.composeModal) {
        try {
          this.composeModal.hide();
        } catch (e) {
          console.error('Error hiding compose modal:', e);
        }
      }
      
      // Clean up lingering backdrops
      document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.parentNode.removeChild(backdrop);
      });
      
      // Reset body styles
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    },
    
    onMessageModalHidden() {
      // Clean up current message when modal is closed
      this.currentMessage = null;
    },

    // This method handles moving focus away before the modal hides to avoid aria-hidden accessibility issues
    hideModal(modalInstance) {
      // Make sure any focused element loses focus before the modal is hidden
      if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
      }
      
      if (modalInstance) {
        modalInstance.hide();
      }
    },

    async fetchMessages() {
      this.loading = true;
      try {
        const params = {
          per_page: this.itemsPerPage,
          page: this.currentPage
        };
        
        // Add search query if present
        if (this.searchQuery.trim()) {
          params.search = this.searchQuery.trim();
        }
        
        const response = await axios.get('/api/admin/messages', { params });
        const data = response.data;
        
        if (data.success) {
          // Handle the case where data is wrapped in a data property (pagination response)
          if (data.data && data.data.data) {
            this.messages = data.data.data;
            this.totalMessages = data.data.total;
            this.currentPage = data.data.current_page;
            this.itemsPerPage = data.data.per_page;
          } else {
            this.messages = data.data || [];
            this.totalMessages = this.messages.length;
          }
          
          this.filteredMessages = this.showArchived || this.showSent ? [] : [...this.messages];
          this.unreadCount = data.unread_count || 0;
          
          // Update the sidebar unread count if it exists
          this.updateSidebarUnreadCount(this.unreadCount);
          
          // Get archived messages
          if (!this.showArchived && !this.showSent) {
            await this.fetchArchivedMessages();
          }
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
    
    async fetchArchivedMessages() {
      try {
        const params = {
          per_page: this.itemsPerPage,
          page: this.currentPage
        };
        
        // Add search query if present
        if (this.searchQuery.trim()) {
          params.search = this.searchQuery.trim();
        }
        
        const archivedResponse = await axios.get('/api/admin/messages/archived', { params });
        if (archivedResponse.data.success) {
          // Handle the case where data is wrapped in a data property (pagination response)
          if (archivedResponse.data.data && archivedResponse.data.data.data) {
            this.archivedMessages = archivedResponse.data.data.data;
            
            // If we're in archived view, update filtered messages
            if (this.showArchived) {
              this.filteredMessages = [...this.archivedMessages];
              this.totalMessages = archivedResponse.data.data.total || this.archivedMessages.length;
              this.currentPage = archivedResponse.data.data.current_page || 1;
            }
          } else {
            this.archivedMessages = archivedResponse.data.data || [];
            
            // If we're in archived view, update filtered messages
            if (this.showArchived) {
              this.filteredMessages = [...this.archivedMessages];
              this.totalMessages = this.archivedMessages.length;
            }
          }
        }
      } catch (error) {
        console.error('Error fetching archived messages:', error);
      }
    },
    
    async fetchSentMessages() {
      this.loading = true;
      try {
        const params = {
          per_page: this.itemsPerPage,
          page: this.currentPage
        };
        
        // Add search query if present
        if (this.searchQuery.trim()) {
          params.search = this.searchQuery.trim();
        }
        
        const response = await axios.get('/api/admin/messages/sent', { params });
        const data = response.data;
        
        if (data.success) {
          // Handle the case where data is wrapped in a data property (pagination response)
          if (data.data && data.data.data) {
            this.sentMessages = data.data.data;
            this.totalMessages = data.data.total;
            this.currentPage = data.data.current_page;
          } else {
            this.sentMessages = data.data || [];
            this.totalMessages = this.sentMessages.length;
          }
          
          this.filteredMessages = [...this.sentMessages];
        } else {
          toast.error('Error fetching sent messages');
        }
      } catch (error) {
        console.error('Error fetching sent messages:', error);
        toast.error('Failed to retrieve sent messages. Please try again.');
      } finally {
        this.loading = false;
      }
    },
    
    viewMessage(message) {
      this.currentMessage = { ...message };
      
      // Mark as read if it's unread
      if (!message.read) {
        this.markMessageAsRead(message.id);
      }
      
      // Check if the messageModal is null and init if needed
      if (!this.messageModal) {
        const messageModalEl = document.getElementById('messageModal');
        if (messageModalEl) {
          this.messageModal = new Modal(messageModalEl);
        } else {
          console.error('Message modal element not found');
          return;
        }
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
      
      // Check if the deleteModal is null and init if needed
      if (!this.deleteModal) {
        const deleteModalEl = document.getElementById('deleteModal');
        if (deleteModalEl) {
          this.deleteModal = new Modal(deleteModalEl);
        } else {
          console.error('Delete modal element not found');
          return;
        }
      }
      
      this.deleteModal.show();
    },
    
    deleteSelected() {
      if (this.selectedIds.length === 0) return;
      
      this.deleteType = 'multiple';
      this.deleteModal.show();
    },
    
    async confirmDelete() {
      this.hideModal(this.deleteModal);
      
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
              this.hideModal(this.messageModal);
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
      // Debounce the search to avoid too many API calls
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.currentPage = 1; // Reset to first page when searching
        this.refreshMessages();
      }, 300);
    },
    
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.refreshMessages();
      }
    },
    
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.refreshMessages();
      }
    },
    
    refreshMessages() {
      if (this.showArchived) {
        this.fetchArchivedMessages();
      } else if (this.showSent) {
        this.fetchSentMessages();
      } else {
        this.fetchMessages();
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
    },
    
    archiveSelected() {
      const promises = this.selectedIds.map(id => this.archiveMessage(id));
      
      Promise.all(promises)
        .then(() => {
          this.selectedIds = [];
          toast.success('Selected messages archived');
        })
        .catch(error => {
          console.error('Error archiving selected messages:', error);
          toast.error('Failed to archive one or more messages');
        });
    },
    
    toggleArchivedView() {
      // If already showing archived messages, go back to inbox
      if (this.showArchived) {
        this.showArchived = false;
        this.showSent = false;
        this.currentPage = 1;
        this.fetchMessages(); // Fetch regular inbox messages
      } else {
        this.showArchived = true;
        this.showSent = false;
        this.currentPage = 1;
        this.fetchArchivedMessages();
      }
      
      this.selectedIds = [];
    },
    
    updateFilteredMessages() {
      if (this.searchQuery.trim()) {
        this.searchMessages();
      } else {
        if (this.showArchived) {
          this.filteredMessages = [...this.archivedMessages];
        } else if (this.showSent) {
          this.filteredMessages = [...this.sentMessages];
        } else {
          this.filteredMessages = [...this.messages];
        }
        this.totalMessages = this.filteredMessages.length;
      }
    },
    
    copyEmail(email) {
      navigator.clipboard.writeText(email)
        .then(() => {
          this.copyTooltip = 'Copied!';
          setTimeout(() => {
            this.copyTooltip = 'Copy Email';
          }, 2000);
          toast.success('Email copied to clipboard!');
        })
        .catch(err => {
          console.error('Could not copy email: ', err);
          toast.error('Failed to copy email');
        });
    },
    
    copyPhone(phone) {
      navigator.clipboard.writeText(phone)
        .then(() => {
          toast.success('Phone number copied to clipboard!');
        })
        .catch(err => {
          console.error('Could not copy phone: ', err);
          toast.error('Failed to copy phone number');
        });
    },
    
    toggleArchiveStatus(message) {
      if (message.archived) {
        this.unarchiveMessage(message.id);
      } else {
        this.archiveMessage(message.id);
      }
    },
    
    async archiveMessage(id) {
      try {
        const response = await axios.put(`/api/admin/messages/${id}/archive`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = response.data;
        
        if (data.success) {
          // Update local data
          const messageIndex = this.messages.findIndex(m => m.id === id);
          if (messageIndex !== -1) {
            this.messages[messageIndex].archived = true;
            
            // Update filtered messages
            const filteredIndex = this.filteredMessages.findIndex(m => m.id === id);
            if (filteredIndex !== -1) {
              this.filteredMessages[filteredIndex].archived = true;
            }
            
            // If viewing the message details, update it there too
            if (this.currentMessage && this.currentMessage.id === id) {
              this.currentMessage.archived = true;
            }
            
            // Move to archived array
            const message = this.messages[messageIndex];
            this.archivedMessages.push({...message});
            
            // Remove from main list if showing inbox
            if (!this.showArchived) {
              this.messages = this.messages.filter(m => m.id !== id);
              this.filteredMessages = this.filteredMessages.filter(m => m.id !== id);
              this.totalMessages = this.filteredMessages.length;
              
              // Adjust current page if needed
              if (this.paginatedMessages.length === 0 && this.currentPage > 1) {
                this.currentPage--;
              }
              
              // Close modal if current message is archived
              if (this.currentMessage && this.currentMessage.id === id) {
                this.hideModal(this.messageModal);
              }
            }
            
            toast.success('Message archived');
          }
        }
      } catch (error) {
        console.error('Error archiving message:', error);
        toast.error('Failed to archive message');
      }
    },
    
    async unarchiveMessage(id) {
      try {
        const response = await axios.put(`/api/admin/messages/${id}/unarchive`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = response.data;
        
        if (data.success) {
          // Update local data
          const archivedIndex = this.archivedMessages.findIndex(m => m.id === id);
          
          if (archivedIndex !== -1) {
            const message = this.archivedMessages[archivedIndex];
            message.archived = false;
            
            // If showing archived, update filtered messages
            if (this.showArchived) {
              const filteredIndex = this.filteredMessages.findIndex(m => m.id === id);
              if (filteredIndex !== -1) {
                this.filteredMessages[filteredIndex].archived = false;
              }
              
              // Remove from display if in archived view
              this.archivedMessages = this.archivedMessages.filter(m => m.id !== id);
              this.filteredMessages = this.filteredMessages.filter(m => m.id !== id);
              this.totalMessages = this.filteredMessages.length;
              
              // If viewing the message details, update it there too
              if (this.currentMessage && this.currentMessage.id === id) {
                this.currentMessage.archived = false;
                this.hideModal(this.messageModal);
              }
            } else {
              // Add back to main messages array
              this.messages.unshift({...message});
              this.updateFilteredMessages();
            }
            
            toast.success('Message moved to inbox');
          }
        }
      } catch (error) {
        console.error('Error unarchiving message:', error);
        toast.error('Failed to unarchive message');
      }
    },
    
    showComposeModal() {
      // Check if the composeModal is null and init if needed
      if (!this.composeModal) {
        const composeModalEl = document.getElementById('composeModal');
        if (composeModalEl) {
          this.composeModal = new Modal(composeModalEl);
        } else {
          console.error('Compose modal element not found');
          return;
        }
      }
      
      // Pre-populate the form if replying to a message
      if (this.currentMessage) {
        this.composeForm.email = this.currentMessage.email;
        this.composeForm.first_name = this.currentMessage.first_name;
        this.composeForm.last_name = this.currentMessage.last_name;
        this.composeForm.phone = this.currentMessage.phone || '';
        this.composeForm.message = `\n\n---------- Original Message ----------\nFrom: ${this.currentMessage.full_name}\nDate: ${this.formatFullDate(this.currentMessage.created_at)}\n\n${this.currentMessage.message}`;
      } else {
        // Reset the form if creating a new message
        this.composeForm = {
          first_name: '',
          last_name: '',
          email: '',
          phone: '',
          message: ''
        };
      }
      
      this.composeModal.show();
    },
    
    async sendMessage() {
      this.sendingMessage = true;
      try {
        const response = await axios.post('/api/admin/messages/send', this.composeForm, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = response.data;
        
        if (data.success) {
          // Close the modal
          this.composeModal.hide();
          
          // Show success message
          toast.success('Message sent successfully');
          
          // Reset the form
          this.composeForm = {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            message: ''
          };
          
          // If we are showing sent messages, refresh the list
          if (this.showSent) {
            this.fetchSentMessages();
          }
        } else {
          toast.error('Error sending message: ' + (data.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error sending message:', error);
        toast.error('Failed to send message: ' + (error.response?.data?.message || error.message || 'Unknown error'));
      } finally {
        this.sendingMessage = false;
      }
    },
    
    replyToMessage() {
      // Close the message view modal
      this.hideModal(this.messageModal);
      
      // Wait a bit for the modal to close to avoid Bootstrap modal issues
      setTimeout(() => {
        // Pre-populate the compose form with the recipient's details
        this.composeForm = {
          email: this.currentMessage.email,
          first_name: this.currentMessage.first_name,
          last_name: this.currentMessage.last_name,
          phone: this.currentMessage.phone || '',
          message: `\n\n---------- Original Message ----------\nFrom: ${this.currentMessage.full_name}\nDate: ${this.formatFullDate(this.currentMessage.created_at)}\n\n${this.currentMessage.message}`
        };
        
        // Show the compose modal
        this.composeModal.show();
      }, 300);
    },
    
    toggleSentView() {
      // Don't toggle if we're already in sent view
      if (this.showSent) {
        this.showSent = false;
        this.showArchived = false;
        this.currentPage = 1;
        this.fetchMessages(); // Show regular inbox
      } else {
        this.showSent = true;
        this.showArchived = false;
        this.currentPage = 1;
        this.fetchSentMessages(); // Show sent messages
      }
      
      this.selectedIds = [];
    }
  },
  watch: {
    itemsPerPage() {
      this.currentPage = 1; // Reset to first page when changing items per page
      this.refreshMessages();
    }
  }
};
</script>

<style>
/* Use more specific selectors to avoid conflicts when navigating between pages */
.messages-page {
  padding: 20px 0;
}

.messages-page .messages-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.messages-page .messages-list {
  max-height: calc(100vh - 240px);
  overflow-y: auto;
}

.messages-page .message-item {
  cursor: pointer;
  transition: background-color 0.15s;
}

.messages-page .message-item:hover {
  background-color: #f8f9fa;
}

.messages-page .message-item.unread {
  background-color: #f0f7ff;
  font-weight: 500;
}

.messages-page .message-item.selected {
  background-color: #e9ecef;
}

.messages-page .message-indicator {
  width: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.messages-page .message-indicator i {
  font-size: 8px;
}

.messages-page .inbox-search {
  width: 300px;
}

/* Compose button */
.messages-page .compose-btn {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  position: relative;
  min-width: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Customize checkboxes */
.messages-page .form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Message content */
.messages-page .message-content {
  min-height: 150px;
}

/* Copy buttons */
.messages-page .sender button, 
.messages-page .phone button {
  padding: 0.125rem 0.375rem;
  font-size: 0.75rem;
}

/* Archive button */
.messages-page .btn-archive {
  background-color: #6c757d;
  color: white;
}

/* Make toolbar responsive */
@media (max-width: 768px) {
  .messages-page .messages-toolbar {
    flex-wrap: wrap;
  }
  
  .messages-page .messages-toolbar .btn-group {
    margin-bottom: 8px;
  }
  
  .messages-page .inbox-search {
    width: 100%;
    margin-bottom: 8px;
  }
}

/* Add a highlight for archived view */
.messages-page .messages-container.archived-view {
  border: 2px solid #6c757d;
}

.messages-page .archived-badge {
  background-color: #6c757d;
  color: white;
  font-size: 0.75rem;
  padding: 2px 6px;
  border-radius: 4px;
  margin-left: 6px;
}

/* Compose form */
.messages-page .compose-form .form-group {
  margin-bottom: 1rem;
  position: relative;
}

.messages-page .compose-form label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  position: relative;
  z-index: 2;
  background: none;
  transform: none;
  color: #333;
  font-size: 0.9rem;
  top: auto;
  left: auto;
}

.messages-page .compose-form .form-control {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  width: 100%;
  background-color: #fff;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  height: auto;
}

.messages-page .compose-form .form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.messages-page .compose-form .form-control::placeholder {
  color: transparent;
}

/* Make sure modal elements don't overlap */
.messages-page #composeModal .modal-body {
  padding: 1.5rem;
}

/* Override Bootstrap floating label behavior */
.messages-page .form-floating > .form-control-plaintext ~ label::after,
.messages-page .form-floating > .form-control:focus ~ label::after, 
.messages-page .form-floating > .form-control:not(:placeholder-shown) ~ label::after {
  display: none !important;
}

.messages-page .form-floating > .form-control-plaintext ~ label,
.messages-page .form-floating > .form-control:focus ~ label,
.messages-page .form-floating > .form-control:not(:placeholder-shown) ~ label,
.messages-page .form-floating > .form-select ~ label {
  opacity: 1 !important;
  transform: none !important;
  color: #212529 !important;
  background-color: transparent !important;
}

/* Ensure form controls appear properly */
.messages-page #composeModal .form-control {
  height: calc(3rem + 2px);
  line-height: 1.5;
  padding: 0.75rem 0.75rem;
}

.messages-page #composeModal textarea.form-control {
  height: auto;
}
</style> 