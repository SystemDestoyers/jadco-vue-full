<template>
  <li class="tree-node" :style="{ paddingLeft: `${depth * 20}px` }">
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
      <div v-if="isPrimitive" class="node-value-container">
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

export default defineComponent({
  name: 'TreeNode',
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
      
      isEditingValue.value = true;
      nextTick(() => {
        valueInput.value.focus();
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
      valueType,
      displayValue,
      toggleExpand,
      startEditKey,
      finishEditKey,
      cancelEditKey,
      startEditValue,
      finishEditValue,
      cancelEditValue,
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

<style scoped>
.tree-node {
  list-style-type: none;
  margin: 0;
  padding: 2px 0;
}

.node-header {
  display: flex;
  align-items: center;
  padding: 4px 8px;
  border-radius: 4px;
}

.node-header:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.node-toggle {
  cursor: pointer;
  margin-right: 8px;
}

.node-toggle i {
  width: 10px;
  text-align: center;
  font-size: 12px;
}

i.expandable {
  color: #3498db;
}

i.leaf {
  color: #95a5a6;
  font-size: 6px;
}

.node-key-container, .node-value-container {
  position: relative;
}

.node-key {
  font-weight: 600;
  margin-right: 8px;
  color: #2c3e50;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 2px;
}

.node-key:hover {
  background-color: rgba(52, 152, 219, 0.1);
}

.node-value {
  color: #7f8c8d;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 2px;
}

.node-value:hover {
  background-color: rgba(52, 152, 219, 0.1);
}

.node-type-indicator {
  color: #95a5a6;
  font-style: italic;
  cursor: pointer;
}

.node-value.string {
  color: #27ae60;
}

.node-value.number {
  color: #e67e22;
}

.node-value.boolean {
  color: #8e44ad;
}

.node-value.null {
  color: #95a5a6;
  font-style: italic;
}

.node-children {
  padding-left: 0;
}

.edit-input {
  border: 1px solid #ddd;
  border-radius: 2px;
  padding: 1px 3px;
  font-size: 14px;
  font-family: monospace;
  min-width: 100px;
}

.node-actions {
  margin-left: auto;
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s;
}

.node-header:hover .node-actions {
  opacity: 1;
}

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 2px 4px;
  font-size: 12px;
  border-radius: 2px;
  color: #95a5a6;
}

.action-btn:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.add-btn:hover {
  color: #27ae60;
}

.delete-btn:hover {
  color: #e74c3c;
}

.add-item-form {
  margin-left: 20px;
  margin-top: 4px;
  padding: 8px;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 4px;
}

.form-group {
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  padding: 4px 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-actions {
  display: flex;
  gap: 8px;
}

.btn {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
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
</style> 