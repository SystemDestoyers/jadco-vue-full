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
      <div v-if="isPrimitive" class="node-value-container" :class="valueTypeClass">
        <span v-if="!isEditingValue" 
          class="node-value" 
          :class="valueType"
          @click="startEditValue"
        >{{ displayValue }}</span>
        
        <!-- Simple Textarea for regular input -->
        <textarea
          v-else
          ref="valueInput"
          class="edit-input"
          v-model="editableValue"
          @blur="handleBlur"
          @keyup.enter.ctrl="finishEditValue"
          @keyup.esc="cancelEditValue"
          :class="textFormatClasses"
        ></textarea>
        
        <!-- Formatting toolbar when editing -->
        <div v-if="isEditingValue" class="rich-editor-toolbar">
          <button type="button" class="toolbar-btn" :class="{ active: isBold }" @click.stop.prevent="toggleBold" title="Bold">
            <i class="fas fa-bold"></i>
          </button>
          <button type="button" class="toolbar-btn" :class="{ active: isItalic }" @click.stop.prevent="toggleItalic" title="Italic">
            <i class="fas fa-italic"></i>
          </button>
          <button type="button" class="toolbar-btn" :class="{ active: isLarge }" @click.stop.prevent="toggleLargeText" title="Larger Text">
            <i class="fas fa-plus"></i>
          </button>
          <button type="button" class="toolbar-btn" :class="{ active: isSmall }" @click.stop.prevent="toggleSmallText" title="Smaller Text">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="toolbar-btn" :class="{ active: isMonospace }" @click.stop.prevent="toggleMonospace" title="Monospace">
            <i class="fas fa-code"></i>
          </button>
          <button type="button" class="toolbar-btn" :class="{ active: isSansSerif }" @click.stop.prevent="toggleSansSerif" title="Sans-serif">
            <i class="fas fa-font"></i>
          </button>
        </div>
        
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
import { defineComponent, ref, computed, nextTick, watch } from 'vue';
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
    
    // Text formatting state
    const isBold = ref(false);
    const isItalic = ref(false);
    const isLarge = ref(false);
    const isSmall = ref(false);
    const isMonospace = ref(true); // Default to monospace
    const isSansSerif = ref(false);
    
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
    
    // Text format classes computed property
    const textFormatClasses = computed(() => ({
      'bold': isBold.value,
      'italic': isItalic.value,
      'larger': isLarge.value,
      'smaller': isSmall.value,
      'monospace': isMonospace.value,
      'sans-serif': isSansSerif.value
    }));
    
    // Value type class for styling
    const valueTypeClass = computed(() => {
      if (props.value === null) return 'null-value';
      if (typeof props.value === 'boolean') return 'boolean-value';
      if (typeof props.value === 'number') return 'number-value';
      if (typeof props.value === 'string') return 'string-value';
      return '';
    });
    
    // Adjust textarea height on value change
    watch(editableValue, (newValue) => {
      nextTick(() => {
        if (valueInput.value) {
          valueInput.value.style.height = 'auto';
          valueInput.value.style.height = (valueInput.value.scrollHeight) + 'px';
        }
      });
    });
    
    // Toggle text formatting functions
    const toggleBold = (e) => {
      e.preventDefault();
      isBold.value = !isBold.value;
      preserveFocus();
    };
    
    const toggleItalic = (e) => {
      e.preventDefault();
      isItalic.value = !isItalic.value;
      preserveFocus();
    };
    
    const toggleLargeText = (e) => {
      e.preventDefault();
      isLarge.value = !isLarge.value;
      isSmall.value = false;
      preserveFocus();
    };
    
    const toggleSmallText = (e) => {
      e.preventDefault();
      isSmall.value = !isSmall.value;
      isLarge.value = false;
      preserveFocus();
    };
    
    const toggleMonospace = (e) => {
      e.preventDefault();
      isMonospace.value = !isMonospace.value;
      isSansSerif.value = false;
      preserveFocus();
    };
    
    const toggleSansSerif = (e) => {
      e.preventDefault();
      isSansSerif.value = !isSansSerif.value;
      isMonospace.value = false;
      preserveFocus();
    };
    
    // Ensure textarea stays focused and prevents the edit mode from closing
    const preserveFocus = () => {
      // Prevent any blur events from happening during the toggle
      nextTick(() => {
        if (valueInput.value && isEditingValue.value) {
          valueInput.value.focus();
          
          // Preserve cursor position
          const selectionStart = valueInput.value.selectionStart;
          const selectionEnd = valueInput.value.selectionEnd;
          
          // Re-apply selection range after focus
          nextTick(() => {
            if (valueInput.value) {
              valueInput.value.setSelectionRange(selectionStart, selectionEnd);
            }
          });
        }
      });
    };
    
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
      }
      
      // Reset formatting states
      isBold.value = false;
      isItalic.value = false;
      isLarge.value = false;
      isSmall.value = false;
      isMonospace.value = true;
      isSansSerif.value = false;
      
      isEditingValue.value = true;
      
      // Focus and adjust textarea height after Vue updates the DOM
      nextTick(() => {
        if (valueInput.value) {
          valueInput.value.focus();
          
          // Auto-adjust height based on content
          valueInput.value.style.height = 'auto';
          valueInput.value.style.height = (valueInput.value.scrollHeight) + 'px';
          
          // Position cursor at the end
          const length = editableValue.value.length;
          valueInput.value.setSelectionRange(length, length);
        }
      });
    };
    
    // Handle blur events with special handling for toolbar clicks
    const handleBlur = (e) => {
      // Check if the related target (where focus is going) is within our toolbar
      // This prevents closing edit mode when clicking toolbar buttons
      const toolbar = document.querySelector('.rich-editor-toolbar');
      if (toolbar && (toolbar.contains(e.relatedTarget) || toolbar === e.relatedTarget)) {
        // If clicked on toolbar, don't close the edit mode
        // Re-focus the textarea after a short delay to ensure it keeps focus
        setTimeout(() => {
          if (valueInput.value && isEditingValue.value) {
            valueInput.value.focus();
          }
        }, 10);
        return;
      }
      
      // Normal blur handling - finish editing
      finishEditValue();
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
      valueTypeClass,
      displayValue,
      getImageUrl,
      toggleExpand,
      startEditKey,
      finishEditKey,
      cancelEditKey,
      startEditValue,
      handleBlur,
      finishEditValue,
      cancelEditValue,
      // Formatting controls
      isBold,
      isItalic,
      isLarge,
      isSmall,
      isMonospace,
      isSansSerif,
      textFormatClasses,
      toggleBold,
      toggleItalic,
      toggleLargeText,
      toggleSmallText,
      toggleMonospace,
      toggleSansSerif,
      // Media selector methods
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
  max-width: 100%;
  border: 1px solid #3498db;
  border-radius: 4px;
  padding: 8px 10px;
  font-family: 'Courier New', monospace;
  background-color: #f8fafd;
  box-sizing: border-box;
  font-size: 14px;
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-word;
  overflow: auto;
  color: #2c3e50;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.backend-ui .edit-input:focus {
  border-color: #2980b9;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
  outline: none;
}

