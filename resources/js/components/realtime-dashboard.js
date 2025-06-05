document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
// Global configuration for the dashboard
    window.APP_CONFIG = {
        websocket_url: '{{ config("broadcasting.connections.pusher.options.host","localhost:6001") }}',
        user_type: '{{ auth()->user()->user_type }}',
        user_id: {{ auth()->user()->id }},
        csrf_token: '{{ csrf_token() }}'
    };


});