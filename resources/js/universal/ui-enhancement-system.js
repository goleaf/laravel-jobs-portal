/**
 * Universal UI Enhancement System
 * Modern TailwindCSS Components with Dark Mode Support
 */

class UniversalUISystem {
    constructor() {
        this.darkMode = this.initializeDarkMode();
        this.components = new Map();
        this.init();
    }

    init() {
        this.setupDarkModeToggle();
        this.initializeComponents();
        this.setupAccessibility();
        this.setupAnimations();
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
        
        this.dispatchThemeChange();
    }

    setupDarkModeToggle() {
        const toggles = document.querySelectorAll('[data-theme-toggle]');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', () => this.toggleDarkMode());
        });
    }

    dispatchThemeChange() {
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: this.darkMode ? 'dark' : 'light' }
        }));
    }

    // Component Management
    initializeComponents() {
        this.registerComponent('button', new ButtonComponent());
        this.registerComponent('card', new CardComponent());
        this.registerComponent('modal', new ModalComponent());
        this.registerComponent('dropdown', new DropdownComponent());
        this.registerComponent('form', new FormComponent());
        this.registerComponent('navigation', new NavigationComponent());
        this.registerComponent('dashboard', new DashboardComponent());
    }

    registerComponent(name, component) {
        this.components.set(name, component);
        component.init();
    }

    getComponent(name) {
        return this.components.get(name);
    }

    // Accessibility
    setupAccessibility() {
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('user-is-tabbing');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('user-is-tabbing');
        });

        // Focus management
        this.setupFocusManagement();
    }

    setupFocusManagement() {
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    this.getComponent('modal').close(activeModal);
                }
            }
        });
    }

    // Animations
    setupAnimations() {
        // Intersection Observer for scroll animations
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
    }
}

// Component Classes
class ButtonComponent {
    init() {
        this.setupRippleEffect();
        this.setupLoadingStates();
    }

    setupRippleEffect() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-ripple')) {
                this.createRipple(e);
            }
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

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    setupLoadingStates() {
        document.querySelectorAll('[data-loading]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.setLoading(btn, true);
            });
        });
    }

    setLoading(button, loading) {
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
}

class CardComponent {
    init() {
        this.setupHoverEffects();
        this.setupExpandableCards();
    }

    setupHoverEffects() {
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('shadow-lg', 'scale-105');
            });
            
            card.addEventListener('mouseleave', () => {
                card.classList.remove('shadow-lg', 'scale-105');
            });
        });
    }

    setupExpandableCards() {
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
}

class ModalComponent {
    init() {
        this.setupTriggers();
        this.setupCloseHandlers();
    }

    setupTriggers() {
        document.querySelectorAll('[data-modal-target]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = trigger.getAttribute('data-modal-target');
                this.open(targetId);
            });
        });
    }

    setupCloseHandlers() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-close]') || 
                e.target.closest('[data-modal-close]')) {
                const modal = e.target.closest('.modal');
                if (modal) this.close(modal);
            }
        });
    }

    open(modalId) {
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

    close(modal) {
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
}

class DropdownComponent {
    init() {
        this.setupDropdowns();
        this.setupOutsideClick();
    }

    setupDropdowns() {
        document.querySelectorAll('[data-dropdown-toggle]').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const targetId = toggle.getAttribute('data-dropdown-toggle');
                this.toggle(targetId);
            });
        });
    }

    setupOutsideClick() {
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown.active').forEach(dropdown => {
                this.close(dropdown);
            });
        });
    }

    toggle(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            dropdown.classList.toggle('active');
            dropdown.classList.toggle('hidden');
        }
    }

    close(dropdown) {
        if (typeof dropdown === 'string') {
            dropdown = document.getElementById(dropdown);
        }
        
        if (dropdown) {
            dropdown.classList.remove('active');
            dropdown.classList.add('hidden');
        }
    }
}

class FormComponent {
    init() {
        this.setupValidation();
        this.setupFloatingLabels();
        this.setupFileUploads();
    }

    setupValidation() {
        document.querySelectorAll('form[data-validate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
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
        
        // Required validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }
        
        // Email validation
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
            field.classList.add('border-danger-500', 'focus:ring-danger-500');
            field.classList.remove('border-gray-300', 'focus:ring-primary-500');
            container.classList.add('error');
        } else {
            field.classList.remove('border-danger-500', 'focus:ring-danger-500');
            field.classList.add('border-gray-300', 'focus:ring-primary-500');
            container.classList.remove('error');
        }
    }

    setupFloatingLabels() {
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
            
            // Initial state
            updateLabel();
        });
    }

    setupFileUploads() {
        document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFilePreview(e.target);
            });
        });
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
}

class NavigationComponent {
    init() {
        this.setupMobileMenu();
        this.setupScrollBehavior();
        this.setupActiveStates();
    }

    setupMobileMenu() {
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
    }

    setupScrollBehavior() {
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
                
                // Hide/show on scroll
                if (currentScroll > lastScroll && currentScroll > 200) {
                    navbar.classList.add('nav-hidden');
                } else {
                    navbar.classList.remove('nav-hidden');
                }
                
                lastScroll = currentScroll;
            });
        }
    }

    setupActiveStates() {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }
}

class DashboardComponent {
    init() {
        this.setupMetrics();
        this.setupCharts();
        this.setupRealtime();
    }

    setupMetrics() {
        this.animateCounters();
        this.setupProgressBars();
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

    setupProgressBars() {
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

    setupCharts() {
        // Chart initialization would go here
        // Could integrate with Chart.js or similar library
    }

    setupRealtime() {
        // Real-time updates setup
        if (window.Echo) {
            // Laravel Echo integration for real-time updates
        }
    }
}

// Initialize the UI system when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.UniversalUI = new UniversalUISystem();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UniversalUISystem;
} 