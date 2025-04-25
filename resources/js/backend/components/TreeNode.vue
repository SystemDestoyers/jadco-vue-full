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
        >{{ displayRawValue }}</span>
        
        <!-- Preview of formatted content when editing -->
        <div v-if="isEditingValue && hasHtmlFormatting" class="formatted-preview" v-html="editableValue"></div>
        
        <!-- Simple Textarea for regular input -->
        <textarea
          v-if="isEditingValue"
          ref="valueInput"
          class="edit-input"
          v-model="editableValue"
          @blur="handleBlur"
          @keyup.enter.ctrl="finishEditValue"
          @keydown.enter.exact.prevent="insertLineBreak"
          @keyup.esc="cancelEditValue"
          :class="textFormatClasses"
        ></textarea>
        
        <!-- Formatting toolbar when editing -->
        <div v-if="isEditingValue" class="rich-editor-toolbar">
          <!-- Text style buttons -->
          <div class="toolbar-group">
            <button type="button" class="toolbar-btn" :class="{ active: isBold }" @click.stop.prevent="toggleBold" title="Bold">
              <i class="fas fa-bold"></i>
            </button>
            <button type="button" class="toolbar-btn" :class="{ active: isItalic }" @click.stop.prevent="toggleItalic" title="Italic">
              <i class="fas fa-italic"></i>
            </button>
            <button type="button" class="toolbar-btn" :class="{ active: isUnderline }" @click.stop.prevent="toggleUnderline" title="Underline">
              <i class="fas fa-underline"></i>
            </button>
          </div>
          
          <!-- Font family buttons -->
          <div class="toolbar-group">
            <button type="button" class="toolbar-btn" :class="{ active: isMonospace }" @click.stop.prevent="toggleMonospace" title="Monospace">
              <i class="fas fa-code"></i>
            </button>
            <button type="button" class="toolbar-btn" :class="{ active: isSansSerif }" @click.stop.prevent="toggleSansSerif" title="Sans-serif">
              <i class="fas fa-font"></i>
            </button>
          </div>
          
          <!-- Font size control -->
          <div class="toolbar-group font-size-control">
            <span class="control-label">Size:</span>
            <div class="number-input">
              <button @click.stop.prevent="decrementFontSize" class="decrement" title="Decrease font size">-</button>
              <input type="number" 
                    min="8" 
                    max="64" 
                    step="1" 
                    v-model="fontSize" 
                    @change="updateFontSize" 
                    @blur="handleInputBlur"
                    @keydown.stop>
              <button @click.stop.prevent="incrementFontSize" class="increment" title="Increase font size">+</button>
            </div>
          </div>
          
          <!-- Color controls -->
          <div class="toolbar-group">
            <button type="button" class="toolbar-btn color-btn" @click.stop.prevent="applyColor('#27ae60')" title="Green" style="color: #27ae60">
              <i class="fas fa-paint-brush"></i>
            </button>
            <button type="button" class="toolbar-btn color-btn" @click.stop.prevent="applyColor('#3498db')" title="Blue" style="color: #3498db">
              <i class="fas fa-paint-brush"></i>
            </button>
            <button type="button" class="toolbar-btn color-btn" @click.stop.prevent="applyColor('#e74c3c')" title="Red" style="color: #e74c3c">
              <i class="fas fa-paint-brush"></i>
            </button>
            <button type="button" class="toolbar-btn color-btn" @click.stop.prevent="applyColor('#f39c12')" title="Orange" style="color: #f39c12">
              <i class="fas fa-paint-brush"></i>
            </button>
          </div>
          
          <!-- Clear formatting -->
          <div class="toolbar-group">
            <button type="button" class="toolbar-btn" @click.stop.prevent="clearFormatting" title="Clear formatting">
              <i class="fas fa-remove-format"></i>
            </button>
          </div>
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
    const isUnderline = ref(false);
    const isMonospace = ref(true); // Default to monospace
    const isSansSerif = ref(false);
    const fontSize = ref(14);
    
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
      'underline': isUnderline.value,
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
      applyStyleToFullText('font-weight', isBold.value ? 'normal' : 'bold');
      isBold.value = !isBold.value;
      preserveFocus();
    };
    
    const toggleItalic = (e) => {
      e.preventDefault();
      applyStyleToFullText('font-style', isItalic.value ? 'normal' : 'italic');
      isItalic.value = !isItalic.value;
      preserveFocus();
    };
    
    const toggleUnderline = (e) => {
      e.preventDefault();
      applyStyleToFullText('text-decoration', isUnderline.value ? 'none' : 'underline');
      isUnderline.value = !isUnderline.value;
      preserveFocus();
    };
    
    const toggleMonospace = (e) => {
      e.preventDefault();
      applyStyleToFullText('font-family', isMonospace.value ? 'Arial, sans-serif' : '"Courier New", monospace');
      isMonospace.value = !isMonospace.value;
      isSansSerif.value = !isMonospace.value;
      preserveFocus();
    };
    
    const toggleSansSerif = (e) => {
      e.preventDefault();
      applyStyleToFullText('font-family', isSansSerif.value ? '"Courier New", monospace' : 'Arial, sans-serif');
      isSansSerif.value = !isSansSerif.value;
      isMonospace.value = !isSansSerif.value;
      preserveFocus();
    };
    
    // Font size control
    const incrementFontSize = () => {
      if (fontSize.value < 64) {
        fontSize.value++;
        applyStyleToFullText('font-size', `${fontSize.value}px`);
      }
    };
    
    const decrementFontSize = () => {
      if (fontSize.value > 8) {
        fontSize.value--;
        applyStyleToFullText('font-size', `${fontSize.value}px`);
      }
    };
    
    const updateFontSize = () => {
      // Ensure font size is within limits
      if (fontSize.value < 8) fontSize.value = 8;
      if (fontSize.value > 64) fontSize.value = 64;
      
      applyStyleToFullText('font-size', `${fontSize.value}px`);
    };
    
    // Color application
    const applyColor = (color) => {
      applyStyleToFullText('color', color);
      preserveFocus();
    };
    
    // Apply a specific style to the entire text
    const applyStyleToFullText = (property, value) => {
      if (!valueInput.value) return;
      
      let text = editableValue.value;
      
      // Check if text is already wrapped in a style tag
      const styleRegex = /<span style="([^"]*)">([\s\S]*?)<\/span>/;
      const match = text.match(styleRegex);
      
      if (match) {
        // Text already has a style tag, update the style
        let styles = match[1];
        const content = match[2];
        
        // Parse existing styles into an object
        const styleObj = {};
        styles.split(';').forEach(style => {
          if (style.trim()) {
            const [prop, val] = style.split(':').map(s => s.trim());
            if (prop && val) {
              styleObj[prop] = val;
            }
          }
        });
        
        // Update or add the new style
        styleObj[property] = value;
        
        // Convert back to style string
        styles = Object.entries(styleObj)
          .map(([prop, val]) => `${prop}: ${val}`)
          .join('; ');
        
        // Recreate the style tag
        text = `<span style="${styles}">${content}</span>`;
      } else {
        // No style tag yet, create one
        text = `<span style="${property}: ${value};">${text}</span>`;
      }
      
      editableValue.value = text;
    };
    
    // Clear formatting from text
    const clearFormatting = () => {
      if (!valueInput.value) return;
      
      const text = editableValue.value;
      
      // Remove HTML tags using regex, preserve the innermost content
      const cleanText = text.replace(/<[^>]+>|<\/[^>]+>/g, "");
      editableValue.value = cleanText;
      
      // Reset all formatting states
      isBold.value = false;
      isItalic.value = false;
      isUnderline.value = false;
      fontSize.value = 14;
      
      preserveFocus();
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
      if (typeof props.value === 'string') {
        // For plain display, remove any HTML tags for safety
        const plainText = props.value.replace(/<[^>]*>/g, '');
        return plainText;
      }
      if (isArray.value) return `Array[${props.value.length}]`;
      if (isObject.value) return `Object{${Object.keys(props.value).length}}`;
      return String(props.value);
    });
    
    // For display with formatting, but safely escaping HTML for security
    const displayValueHTML = computed(() => {
      if (typeof props.value === 'string') {
        // Remove the quotes that were surrounding the string value
        let content = props.value;
        
        // If it already contains HTML tags, just return the content without quotes
        if (/<[a-z][\s\S]*>/i.test(content)) {
          return content;
        }
        
        // Otherwise, escape HTML special characters
        return escapeHTML(content);
      }
      
      return displayValue.value;
    });
    
    // Helper to escape HTML for display
    const escapeHTML = (str) => {
      return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    };
    
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
        // For string values, use the raw value without quotes or any processing
        editableValue.value = props.value;
      }
      
      // Reset formatting states
      isBold.value = false;
      isItalic.value = false;
      isUnderline.value = false;
      isMonospace.value = true;
      isSansSerif.value = false;
      
      // Try to parse existing font size from the content
      const fontSizeMatch = props.value && typeof props.value === 'string' && 
                            props.value.match(/font-size:\s*(\d+)px/);
      if (fontSizeMatch && fontSizeMatch[1]) {
        fontSize.value = parseInt(fontSizeMatch[1], 10);
      } else {
        fontSize.value = 14; // Default font size
      }
      
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
    
    const finishEditValue = () => {
      let newValue = editableValue.value;
      
      // Convert value based on type
      if (valueType.value === 'number') {
        // For numbers, strip any HTML and convert to number
        const plainText = newValue.replace(/<[^>]*>/g, '');
        newValue = parseFloat(plainText);
        if (isNaN(newValue)) {
          newValue = 0;
        }
      } else if (valueType.value === 'boolean') {
        // For booleans, strip any HTML and convert to boolean
        const plainText = newValue.replace(/<[^>]*>/g, '');
        newValue = plainText.toLowerCase() === 'true';
      } else if (valueType.value === 'null') {
        newValue = null;
      }
      // String values are preserved as-is with their HTML tags
      
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
    
    // Ensure textarea stays focused and prevents the edit mode from closing
    const preserveFocus = () => {
      // Prevent any blur events from happening during the toggle
      nextTick(() => {
        if (valueInput.value && isEditingValue.value) {
          valueInput.value.focus();
        }
      });
    };
    
    // Function to handle input blur events from font size input
    const handleInputBlur = (e) => {
      // Only prevent blur if it's coming from our font size input
      if (e.target.type === 'number') {
        e.stopPropagation();
        return;
      }
      
      // For other elements, proceed with normal blur handling
      handleBlur(e);
    };
    
    // Insert a <br> tag at cursor position when Enter is pressed
    const insertLineBreak = (e) => {
      e.preventDefault(); // Prevent default enter behavior
      
      const textarea = valueInput.value;
      if (!textarea) return;
      
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      
      // Insert <br> tag at cursor position
      const before = editableValue.value.substring(0, start);
      const after = editableValue.value.substring(end);
      editableValue.value = before + '<br>' + after;
      
      // Set cursor position after the <br> tag
      nextTick(() => {
        if (textarea) {
          const newPosition = start + 4; // 4 is the length of <br>
          textarea.setSelectionRange(newPosition, newPosition);
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
    
    // Computed property to check if content has HTML formatting
    const hasHtmlFormatting = computed(() => {
      return /<[a-z][\s\S]*>/i.test(editableValue.value);
    });
    
    // Display the raw value with HTML tags visible
    const displayRawValue = computed(() => {
      if (props.value === null) return 'null';
      if (typeof props.value === 'boolean') return props.value.toString();
      if (typeof props.value === 'number') return props.value.toString();
      if (typeof props.value === 'string') {
        // Show the raw HTML string by escaping < and > characters
        return props.value
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;');
      }
      if (isArray.value) return `Array[${props.value.length}]`;
      if (isObject.value) return `Object{${Object.keys(props.value).length}}`;
      return String(props.value);
    });
    
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
      displayValueHTML,
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
      isUnderline,
      isMonospace,
      isSansSerif,
      textFormatClasses,
      toggleBold,
      toggleItalic,
      toggleUnderline,
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
      addNewItem,
      handleInputBlur,
      updateFontSize,
      applyColor,
      clearFormatting,
      fontSize,
      incrementFontSize,
      decrementFontSize,
      insertLineBreak,
      displayRawValue,
      hasHtmlFormatting
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
  gap: 8px;
  padding: 8px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  margin-top: 5px;
  margin-bottom: 10px;
}

.backend-ui .toolbar-group {
  display: flex;
  gap: 2px;
  border-right: 1px solid #e2e8f0;
  padding-right: 8px;
  margin-right: 0;
}

.backend-ui .toolbar-group:last-child {
  border-right: none;
  padding-right: 0;
}

.backend-ui .toolbar-btn {
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 3px;
  padding: 5px 10px;
  font-size: 12px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  transition: all 0.2s ease;
  min-width: 30px;
  height: 30px;
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

.backend-ui .toolbar-btn i {
  font-size: 14px;
}

/* Font size control styles */
.backend-ui .font-size-control {
  display: flex;
  align-items: center;
  gap: 5px;
}

.backend-ui .control-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.backend-ui .number-input {
  display: flex;
  align-items: center;
  border: 1px solid #cbd5e1;
  border-radius: 3px;
  overflow: hidden;
  background: #fff;
  height: 30px;
}

.backend-ui .number-input button {
  border: none;
  background: #f8fafc;
  color: #64748b;
  width: 25px;
  height: 100%;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.backend-ui .number-input button:hover {
  background: #f1f5f9;
  color: #334155;
}

.backend-ui .number-input input[type="number"] {
  border: none;
  width: 40px;
  text-align: center;
  font-size: 12px;
  height: 100%;
  padding: 0;
  -moz-appearance: textfield; /* Firefox */
}

.backend-ui .number-input input[type="number"]::-webkit-outer-spin-button,
.backend-ui .number-input input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Color button styles */
.backend-ui .color-btn i {
  font-size: 14px;
}

/* Rich text styles that can be applied */
.backend-ui .edit-input.bold {
  font-weight: bold;
}

.backend-ui .edit-input.italic {
  font-style: italic;
}

.backend-ui .edit-input.underline {
  text-decoration: underline;
}

.backend-ui .edit-input.monospace {
  font-family: 'Courier New', monospace;
}

.backend-ui .edit-input.sans-serif {
  font-family: 'Arial', sans-serif;
}

/* Add styles for formatted HTML content */
.backend-ui .node-value b {
  font-weight: bold;
}

.backend-ui .node-value i {
  font-style: italic;
}

.backend-ui .node-value u {
  text-decoration: underline;
}

.backend-ui .node-value code {
  font-family: 'Courier New', monospace;
  background-color: #f1f5f9;
  padding: 0 2px;
  border-radius: 2px;
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

/* Add styling for <br> tags in the node-value display */
.backend-ui .node-value br {
  display: block;
  content: "";
  margin: 4px 0;
}

/* Formatted preview styling */
.backend-ui .formatted-preview {
  background: #fff;
  border: 1px dashed #cbd5e1;
  border-radius: 4px;
  padding: 10px;
  margin-bottom: 10px;
  max-width: 64rem;
  overflow: auto;
  max-height: 200px;
}

.backend-ui .formatted-preview * {
  font-family: inherit;
  white-space: pre-wrap;
}
</style>