document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordToggle.className = 'fas fa-eye';
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);


});