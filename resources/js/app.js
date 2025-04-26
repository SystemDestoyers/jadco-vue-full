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
    console.log('Setting Authorization header:', bearerToken.substring(0, 20) + '...');
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
    console.log(`Navigation attempt: ${from.path} → ${to.path}`);
    console.log('To meta:', to.meta);
    
    if (to.matched.some(record => record.meta.requiresAuth)) {
        try {
            // Check if there's a token
            const token = localStorage.getItem('auth_token');
            console.log('Token exists:', !!token);
            
            if (!token) {
                // No token found, redirect to login
                console.log('No token found, redirecting to login');
                next({ 
                    path: '/admin/login', 
                    query: { redirect: to.fullPath } 
                });
                return;
            }
            
            console.log('Setting Authorization header:', `Bearer ${token.substring(0, 10)}...`);
            // Ensure the token is set in the headers for this request
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            // Use debug endpoint to get more info
            const response = await axios.get('/api/admin/debug-auth');
            console.log('Auth check response:', response.data);
            
            if (response.data && response.data.authenticated) {
                // User is authenticated, proceed
                console.log('Authentication successful, proceeding to:', to.path);
                next();
            } else {
                // User is not authenticated, redirect to login
                console.log('Authentication failed, redirecting to login');
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
                
                next({ 
                    path: '/admin/login', 
                    query: { redirect: to.fullPath } 
                });
            }
        } catch (error) {
            console.error('Auth check error:', error);
            if (error.response) {
                console.error('Error response:', error.response.data);
                console.error('Status:', error.response.status);
                console.error('Headers:', error.response.headers);
            }
            
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
        console.log('Route is not protected, proceeding normally');
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