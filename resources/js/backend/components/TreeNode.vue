<template>
  <li class="tree-node">
    <div class="node-header">
      <div class="node-toggle" @click="toggleExpand">
        <i :class="[
          'fas', 
          isExpandable ? (isExpanded ? 'fa-caret-down' : 'fa-caret-right') : 'fa-circle',
          isExpandable ? 'expandable' : 'leaf'
        ]"></i>
      </div>
      
      <!-- Editable key -->
      <div class="node-key-container">
        <span v-if="!isEditingKey" class="node-key" @click="startEditKey">{{ name }}</span>
        <input 
          v-else
          ref="keyInput"
          type="text"
          class="edit-input"
          v-model="editableKey"
          @blur="finishEditKey"
          @keyup.enter="finishEditKey"
          @keyup.esc="cancelEditKey"
        />
      </div>
      
      <!-- Editable value for primitive types -->
      <div v-if="isPrimitive" class="node-value-container" :class="{'has-image': isImageSrc && typeof value === 'string' && value}">
        <span v-if="!isEditingValue" 
          class="node-value" 
          :class="valueType"
          @click="startEditValue"
        >{{ displayValue }}</span>
        <input 
          v-else
          ref="valueInput"
          type="text"
          class="edit-input"
          v-model="editableValue"
          @blur="finishEditValue"
          @keyup.enter="finishEditValue"
          @keyup.esc="cancelEditValue"
        />
        
        <!-- Image preview for src fields -->
        <div v-if="isImageSrc && typeof value === 'string' && value" class="image-preview">
          <img :src="getImageUrl(value)" :alt="value" @click="startEditValue" />
        </div>
        
        <!-- Media library button for src fields -->
        <button 
          v-if="isImageSrc" 
          class="action-btn media-btn" 
          title="Select from Media Library"
          @click.stop="openMediaSelector"
        >
          <i class="fas fa-photo-video"></i>
        </button>
      </div>
      
      <!-- Type indicator for objects and arrays -->
      <span v-else class="node-type-indicator" @click="toggleExpand">
        {{ displayValue }}
      </span>
      
      <!-- Actions menu -->
      <div class="node-actions">
        <button v-if="isExpandable" class="action-btn add-btn" @click.stop="showAddItemForm">
          <i class="fas fa-plus"></i>
        </button>
        <button class="action-btn delete-btn" @click.stop="deleteNode">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    
    <!-- Add new item form -->
    <div v-if="isAddingItem" class="add-item-form">
      <div class="form-group">
        <input 
          v-if="isObject" 
          type="text" 
          class="form-control" 
          placeholder="Key"
          v-model="newItemKey"
        />
      </div>
      <div class="form-group">
        <select v-model="newItemType" class="form-control">
          <option value="string">String</option>
          <option value="number">Number</option>
          <option value="boolean">Boolean</option>
          <option value="object">Object</option>
          <option value="array">Array</option>
          <option value="null">Null</option>
        </select>
      </div>
      <div class="form-group" v-if="newItemType === 'string' || newItemType === 'number'">
        <input 
          type="text" 
          class="form-control" 
          :placeholder="newItemType === 'string' ? 'Text value' : 'Numeric value'"
          v-model="newItemValue"
        />
      </div>
      <div class="form-group" v-if="newItemType === 'boolean'">
        <select v-model="newItemValue" class="form-control">
          <option value="true">true</option>
          <option value="false">false</option>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn btn-sm btn-primary" @click="addNewItem">Add</button>
        <button class="btn btn-sm btn-secondary" @click="cancelAddItem">Cancel</button>
      </div>
    </div>
    
    <!-- Media Selector Modal -->
    <div v-if="showMediaModal" class="modal">
      <div class="modal-content media-selector-modal">
        <div class="modal-header">
          <h3>Select Media</h3>
          <button class="close-btn" @click="closeMediaSelector">&times;</button>
        </div>
        <div class="modal-body">
          <media-selector @select="handleMediaSelect" />
        </div>
      </div>
    </div>
    
    <!-- Children nodes -->
    <ul v-if="isExpandable && isExpanded" class="node-children">
      <template v-if="isArray">
        <tree-node
          v-for="(item, index) in value"
          :key="index"
          :name="index"
          :value="item"
          :depth="depth + 1"
          @update:name="updateArrayIndex($event, index)"
          @update:value="updateArrayValue($event, index)"
          @delete-node="deleteArrayItem(index)"
        />
      </template>
      <template v-else-if="isObject">
        <tree-node
          v-for="(val, key) in value"
          :key="key"
          :name="key"
          :value="val"
          :depth="depth + 1"
          @update:name="updateObjectKey($event, key)"
          @update:value="updateObjectValue($event, key)"
          @delete-node="deleteObjectProperty(key)"
        />
      </template>
    </ul>
  </li>
