<template>
  <div v-if="show" :class="['notification-alert', `notification-${type}`]">
    <div class="notification-content">
      <span>{{ message }}</span>
      <button @click="close" class="notification-close">&times;</button>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, onBeforeUnmount } from 'vue';

export default defineComponent({
  name: 'Notification',
  props: {
    message: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'success',
      validator: value => ['success', 'error', 'warning', 'info'].includes(value)
    },
    duration: {
      type: Number,
      default: 3000
    },
    autoClose: {
      type: Boolean,
      default: true
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const show = ref(true);
    let timeout = null;

    const close = () => {
      show.value = false;
      emit('close');
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
    };

    onMounted(() => {
      if (props.autoClose && props.duration > 0) {
        timeout = setTimeout(() => {
          close();
        }, props.duration);
      }
    });

    onBeforeUnmount(() => {
      if (timeout) {
        clearTimeout(timeout);
      }
    });

    return {
      show,
      close
    };
  }
});
</script>

<style scoped>
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
</style> 