.backend-ui .node-value-container .edit-input {
  min-height: 24px;
  height: auto;
  min-height: 61px;
  resize: vertical;
  display: block;
  width: 64rem;
  border-radius: 4px;
  overflow-wrap: break-word;
  font-weight: normal;
  margin-bottom: 5px;
}

/* Different styling based on value type */
.backend-ui .node-value-container.string-value .node-value {
  color: #27ae60;
}

.backend-ui .node-value-container.number-value .node-value {
  color: #3498db;
  font-weight: bold;
}

.backend-ui .node-value-container.boolean-value .node-value {
  color: #9b59b6;
  font-weight: bold;
}

/* Rich text editor toolbar styles */
.backend-ui .rich-editor-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  padding: 5px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  margin-top: 5px;
  margin-bottom: 10px;
}

.backend-ui .toolbar-btn {
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 3px;
  padding: 3px 8px;
  font-size: 12px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  transition: all 0.2s ease;
}

.backend-ui .toolbar-btn:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}

.backend-ui .toolbar-btn.active {
  background: #e0f2fe;
  border-color: #7dd3fc;
  color: #0284c7;
}

/* Rich text styles that can be applied */
.backend-ui .edit-input.bold {
  font-weight: bold;
}

.backend-ui .edit-input.italic {
  font-style: italic;
}

.backend-ui .edit-input.larger {
  font-size: 16px;
}

.backend-ui .edit-input.smaller {
  font-size: 12px;
}

.backend-ui .edit-input.monospace {
  font-family: 'Courier New', monospace;
}

.backend-ui .edit-input.sans-serif {
  font-family: 'Arial', sans-serif;
}

/* Style for multiline text editing */
.backend-ui .multiline-edit {
  font-family: 'Courier New', monospace;
  min-height: 60px;
  max-height: 300px;
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #3498db;
  border-radius: 4px;
  background-color: #f8fafd;
  line-height: 1.5;
  font-size: 14px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Auto-convert all edit inputs to textareas for better text editing */
.backend-ui input.edit-input {
  height: auto;
  min-height: 61px;
}
</style>