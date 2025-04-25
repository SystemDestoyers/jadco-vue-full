import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './routes';
import axios from 'axios';
import Vue3Toastify, { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// Configure axios
axios.defaults.withCredentials = true;

// Create Vue Router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        if (to.hash) {
            return {
                el: to.hash,
                behavior: 'smooth'
            };
        }
        return { top: 0 };
    }
});

// Add navigation guard for protected routes
router.beforeEach(async (to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        try {
            // Check if user is authenticated
            const response = await axios.get('/admin-auth/user');
            if (response.data) {
                // User is authenticated
                next();
            } else {
                // Redirect to login page
                next({ path: '/admin/login', query: { redirect: to.fullPath } });
            }
        } catch (error) {
            // Error or unauthorized, redirect to login
            next({ path: '/admin/login', query: { redirect: to.fullPath } });
        }
    } else {
        // Route is not protected
        next();
    }
});

// Create and mount the Vue app
const app = createApp(App);

// Configure Vue3Toastify
app.use(Vue3Toastify, {
    autoClose: 3000,
    position: "top-right",
    closeButton: true,
});

// Register global mixins
app.mixin({
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        }
    }
});

app.use(router);
app.mount('#app'); 