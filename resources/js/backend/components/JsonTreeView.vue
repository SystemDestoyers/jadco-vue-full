<template>
  <div class="json-tree-view">
    <div v-if="Object.keys(json).length === 0" class="empty-state">
      No data available
    </div>
    <ul v-else class="tree-root">
      <tree-node 
        v-for="(value, key) in json" 
        :key="key"
        :name="key"
        :value="value"
        :depth="0"
        @update:name="updateTopLevelKey($event, key)"
        @update:value="updateTopLevelValue($event)"
        @delete-node="deleteTopLevelNode(key)"
      />
    </ul>
    
    <!-- Add new top-level item -->
    <div class="add-top-level">
      <button v-if="!isAddingTopLevel" class="btn btn-sm btn-primary" @click="showAddTopLevelForm">
        <i class="fas fa-plus"></i> Add Item
      </button>
      
      <div v-else class="add-item-form">
        <div class="form-group">
          <input 
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
          <button class="btn btn-sm btn-primary" @click="addTopLevelItem">Add</button>
          <button class="btn btn-sm btn-secondary" @click="cancelAddTopLevel">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import TreeNode from './TreeNode.vue';
import '../assets/css/components/json-tree-view.css';

export default defineComponent({
  name: 'JsonTreeView',
  components: {
    TreeNode
  },
  props: {
    json: {
      type: Object,
      required: true,
      default: () => ({})
    }
  },
  emits: ['update:json'],
  setup(props, { emit }) {
    const isAddingTopLevel = ref(false);
    const newItemKey = ref('');
    const newItemType = ref('string');
    const newItemValue = ref('');
    
    // Update handlers for top-level keys and values
    const updateTopLevelKey = (event, oldKey) => {
      const { oldKey: _, newKey } = event;
      if (oldKey === newKey) return;
      
      const newJson = { ...props.json };
      const value = newJson[oldKey];
      delete newJson[oldKey];
      newJson[newKey] = value;
      
      emit('update:json', newJson);
    };
    
    const updateTopLevelValue = (event) => {
      const { key, value } = event;
      
      const newJson = { ...props.json };
      newJson[key] = value;
      
      emit('update:json', newJson);
    };
    
    const deleteTopLevelNode = (key) => {
      const newJson = { ...props.json };
      delete newJson[key];
      
      emit('update:json', newJson);
    };
    
    // Add top-level item
    const showAddTopLevelForm = () => {
      newItemKey.value = '';
      newItemType.value = 'string';
      newItemValue.value = '';
      isAddingTopLevel.value = true;
    };
    
    const cancelAddTopLevel = () => {
      isAddingTopLevel.value = false;
    };
    
    const addTopLevelItem = () => {
      if (newItemKey.value.trim() === '') return;
      
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
      
      const newJson = { ...props.json };
      newJson[newItemKey.value.trim()] = parsedValue;
      
      emit('update:json', newJson);
      isAddingTopLevel.value = false;
    };
    
    return {
      isAddingTopLevel,
      newItemKey,
      newItemType,
      newItemValue,
      updateTopLevelKey,
      updateTopLevelValue,
      deleteTopLevelNode,
      showAddTopLevelForm,
      cancelAddTopLevel,
      addTopLevelItem
    };
  }
});
</script> 