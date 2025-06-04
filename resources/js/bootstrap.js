import axios from 'axios';

// Set up CSRF token
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Set up request/response interceptors
axios.interceptors.request.use(function (config) {
    // Show loading indicator if needed
    return config;
}, function (error) {
    return Promise.reject(error);
});

axios.interceptors.response.use(function (response) {
    // Hide loading indicator if needed
    return response;
}, function (error) {
    if (error.response && error.response.status === 419) {
        // CSRF token mismatch
        window.location.reload();
    }
    return Promise.reject(error);
});

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

// Echo setup (if using Laravel Echo)
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });