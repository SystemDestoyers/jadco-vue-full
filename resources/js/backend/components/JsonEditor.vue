<template>
  <div class="json-editor">
    <!-- Notification alert -->
    <div v-if="notification.show" :class="['notification-alert', `notification-${notification.type}`]">
      <div class="notification-content">
        <span>{{ notification.message }}</span>
        <button @click="closeNotification" class="notification-close">&times;</button>
      </div>
    </div>

    <div v-for="(field, key) in schema.properties" :key="key" class="form-group">
      <label :for="key">{{ field.title || key }}</label>
      
      <!-- String input -->
      <textarea 
        v-if="field.type === 'string' && field.format === 'textarea'"
        :id="key"
        v-model="internalValue[key]"
        class="form-control"
        rows="5"
      ></textarea>
      
      <input 
        v-else-if="field.type === 'string'"
        :id="key"
        v-model="internalValue[key]"
        :type="field.format === 'password' ? 'password' : 'text'"
        class="form-control"
      />
      
      <!-- Number input -->
      <input 
        v-else-if="field.type === 'number' || field.type === 'integer'"
        :id="key"
        v-model.number="internalValue[key]"
        type="number"
        class="form-control"
      />
      
      <!-- Boolean input -->
      <div v-else-if="field.type === 'boolean'" class="form-check">
        <input 
          :id="key"
          v-model="internalValue[key]"
          type="checkbox"
          class="form-check-input"
        />
      </div>
      
      <!-- Array input -->
      <div v-else-if="field.type === 'array'" class="array-field">
        <div v-if="!internalValue[key]">
          <button @click="initArray(key)" class="btn btn-sm btn-primary">
            Add {{ field.title || key }}
          </button>
        </div>
        <div v-else class="array-items">
          <div v-for="(item, index) in internalValue[key]" :key="index" class="array-item">
            <div class="array-item-header">
              <h5>Item {{ index + 1 }}</h5>
              <button @click="removeArrayItem(key, index)" class="btn btn-sm btn-danger">
                Remove
              </button>
            </div>
            
            <div v-if="field.items && field.items.type === 'object'" class="object-fields">
              <div 
                v-for="(propSchema, propKey) in field.items.properties" 
                :key="propKey"
                class="form-group"
              >
                <label :for="`${key}-${index}-${propKey}`">{{ propSchema.title || propKey }}</label>
                
                <textarea 
                  v-if="propSchema.type === 'string' && propSchema.format === 'textarea'"
                  :id="`${key}-${index}-${propKey}`"
                  v-model="internalValue[key][index][propKey]"
                  class="form-control"
                  rows="3"
                ></textarea>
                
                <input 
                  v-else-if="propSchema.type === 'string'"
                  :id="`${key}-${index}-${propKey}`"
                  v-model="internalValue[key][index][propKey]"
                  type="text"
                  class="form-control"
                />
                
                <input 
                  v-else-if="propSchema.type === 'number' || propSchema.type === 'integer'"
                  :id="`${key}-${index}-${propKey}`"
                  v-model.number="internalValue[key][index][propKey]"
                  type="number"
                  class="form-control"
                />
                
                <div v-else-if="propSchema.type === 'boolean'" class="form-check">
                  <input 
                    :id="`${key}-${index}-${propKey}`"
                    v-model="internalValue[key][index][propKey]"
                    type="checkbox"
                    class="form-check-input"
                  />
                </div>
              </div>
            </div>
            
            <div v-else>
              <input 
                v-model="internalValue[key][index]"
                :type="field.items.type === 'number' ? 'number' : 'text'"
                class="form-control"
              />
            </div>
          </div>
          
          <button @click="addArrayItem(key, field.items)" class="btn btn-sm btn-primary mt-2">
            Add Item
          </button>
        </div>
      </div>
    </div>
    
    <!-- Slot for additional buttons or controls -->
    <slot></slot>
  </div>
</template>

<script>
import { defineComponent, ref, watch } from 'vue';

