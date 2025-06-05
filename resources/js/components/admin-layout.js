document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }


});