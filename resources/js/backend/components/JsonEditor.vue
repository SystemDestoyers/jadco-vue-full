<template>
  <div class="json-editor">
    <!-- Removed notification component (using vue3-toastify instead) -->

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
      // Use vue3-toastify for notification
      if (window.$notifications) {
        window.$notifications.warning('Changes reset to original values');
      }
    };

    return {
      internalValue,
      initArray,
      addArrayItem,
      removeArrayItem,
      reset
    };
  },
  methods: {
    notifySuccess(message = 'Content saved successfully!') {
      // Use vue3-toastify
      if (window.$notifications) {
        window.$notifications.success(message);
      }
    },
    notifyError(message = 'Failed to save content.') {
      // Use vue3-toastify
      if (window.$notifications) {
        window.$notifications.error(message);
      }
    },
    notifyInfo(message) {
      // Use vue3-toastify
      if (window.$notifications) {
        window.$notifications.info(message);
      }
    }
  }
});
</script>

<style scoped>
.json-editor {
  padding: 1rem;
  position: relative;
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