export default defineComponent({
  name: 'JsonEditor',
  
  props: {
    schema: {
      type: Object,
      required: true,
      validator: (value) => {
        return value.type === 'object' && value.properties;
      }
    },
    modelValue: {
      type: Object,
      default: () => ({})
    }
  },
  
  emits: ['update:modelValue', 'input'],
  
  setup(props, { emit }) {
    const internalValue = ref({ ...props.modelValue });
    const notification = ref({
      show: false,
      type: 'success',
      message: '',
      timeout: null
    });
    
    // Watch for changes in the modelValue prop
    watch(() => props.modelValue, (newValue) => {
      internalValue.value = { ...newValue };
    });
    
    // Watch for changes in the internal value
    watch(internalValue, (newValue) => {
      emit('update:modelValue', newValue);
      emit('input', newValue);
    }, { deep: true });
    
    const initArray = (key) => {
      if (!internalValue.value[key]) {
        internalValue.value[key] = [];
      }
    };
    
    const addArrayItem = (key, itemSchema) => {
      if (!internalValue.value[key]) {
        internalValue.value[key] = [];
      }
      
      if (itemSchema.type === 'object') {
        const newItem = {};
        
        // Initialize object properties
        if (itemSchema.properties) {
          Object.keys(itemSchema.properties).forEach(propKey => {
            const propSchema = itemSchema.properties[propKey];
            
            // Set default values based on type
            if (propSchema.type === 'string') {
              newItem[propKey] = propSchema.default || '';
            } else if (propSchema.type === 'number' || propSchema.type === 'integer') {
              newItem[propKey] = propSchema.default || 0;
            } else if (propSchema.type === 'boolean') {
              newItem[propKey] = propSchema.default || false;
            } else if (propSchema.type === 'array') {
              newItem[propKey] = propSchema.default || [];
            } else if (propSchema.type === 'object') {
              newItem[propKey] = propSchema.default || {};
            }
          });
        }
        
        internalValue.value[key].push(newItem);
      } else if (itemSchema.type === 'string') {
        internalValue.value[key].push('');
      } else if (itemSchema.type === 'number' || itemSchema.type === 'integer') {
        internalValue.value[key].push(0);
      } else if (itemSchema.type === 'boolean') {
        internalValue.value[key].push(false);
      }
    };
    
    const removeArrayItem = (key, index) => {
      internalValue.value[key].splice(index, 1);
    };
    
    const reset = () => {
      internalValue.value = { ...props.modelValue };
      showNotification('warning', 'Changes reset to original values');
    };

    const showNotification = (type, message, duration = 3000) => {
      // Clear any existing timeout
      if (notification.value.timeout) {
        clearTimeout(notification.value.timeout);
      }
      
      // Set notification properties
      notification.value.show = true;
      notification.value.type = type;
      notification.value.message = message;
      
      // Auto-hide notification after duration
      notification.value.timeout = setTimeout(() => {
        notification.value.show = false;
      }, duration);
    };
    
    const closeNotification = () => {
      notification.value.show = false;
      if (notification.value.timeout) {
        clearTimeout(notification.value.timeout);
      }
    };
    
    return {
      internalValue,
      notification,
      initArray,
      addArrayItem,
      removeArrayItem,
      reset,
      showNotification,
      closeNotification
    };
  },
  methods: {
    notifySuccess(message = 'Content saved successfully!') {
      this.showNotification('success', message);
    },
    notifyError(message = 'Failed to save content.') {
      this.showNotification('error', message);
    },
    notifyInfo(message) {
      this.showNotification('info', message);
    }
  }
});
</script>

<style scoped>
.json-editor {
  padding: 1rem;
  position: relative;
}

/* Notification styles */
.notification-alert {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 300px;
  max-width: 450px;
  border-radius: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slide-in 0.3s ease-out;
}

.notification-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}

.notification-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.notification-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.notification-warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

.notification-info {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.notification-close {
  background: none;
  border: none;
  color: inherit;
  font-size: 1.2rem;
  cursor: pointer;
  margin-left: 8px;
  opacity: 0.7;
}

.notification-close:hover {
  opacity: 1;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

textarea.form-control {
  min-height: 100px;
  resize: vertical;
}

.array-field {
  border: 1px solid #ecf0f1;
  border-radius: 4px;
  padding: 1rem;
  margin-bottom: 1rem;
}

.array-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.array-item {
  border: 1px solid #ecf0f1;
  border-radius: 4px;
  padding: 1rem;
  margin-bottom: 0.5rem;
}

.array-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.array-item-header h5 {
  margin: 0;
  font-size: 1rem;
}

.object-fields {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
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

.btn-danger {
  background-color: #e74c3c;
  color: white;
}

.btn-danger:hover {
  background-color: #c0392b;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.mt-2 {
  margin-top: 0.5rem;
}
</style> 