<template>
  <div class="notifications-container">
    <Notification
      v-for="notification in notifications"
      :key="notification.id"
      :message="notification.message"
      :type="notification.type"
      :duration="notification.duration"
      :auto-close="notification.autoClose"
      @close="removeNotification(notification.id)"
    />
  </div>
</template>

<script>
import { defineComponent, ref, provide, onMounted } from 'vue';
import Notification from './Notification.vue';

const NOTIFICATION_SYMBOL = Symbol('notifications');

export default defineComponent({
  name: 'NotificationManager',
  components: {
    Notification
  },
  setup() {
    const notifications = ref([]);
    let nextId = 0;

    const addNotification = (message, type = 'success', options = {}) => {
      const id = nextId++;
      const notification = {
        id,
        message,
        type,
        duration: options.duration || 3000,
        autoClose: options.autoClose !== false
      };
      
      notifications.value.push(notification);
      
      return id;
    };

    const removeNotification = (id) => {
      const index = notifications.value.findIndex(n => n.id === id);
      if (index !== -1) {
        notifications.value.splice(index, 1);
      }
    };

    const notificationApi = {
      success: (message, options) => addNotification(message, 'success', options),
      error: (message, options) => addNotification(message, 'error', options),
      warning: (message, options) => addNotification(message, 'warning', options),
      info: (message, options) => addNotification(message, 'info', options),
      remove: removeNotification
    };

    // Provide the notification API to all descendants
    provide(NOTIFICATION_SYMBOL, notificationApi);

    // Expose the API globally for components that can't use inject
    onMounted(() => {
      window.$notifications = notificationApi;
    });

    return {
      notifications,
      removeNotification
    };
  }
});

// Helper for components to inject the notification API
export const useNotifications = () => {
  return {
    NOTIFICATION_SYMBOL
  };
};
</script>

<style scoped>
.notifications-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none;
}

.notifications-container :deep(.notification-alert) {
  position: relative;
  top: auto;
  right: auto;
  pointer-events: auto;
}
</style> 