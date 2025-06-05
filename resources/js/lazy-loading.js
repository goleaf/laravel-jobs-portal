/**
 * Lazy Loading Implementation with Progressive Enhancement
 * Supports WebP, responsive images, and blur-to-sharp transitions
 */

class LazyImageLoader {
    constructor(options = {}) {
        this.options = {
            rootMargin: '50px',
            threshold: 0.1,
            imageSelector: '[data-lazy]',
            placeholderClass: 'lazy-placeholder',
            loadingClass: 'lazy-loading',
            loadedClass: 'lazy-loaded',
            errorClass: 'lazy-error',
            fadeTransition: true,
            progressiveLoading: true,
            ...options
        };

        this.observer = null;
        this.images = [];
        this.init();
    }

    init() {
        if (!this.isIntersectionObserverSupported()) {
            this.fallbackLoad();
            return;
        }

        this.setupObserver();
        this.findImages();
        this.observeImages();
    }

    isIntersectionObserverSupported() {
        return 'IntersectionObserver' in window;
    }

    setupObserver() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: this.options.rootMargin,
            threshold: this.options.threshold
        });
    }

    findImages() {
        this.images = document.querySelectorAll(this.options.imageSelector);
        console.log(`Found ${this.images.length} lazy images`);
    }

    observeImages() {
        this.images.forEach(img => {
            // Add placeholder class
            img.classList.add(this.options.placeholderClass);
            
            // Set up progressive loading if enabled
            if (this.options.progressiveLoading && img.dataset.placeholder) {
                this.setupProgressiveLoading(img);
            }
            
            this.observer.observe(img);
        });
    }

    setupProgressiveLoading(img) {
        // Create a canvas for the blur effect
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size
        canvas.width = 20;
        canvas.height = 20;
        
        // Create temp image to load placeholder
        const tempImg = new Image();
        tempImg.onload = () => {
            ctx.drawImage(tempImg, 0, 0, 20, 20);
            img.style.backgroundImage = `url(${canvas.toDataURL()})`;
            img.style.backgroundSize = 'cover';
            img.style.filter = 'blur(5px)';
        };
        tempImg.src = img.dataset.placeholder;
    }

    async loadImage(img) {
        img.classList.add(this.options.loadingClass);
        
        try {
            const imageData = await this.getOptimalImageSource(img);
            await this.preloadImage(imageData.src);
            
            this.applyImage(img, imageData);
            this.onImageLoaded(img);
        } catch (error) {
            console.error('Failed to load image:', error);
            this.onImageError(img);
        }
    }

    async getOptimalImageSource(img) {
        const sources = this.extractImageSources(img);
        
        // Check WebP support
        const supportsWebP = await this.checkWebPSupport();
        
        // Get responsive source based on viewport
        const responsiveSource = this.getResponsiveSource(sources);
        
        // Choose optimal format
        const optimalSource = supportsWebP && responsiveSource.webp 
            ? responsiveSource.webp 
            : responsiveSource.original;
            
        return {
            src: optimalSource,
            alt: img.dataset.alt || img.alt || '',
            sources: sources
        };
    }

    extractImageSources(img) {
        const sources = {
            original: img.dataset.src,
            webp: img.dataset.webp,
            responsive: {}
        };

        // Extract responsive sources
        const responsiveData = img.dataset.responsive;
        if (responsiveData) {
            try {
                sources.responsive = JSON.parse(responsiveData);
            } catch (e) {
                console.warn('Invalid responsive data:', responsiveData);
            }
        }

        return sources;
    }

    checkWebPSupport() {
        return new Promise((resolve) => {
            const webP = new Image();
            webP.onload = webP.onerror = () => {
                resolve(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    getResponsiveSource(sources) {
        const viewportWidth = window.innerWidth;
        const responsive = sources.responsive;
        
        // Find best matching size
        let bestMatch = { original: sources.original, webp: sources.webp };
        
        if (responsive && Object.keys(responsive).length > 0) {
            const sizes = Object.keys(responsive)
                .map(key => ({ key, width: responsive[key].width }))
                .sort((a, b) => a.width - b.width);
                
            for (const size of sizes) {
                if (viewportWidth <= size.width * 1.1) { // 10% tolerance
                    bestMatch = {
                        original: responsive[size.key].url,
                        webp: responsive[size.key].webp_url
                    };
                    break;
                }
            }
        }
        
        return bestMatch;
    }

    preloadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = resolve;
            img.onerror = reject;
            img.src = src;
        });
    }

    applyImage(img, imageData) {
        // Set src
        img.src = imageData.src;
        img.alt = imageData.alt;
        
        // Remove loading attributes
        delete img.dataset.src;
        delete img.dataset.webp;
        delete img.dataset.responsive;
        delete img.dataset.placeholder;
        
        // Apply fade transition
        if (this.options.fadeTransition) {
            img.style.transition = 'opacity 0.3s ease-in-out, filter 0.3s ease-in-out';
            img.style.opacity = '0';
            img.style.filter = 'blur(5px)';
            
            setTimeout(() => {
                img.style.opacity = '1';
                img.style.filter = 'none';
                img.style.backgroundImage = 'none';
            }, 50);
        }
    }

    onImageLoaded(img) {
        img.classList.remove(this.options.loadingClass);
        img.classList.remove(this.options.placeholderClass);
        img.classList.add(this.options.loadedClass);
        
        // Dispatch custom event
        img.dispatchEvent(new CustomEvent('lazyImageLoaded', {
            detail: { img }
        }));
    }

    onImageError(img) {
        img.classList.remove(this.options.loadingClass);
        img.classList.add(this.options.errorClass);
        
        // Fallback to original src if available
        if (img.dataset.fallback) {
            img.src = img.dataset.fallback;
        }
        
        console.error('Image failed to load:', img.dataset.src);
    }

    fallbackLoad() {
        // Fallback for browsers without IntersectionObserver
        this.images.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                this.onImageLoaded(img);
            }
        });
    }

    // Public methods
    refresh() {
        this.findImages();
        this.observeImages();
    }

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.lazyLoader = new LazyImageLoader({
        rootMargin: '100px',
        threshold: 0.1,
        progressiveLoading: true
    });
});

// Re-initialize on dynamic content changes
document.addEventListener('contentChanged', () => {
    if (window.lazyLoader) {
        window.lazyLoader.refresh();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyImageLoader;
} 