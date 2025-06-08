/**
 * Universal UI Enhancement System
 * Modern TailwindCSS Components with Dark Mode Support
 */

class UniversalUISystem {
    constructor() {
        this.darkMode = this.initializeDarkMode();
        this.init();
    }

    init() {
        this.setupDarkModeToggle();
        this.setupAccessibility();
        this.setupAnimations();
        this.initializeComponents();
        console.log('🎨 Universal UI System initialized');
    }

    // Dark Mode Management
    initializeDarkMode() {
        const theme = localStorage.theme || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        if (theme === 'dark' || (!('theme' in localStorage) && 
            window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            return true;
        } else {
            document.documentElement.classList.remove('dark');
            return false;
        }
    }

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        }
        
        this.updateThemeIcon();
        this.dispatchThemeChange();
    }

    setupDarkModeToggle() {
        const toggles = document.querySelectorAll('[data-theme-toggle]');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', () => this.toggleDarkMode());
        });
        this.updateThemeIcon();
    }

    updateThemeIcon() {
        const toggles = document.querySelectorAll('[data-theme-toggle]');
        toggles.forEach(toggle => {
            const sunIcon = toggle.querySelector('.sun-icon');
            const moonIcon = toggle.querySelector('.moon-icon');
            
            if (this.darkMode) {
                if (sunIcon) sunIcon.classList.remove('hidden');
                if (moonIcon) moonIcon.classList.add('hidden');
            } else {
                if (sunIcon) sunIcon.classList.add('hidden');
                if (moonIcon) moonIcon.classList.remove('hidden');
            }
        });
    }

    dispatchThemeChange() {
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: this.darkMode ? 'dark' : 'light' }
        }));
    }

    // Component Initialization
    initializeComponents() {
        this.setupButtons();
        this.setupModals();
        this.setupDropdowns();
        this.setupForms();
        this.setupNavigation();
        this.setupCards();
        this.setupTables();
    }

    setupButtons() {
        // Ripple effect for buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-ripple')) {
                this.createRipple(e);
            }
        });

        // Loading states
        document.querySelectorAll('[data-loading]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.setButtonLoading(btn, true);
            });
        });
    }

    createRipple(e) {
        const button = e.target.closest('.btn-ripple');
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        button.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }

    setButtonLoading(button, loading) {
        if (loading) {
            button.classList.add('loading');
            button.disabled = true;
            const spinner = button.querySelector('.loading-spinner');
            if (spinner) spinner.classList.remove('hidden');
        } else {
            button.classList.remove('loading');
            button.disabled = false;
            const spinner = button.querySelector('.loading-spinner');
            if (spinner) spinner.classList.add('hidden');
        }
    }

    setupModals() {
        // Modal triggers
        document.querySelectorAll('[data-modal-target]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = trigger.getAttribute('data-modal-target');
                this.openModal(targetId);
            });
        });

        // Close handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-close]') || 
                e.target.closest('[data-modal-close]')) {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal);
            }
        });

        // Escape key handler
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) this.closeModal(activeModal);
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            modal.classList.remove('hidden');
            document.body.classList.add('modal-open');
            
            // Focus management
            const firstFocusable = modal.querySelector('input, button, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) firstFocusable.focus();
        }
    }

    closeModal(modal) {
        if (typeof modal === 'string') {
            modal = document.getElementById(modal);
        }
        
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
            document.body.classList.remove('modal-open');
        }
    }

    setupDropdowns() {
        document.querySelectorAll('[data-dropdown-toggle]').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const targetId = toggle.getAttribute('data-dropdown-toggle');
                this.toggleDropdown(targetId);
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown.active').forEach(dropdown => {
                this.closeDropdown(dropdown);
            });
        });
    }

    toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            dropdown.classList.toggle('active');
            dropdown.classList.toggle('hidden');
        }
    }

    closeDropdown(dropdown) {
        if (typeof dropdown === 'string') {
            dropdown = document.getElementById(dropdown);
        }
        
        if (dropdown) {
            dropdown.classList.remove('active');
            dropdown.classList.add('hidden');
        }
    }

    setupForms() {
        // Form validation
        document.querySelectorAll('form[data-validate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });

        // Floating labels
        document.querySelectorAll('.floating-label input, .floating-label textarea').forEach(input => {
            const updateLabel = () => {
                const label = input.nextElementSibling;
                if (label && label.tagName === 'LABEL') {
                    if (input.value || input === document.activeElement) {
                        label.classList.add('floating');
                    } else {
                        label.classList.remove('floating');
                    }
                }
            };

            input.addEventListener('focus', updateLabel);
            input.addEventListener('blur', updateLabel);
            input.addEventListener('input', updateLabel);
            updateLabel();
        });

        // File upload previews
        document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFilePreview(e.target);
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }
        
        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
        }
        
        this.toggleFieldError(field, !isValid);
        return isValid;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    toggleFieldError(field, hasError) {
        const container = field.closest('.form-group') || field.parentElement;
        
        if (hasError) {
            field.classList.add('border-red-500', 'focus:ring-red-500');
            field.classList.remove('border-gray-300', 'focus:ring-blue-500');
            container.classList.add('error');
        } else {
            field.classList.remove('border-red-500', 'focus:ring-red-500');
            field.classList.add('border-gray-300', 'focus:ring-blue-500');
            container.classList.remove('error');
        }
    }

    handleFilePreview(input) {
        const file = input.files[0];
        const previewId = input.getAttribute('data-preview');
        const preview = document.getElementById(previewId);
        
        if (file && preview) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (file.type.startsWith('image/')) {
                    preview.innerHTML = `<img src="${e.target.result}" class="max-w-full h-auto rounded">`;
                } else {
                    preview.innerHTML = `<div class="file-info">${file.name} (${this.formatFileSize(file.size)})</div>`;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    setupNavigation() {
        // Mobile menu toggle
        const toggles = document.querySelectorAll('[data-mobile-menu-toggle]');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const menu = document.querySelector('[data-mobile-menu]');
                if (menu) {
                    menu.classList.toggle('hidden');
                    toggle.classList.toggle('active');
                }
            });
        });

        // Navbar scroll behavior
        let lastScroll = 0;
        const navbar = document.querySelector('.navbar-scroll');
        
        if (navbar) {
            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                if (currentScroll > lastScroll && currentScroll > 200) {
                    navbar.classList.add('nav-hidden');
                } else {
                    navbar.classList.remove('nav-hidden');
                }
                
                lastScroll = currentScroll;
            });
        }

        // Active states
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }

    setupCards() {
        // Hover effects
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('shadow-lg', 'scale-105');
            });
            
            card.addEventListener('mouseleave', () => {
                card.classList.remove('shadow-lg', 'scale-105');
            });
        });

        // Expandable cards
        document.querySelectorAll('[data-expandable]').forEach(card => {
            const trigger = card.querySelector('[data-expand-trigger]');
            const content = card.querySelector('[data-expand-content]');
            
            if (trigger && content) {
                trigger.addEventListener('click', () => {
                    content.classList.toggle('hidden');
                    trigger.classList.toggle('expanded');
                });
            }
        });
    }

    setupTables() {
        // Responsive table handling
        document.querySelectorAll('.table-responsive').forEach(table => {
            if (table.scrollWidth > table.clientWidth) {
                table.classList.add('overflow-x-auto');
            }
        });

        // Row selection
        document.querySelectorAll('input[data-select-all]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const targetSelector = checkbox.getAttribute('data-select-all');
                const checkboxes = document.querySelectorAll(targetSelector);
                checkboxes.forEach(cb => {
                    cb.checked = checkbox.checked;
                });
            });
        });
    }

    // Accessibility
    setupAccessibility() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('user-is-tabbing');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('user-is-tabbing');
        });
    }

    // Animations
    setupAnimations() {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            },
            { threshold: 0.1 }
        );

        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });

        this.animateCounters();
        this.animateProgressBars();
    }

    animateCounters() {
        document.querySelectorAll('[data-counter]').forEach(counter => {
            const target = parseInt(counter.getAttribute('data-counter'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString();
            }, 16);
        });
    }

    animateProgressBars() {
        document.querySelectorAll('[data-progress]').forEach(bar => {
            const progress = bar.getAttribute('data-progress');
            const fill = bar.querySelector('.progress-fill');
            
            if (fill) {
                setTimeout(() => {
                    fill.style.width = progress + '%';
                }, 100);
            }
        });
    }

    // Utility methods
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="flex-1">${message}</span>
                <button class="ml-2 text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, duration);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.UniversalUI = new UniversalUISystem();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UniversalUISystem;
}
