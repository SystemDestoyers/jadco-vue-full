<template>
  <div class="json-editor">
    <div v-if="error" class="json-error">
      {{ error }}
    </div>
    <textarea 
      ref="textarea"
      v-model="internalValue"
      class="form-control json-textarea"
      :rows="rows" 
      @input="validateAndUpdate"
      @blur="formatOnBlur"
      :placeholder="placeholder"></textarea>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 6
  },
  placeholder: {
    type: String,
    default: 'Enter JSON data'
  }
});

const emit = defineEmits(['update:modelValue', 'error']);

const internalValue = ref(props.modelValue);
const error = ref('');
const textarea = ref(null);

// Format JSON nicely
const formatJson = (jsonString) => {
  try {
    // Parse and format the JSON
    const parsed = JSON.parse(jsonString);
    return JSON.stringify(parsed, null, 2);
  } catch (e) {
    // If not valid JSON, return the original
    return jsonString;
  }
};

// Validate and update the model value
const validateAndUpdate = () => {
  try {
    // Try to parse the JSON to validate it
    JSON.parse(internalValue.value);
    error.value = '';
    emit('error', '');
    emit('update:modelValue', internalValue.value);
  } catch (e) {
    error.value = 'Invalid JSON: ' + e.message;
    emit('error', error.value);
  }
};

// Format JSON when input loses focus
const formatOnBlur = () => {
  try {
    internalValue.value = formatJson(internalValue.value);
    error.value = '';
    emit('error', '');
    emit('update:modelValue', internalValue.value);
  } catch (e) {
    // Don't change value if format fails
  }
};

// Watch for external changes to model value
watch(() => props.modelValue, (newValue) => {
  if (newValue !== internalValue.value) {
    internalValue.value = newValue;
  }
});

// Initial format if provided value is JSON
onMounted(() => {
  if (props.modelValue) {
    try {
      internalValue.value = formatJson(props.modelValue);
    } catch (e) {
      // Leave as is if format fails
    }
  }
});
</script>

<style scoped>
.json-editor {
  display: flex;
  flex-direction: column;
}

.json-textarea {
  font-family: monospace;
  min-height: 100px;
  white-space: pre;
  resize: vertical;
}

.json-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

[data-color-mode="dark"] .json-textarea {
  background-color: #2d3238;
  color: #e9ecef;
  border-color: #343a40;
}

[data-color-mode="dark"] .json-error {
  color: #ea868f;
}
</style> 