</template>

<script>
import { defineComponent, ref, computed, nextTick } from 'vue';
import MediaSelector from './MediaSelector.vue';
import '../assets/css/components/tree-node.css';
import '../assets/css/backend-global.css';

export default defineComponent({
  name: 'TreeNode',
  components: {
    MediaSelector
  },
  props: {
    name: {
      type: [String, Number],
      required: true
    },
    value: {
      type: [Object, Array, String, Number, Boolean, null],
      required: true
    },
    depth: {
      type: Number,
      default: 0
    }
  },
  emits: ['update:name', 'update:value', 'delete-node'],
  setup(props, { emit }) {
    const isExpanded = ref(props.depth < 2); // Auto-expand first two levels
    
    // Editing state
    const isEditingKey = ref(false);
    const isEditingValue = ref(false);
    const editableKey = ref(String(props.name));
    const editableValue = ref('');
    const keyInput = ref(null);
    const valueInput = ref(null);
    
    // Media selector
    const showMediaModal = ref(false);
    
    // Adding items
    const isAddingItem = ref(false);
    const newItemKey = ref('');
    const newItemType = ref('string');
    const newItemValue = ref('');
    
    // Type detection
    const isArray = computed(() => Array.isArray(props.value));
    const isObject = computed(() => typeof props.value === 'object' && props.value !== null && !isArray.value);
    const isPrimitive = computed(() => !isArray.value && !isObject.value);
    const isExpandable = computed(() => isArray.value || isObject.value);
    
    // Helper function to get the proper image URL for display
    const getImageUrl = (value) => {
      if (!value) return '';
      
      // If it's already a full URL, return it as is
      if (value.startsWith('http') || value.startsWith('//')) {
        return value;
      }
      
      // If it's a relative path without leading slash, add one
      if (!value.startsWith('/')) {
        return '/' + value;
      }
      
      // Otherwise return as is (relative path with leading slash)
      return value;
    };
    
    // Check if this is an image source field
    const isImageSrc = computed(() => {
      // Check if field name suggests it's an image
      const imageNamePattern = String(props.name).toLowerCase() === 'src' || 
            String(props.name).toLowerCase() === 'image' || 
            String(props.name).toLowerCase() === 'background_image' ||
            String(props.name).toLowerCase().includes('image_url');
      
      // Check if value looks like an image file (has image file extension)
      const hasImageExtension = typeof props.value === 'string' && 
        /\.(jpg|jpeg|png|gif|bmp|svg|webp)$/i.test(props.value);
      
      // Return true if either condition is met
      return imageNamePattern || hasImageExtension;
    });
    
    const valueType = computed(() => {
      if (props.value === null) return 'null';
      if (typeof props.value === 'boolean') return 'boolean';
      if (typeof props.value === 'number') return 'number';
      if (typeof props.value === 'string') return 'string';
      return '';
    });
    
    const displayValue = computed(() => {
      if (props.value === null) return 'null';
      if (typeof props.value === 'boolean') return props.value.toString();
      if (typeof props.value === 'number') return props.value.toString();
      if (typeof props.value === 'string') return `"${props.value}"`;
      if (isArray.value) return `Array[${props.value.length}]`;
      if (isObject.value) return `Object{${Object.keys(props.value).length}}`;
      return String(props.value);
    });
    
    // Toggle node expansion
    const toggleExpand = () => {
      if (isExpandable.value) {
        isExpanded.value = !isExpanded.value;
      }
    };
    
    // Key editing
    const startEditKey = () => {
      if (isArray.value) return; // Don't allow editing array indices
      editableKey.value = String(props.name);
      isEditingKey.value = true;
      nextTick(() => {
        keyInput.value.focus();
      });
    };
    
    const finishEditKey = () => {
      if (editableKey.value.trim() !== '') {
        emit('update:name', {
          oldKey: props.name,
          newKey: editableKey.value.trim()
        });
      }
      isEditingKey.value = false;
    };
    
    const cancelEditKey = () => {
      editableKey.value = String(props.name);
      isEditingKey.value = false;
    };
    
    // Value editing
    const startEditValue = () => {
      if (props.value === null) {
        editableValue.value = 'null';
      } else if (typeof props.value === 'boolean') {
        editableValue.value = props.value.toString();
      } else if (typeof props.value === 'number') {
        editableValue.value = props.value.toString();
      } else if (typeof props.value === 'string') {
        editableValue.value = props.value;
        
        // Replace input with textarea for long strings
        if (props.value.length > 100 || props.value.includes('\n')) {
          nextTick(() => {
            const input = valueInput.value;
            if (input && input.type === 'text') {
              // Create and configure textarea
              const textarea = document.createElement('textarea');
              textarea.className = input.className + ' multiline-edit';
              textarea.value = editableValue.value;
              textarea.style.minHeight = '60px';
              textarea.style.height = Math.min(props.value.split('\n').length * 20, 200) + 'px';
              
              // Replace input with textarea
              input.parentNode.replaceChild(textarea, input);
              
              // Update valueInput ref
              valueInput.value = textarea;
              textarea.focus();
              
              // Add event listeners to the new textarea
              textarea.addEventListener('blur', finishEditValue);
              textarea.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.ctrlKey) {
                  finishEditValue();
                } else if (e.key === 'Escape') {
                  cancelEditValue();
                }
              });
            }
          });
        }
      }
      
      isEditingValue.value = true;
      nextTick(() => {
        if (valueInput.value) {
          valueInput.value.focus();
        }
      });
    };
    
    const finishEditValue = () => {
      let newValue = editableValue.value;
      
      // Convert value based on type
      if (valueType.value === 'number') {
        newValue = parseFloat(newValue);
        if (isNaN(newValue)) {
          newValue = 0;
        }
      } else if (valueType.value === 'boolean') {
        newValue = newValue.toLowerCase() === 'true';
      } else if (valueType.value === 'null') {
        newValue = null;
      }
      
      emit('update:value', {
        key: props.name,
        value: newValue
      });
      
      isEditingValue.value = false;
    };
    
    const cancelEditValue = () => {
      isEditingValue.value = false;
    };
    
    // Media selector methods
    const openMediaSelector = () => {
      showMediaModal.value = true;
    };
    
    const closeMediaSelector = () => {
      showMediaModal.value = false;
    };
    
    const handleMediaSelect = (media) => {
      emit('update:value', {
        key: props.name,
        value: media.path || media.url // Use path if available, otherwise fallback to url
      });
      closeMediaSelector();
    };
    
    // Delete node
    const deleteNode = () => {
      emit('delete-node');
    };
    
    // Array update handlers
    const updateArrayIndex = (event, index) => {
      // Can't change array indices directly, but could implement array reordering if needed
      console.log('Array index edit not supported');
    };
    
    const updateArrayValue = (event, index) => {
      const newArray = [...props.value];
      newArray[index] = event.value;
      emit('update:value', {
        key: props.name,
        value: newArray
      });
    };
    
    const deleteArrayItem = (index) => {
      const newArray = [...props.value];
      newArray.splice(index, 1);
      emit('update:value', {
        key: props.name,
        value: newArray
      });
    };
    
    // Object update handlers
    const updateObjectKey = (event, key) => {
      const { oldKey, newKey } = event;
      if (oldKey === newKey) return;
      
      const newObj = { ...props.value };
      const value = newObj[oldKey];
      delete newObj[oldKey];
      newObj[newKey] = value;
      
      emit('update:value', {
        key: props.name,
        value: newObj
      });
    };
    
    const updateObjectValue = (event, key) => {
      const newObj = { ...props.value };
      newObj[key] = event.value;
      
      emit('update:value', {
        key: props.name,
        value: newObj
      });
    };
    
    const deleteObjectProperty = (key) => {
      const newObj = { ...props.value };
      delete newObj[key];
      
      emit('update:value', {
        key: props.name,
        value: newObj
      });
    };
    
    // Add new item
    const showAddItemForm = () => {
      newItemKey.value = '';
      newItemType.value = 'string';
      newItemValue.value = '';
      isAddingItem.value = true;
    };
    
    const cancelAddItem = () => {
      isAddingItem.value = false;
    };
    
    const addNewItem = () => {
      let parsedValue;
      
      switch (newItemType.value) {
        case 'string':
          parsedValue = newItemValue.value;
          break;
        case 'number':
          parsedValue = parseFloat(newItemValue.value);
          if (isNaN(parsedValue)) parsedValue = 0;
          break;
        case 'boolean':
          parsedValue = newItemValue.value === 'true';
          break;
        case 'object':
          parsedValue = {};
          break;
        case 'array':
          parsedValue = [];
          break;
        case 'null':
          parsedValue = null;
          break;
        default:
          parsedValue = '';
      }
      
      if (isArray.value) {
        const newArray = [...props.value];
        newArray.push(parsedValue);
        
        emit('update:value', {
          key: props.name,
          value: newArray
        });
      } else if (isObject.value) {
        if (newItemKey.value.trim() === '') return;
        
        const newObj = { ...props.value };
        newObj[newItemKey.value.trim()] = parsedValue;
        
        emit('update:value', {
          key: props.name,
          value: newObj
        });
      }
      
      isAddingItem.value = false;
    };
    
    return {
      isExpanded,
      isEditingKey,
      isEditingValue,
      isAddingItem,
      showMediaModal,
      editableKey,
      editableValue,
      keyInput,
      valueInput,
      newItemKey,
      newItemType,
      newItemValue,
      isArray,
      isObject,
      isPrimitive,
      isExpandable,
      isImageSrc,
      valueType,
      displayValue,
      getImageUrl,
      toggleExpand,
      startEditKey,
      finishEditKey,
      cancelEditKey,
      startEditValue,
      finishEditValue,
      cancelEditValue,
      openMediaSelector,
      closeMediaSelector,
      handleMediaSelect,
      deleteNode,
      updateArrayIndex,
      updateArrayValue,
      deleteArrayItem,
      updateObjectKey,
      updateObjectValue,
      deleteObjectProperty,
      showAddItemForm,
      cancelAddItem,
      addNewItem
    };
  }
});
</script>

<style>
/* Edit input styling */
.backend-ui .edit-input {
  /* width: 90%; */
  max-width: 100%;
  border: 1px solid #3498db;
  border-radius: 2px;
  padding: 1px 3px;
  font-family: monospace;
  background-color: #f8fafd;
  box-sizing: border-box;
}

.backend-ui .node-value-container .edit-input {
  min-height: 24px;
  height: 61px;
  resize: vertical;
  display: block;
  width: 64rem;
}

/* Style for multiline text editing */
.backend-ui .multiline-edit {
  font-family: monospace;
  min-height: 60px;
  max-height: 300px;
  width: 100%;
  padding: 4px;
  border: 1px solid #3498db;
  border-radius: 3px;
  background-color: #f8fafd;
  line-height: 1.4;
  font-size: 14px;
}
</style>