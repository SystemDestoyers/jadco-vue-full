import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './routes';
import axios from 'axios';
import Vue3Toastify, { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// Configure axios
axios.defaults.withCredentials = true;

// Set up token from localStorage if it exists
const token = localStorage.getItem('auth_token');
if (token) {
    // Make sure the token is in the proper format
    const bearerToken = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
    axios.defaults.headers.common['Authorization'] = bearerToken;
}

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
            // Check if there's a token
            const token = localStorage.getItem('auth_token');
            
            if (!token) {
                // No token found, redirect to login
                next({ 
                    path: '/admin/login', 
                    query: { redirect: to.fullPath } 
                });
                return;
            }
            
            // Ensure the token is set in the headers for this request
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            // Use debug endpoint to get more info
            const response = await axios.get('/api/admin/debug-auth');
            
            if (response.data && response.data.authenticated) {
                // User is authenticated, proceed
                next();
            } else {
                // User is not authenticated, redirect to login
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
                
                next({ 
                    path: '/admin/login', 
                    query: { redirect: to.fullPath } 
                });
            }
        } catch (error) {
            // Error checking authentication, redirect to login
            localStorage.removeItem('auth_token');
            delete axios.defaults.headers.common['Authorization'];
            
            next({ 
                path: '/admin/login', 
                query: { redirect: to.fullPath } 
            });
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