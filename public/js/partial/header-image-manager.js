/**
 * Header Image Manager
 * 
 * This script centralizes header-image animation to prevent multiple initializations
 * between different components trying to animate the header-image.
 * It ensures the animation runs only once.
 */

window.HeaderImageManager = {
    initialized: false,
    initializing: false,
    animationInProgress: false,
    
    // Initialize header image animation - this should be called only once
    init() {
        if (this.initialized || this.initializing) {
            return;
        }
        
        this.initializing = true;
        
        const headerImage = document.querySelector('.header-image');
        if (!headerImage) {
            this.initializing = false;
            return;
        }
        
        // Check if animation is already in progress
        const hasAnimationClass = headerImage.classList.contains('animate');
        
        if (hasAnimationClass) {
            // Apply final state directly to ensure animation completes
            setTimeout(() => {
                headerImage.style.opacity = '1';
                headerImage.style.transform = 'translateX(0)';
                
                this.initialized = true;
                this.initializing = false;
            }, 1000);
            
            return;
        }
        
        // Fresh animation
        
        // Set a flag to prevent double animations
        this.animationInProgress = true;
        
        // Remove any existing animate class
        if (headerImage.classList.contains('animate')) {
            headerImage.classList.remove('animate');
        }
        
        // Force reflow
        void headerImage.offsetWidth;
        
        // Set up properties for animation
        headerImage.style.opacity = '0';
        headerImage.style.transform = 'translateX(80px)';
        
        // Add the animate class after a short delay
        setTimeout(() => {
            headerImage.classList.add('animate');
            
            // Track animation completion
            headerImage.addEventListener('animationend', () => {
                // Ensure final state is applied
                headerImage.style.opacity = '1';
                headerImage.style.transform = 'translateX(0)';
                
                this.animationInProgress = false;
            }, { once: true });
            
            // Mark as initialized after animation duration
            setTimeout(() => {
                this.initialized = true;
                this.initializing = false;
            }, 1000);
        }, 50);
    },
    
    // Reset the manager for new page loads
    reset() {
        this.initialized = false;
        this.initializing = false;
        this.animationInProgress = false;
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        window.HeaderImageManager.init();
    }, 100);
});

// Initialize on window load as well (backup)
window.addEventListener('load', () => {
    if (!window.HeaderImageManager.initialized && !window.HeaderImageManager.initializing) {
        window.HeaderImageManager.init();
    }
});

// Provide a global reset function
window.resetHeaderImageManager = () => {
    window.HeaderImageManager.reset();
}; 