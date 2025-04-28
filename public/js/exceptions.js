/**
 * JADCO Exceptions JS
 * 
 * Dynamic font size scaling for content edited with the JSON editor.
 * When a font size is set in the backend editor, it will automatically scale
 * proportionally across all screen sizes based on the breakpoints in responsive.css.
 * 
 * NOTE FOR VUE APPLICATIONS:
 * - This script needs to be explicitly imported in the Vue entry point (welcome.blade.php)
 * - For Vue components, ensure this script runs AFTER Vue has mounted and rendered its components
 * - Vue components using inline font-size styles will be processed by this script
 * - Text rendered through v-html with inline font-size will be processed
 * - For Vue-specific content, consider using the MutationObserver which catches dynamically added content
 */

document.addEventListener('DOMContentLoaded', function() {
    // First pass - Process all existing content
    processAllContent();
    
    // Set up observer for dynamic content
    setupFrontendMutationObserver();
    
    // Handle resize events
    window.addEventListener('resize', function() {
        updateScaledFontSizes();
    });
});

/**
 * Process all website content for responsive font sizing
 * This function scans the document for elements that may have font-size styles
 * and processes them for responsive sizing.
 */
function processAllContent() {
    // Process all database-sourced sections
    const sectionSelectors = [
        // Header section
        '.heading-text', 
        '.main-heading', 
        '.main-heading span',
        '.services-menu h3', 
        '.service-list li',
        '.service-list li a',
        '.btn-talk',
        
        // About section
        '.about-heading h2',
        '.about-text',
        '.about-description',
        '.about-main-description',
        
        // Services sections
        '.service-content .section-title',
        '.service-content .service-number',
        '.service-title',
        '.service-description',
        '.service-name',
        '.service-desc',
        '.service-num',
        '.service-toggle',
        '.service-content-wrapper .learn-more',
        '.service-buttons .learn-more',
        '.service-text-content h2',
        '.service-text-content p',
        '.service-text-content li',
        
        // Educational services
        '.edu-services-title',
        
        // Contact section
        '.contact-tagline',
        '.let-talk',
        '.location-title',
        '.location-address',
        '.contact-label',
        '.contact-value',
        '.social-link',
        '.copyright',
        
        // General elements that might contain dynamic content
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'p', 'li', 'span', 'a', 'button',
        '.section-title'
    ];
    
    // First, process text elements by selector
    const allContentElements = document.querySelectorAll(sectionSelectors.join(', '));
    processElements(allContentElements);
    
    // Second, do a global search for any element with inline font-size
    const elementsWithFontSize = document.querySelectorAll('[style*="font-size"]');
    processElements(elementsWithFontSize);
}

/**
 * Process a collection of elements for responsive font sizing
 * @param {NodeList} elements - Elements to process
 */
function processElements(elements) {
    elements.forEach(el => {
        processElementAndChildren(el);
    });
}

/**
 * Process an element and all its children for font-size styles
 * This is particularly useful for v-html content in Vue components
 * which may contain nested elements with font-size styles.
 * 
 * @param {Element} element - The element to process
 */
function processElementAndChildren(element) {
    // Check the element itself for inline font-size
    processFontSize(element);
    
    // Then check all its children
    const children = element.querySelectorAll('*');
    children.forEach(child => {
        processFontSize(child);
    });
}

/**
 * Process a single element for font-size style
 * This is the core function that handles the dynamic font sizing logic.
 * 
 * @param {Element} element - The element to process
 */
function processFontSize(element) {
    // Check if element has inline style with font-size
    const style = element.getAttribute('style');
    
    if (style && style.includes('font-size')) {
        // Get computed style to get the numeric value
        const computedStyle = window.getComputedStyle(element);
        const fontSize = computedStyle.fontSize;
        const originalSize = parseFloat(fontSize);
        
        if (!isNaN(originalSize)) {
            // Skip if already processed
            if (element.classList.contains('dynamic-font-size')) {
                return;
            }
            
            // Store the original size
            element.setAttribute('data-original-font-size', originalSize);
            
            // Remove font-size from inline style
            removeInlineFontSize(element);
            
            // Mark as processed
            element.classList.add('dynamic-font-size');
            
            // Apply responsive size based on viewport
            updateElementFontSize(element, originalSize);
        }
    }
}

/**
 * Removes only the font-size property from an element's inline style
 * This preserves other inline styles that may be important.
 * 
 * @param {Element} element - The element to process
 */
function removeInlineFontSize(element) {
    const style = element.getAttribute('style');
    if (!style) return;
    
    // Create a new style string without font-size
    let newStyle = '';
    const styleProps = style.split(';');
    
    for (let prop of styleProps) {
        if (prop.trim() !== '' && !prop.trim().toLowerCase().startsWith('font-size')) {
            newStyle += prop + ';';
        }
    }
    
    if (newStyle) {
        element.setAttribute('style', newStyle);
    } else {
        element.removeAttribute('style');
    }
}

/**
 * Update a specific element's font size based on the viewport width
 * This applies responsive scaling based on the viewport size.
 * 
 * @param {Element} element - The element to update
 * @param {number} originalSize - Original font size in pixels
 */
function updateElementFontSize(element, originalSize) {
    const viewportWidth = window.innerWidth;
    
    // Special case for mobile: completely remove custom font sizes for viewport width < 576px
    if (viewportWidth <= 576) {
        // Remove inline font-size to let the element use default responsive styling
        element.style.removeProperty('font-size');
        return;
    }
    
    // For other viewport sizes, use scaling factors
    let scaleFactor = 1; // Default scale factor
    
    // Match breakpoints from responsive.css
    if (viewportWidth <= 768) {
        // Mobile - larger phones/small tablets
        scaleFactor = 0.50;
    } else if (viewportWidth <= 992) {
        // Tablets/small laptops
        scaleFactor = 0.50;
    } else if (viewportWidth <= 1200) {
        // Small desktops/laptops
        scaleFactor = 0.60;
    } else if (viewportWidth <= 1366) {
        // Medium desktops
        scaleFactor = 0.65;
    } else if (viewportWidth <= 1440) {
        // Larger desktops
        scaleFactor = 0.70;
    } else if (viewportWidth <= 1600) {
        // Large desktops
        scaleFactor = 0.80;
    } else if (viewportWidth <= 1920) {
        // Extra large desktops
        scaleFactor = 0.95;
    }
    
    // Special handling for headings to ensure minimum readability
    const tagName = element.tagName.toLowerCase();
    let minSize = 10; // Default minimum size
    
    if (tagName === 'h1' || element.classList.contains('main-heading')) {
        minSize = 16;
    } else if (tagName === 'h2' || element.classList.contains('section-title')) {
        minSize = 14;
    } else if (tagName === 'h3') {
        minSize = 12;
    }
    
    // Calculate scaled size but ensure it doesn't go below minimum
    const scaledSize = Math.max(originalSize * scaleFactor, minSize);
    
    // Apply the scaled font size
    element.style.fontSize = `${scaledSize}px`;
}

/**
 * Update all dynamic font sizes based on current viewport width
 * Called when the window is resized.
 */
function updateScaledFontSizes() {
    const dynamicElements = document.querySelectorAll('.dynamic-font-size');
    
    dynamicElements.forEach(el => {
        const originalSize = parseFloat(el.getAttribute('data-original-font-size'));
        if (!isNaN(originalSize)) {
            updateElementFontSize(el, originalSize);
        }
    });
}

/**
 * Set up MutationObserver to catch dynamically added content
 * This is crucial for Vue applications where content is dynamically rendered
 * or updated after initial load. Examples include content loaded via AJAX
 * or content updated by Vue reactivity.
 */
function setupFrontendMutationObserver() {
    const observer = new MutationObserver(function(mutations) {
        let hasNewNodes = false;
        let hasStyleChanges = false;
        
        mutations.forEach(function(mutation) {
            // Check for new nodes
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                hasNewNodes = true;
            }
            
            // Check for style attribute changes
            if (mutation.type === 'attributes' && 
                mutation.attributeName === 'style' &&
                mutation.target.getAttribute('style') &&
                mutation.target.getAttribute('style').includes('font-size')) {
                
                hasStyleChanges = true;
                processFontSize(mutation.target);
            }
        });
        
        // Process new content if needed
        if (hasNewNodes) {
            // Wait a bit to make sure DOM is fully updated
            setTimeout(function() {
                processAllContent();
            }, 100);
        }
    });
    
    // Observe the entire document for changes
    observer.observe(document.body, { 
        childList: true, 
        subtree: true,
        attributes: true,
        attributeFilter: ['style']
    });
}

// Add responsive classes to style.css or include them here
document.head.insertAdjacentHTML('beforeend', `
<style>
    /* Responsive text classes */
    .responsive-heading {
        font-size: 2.5rem !important;
    }
    
    .responsive-subheading {
        font-size: 1.8rem !important;
    }
    
    .responsive-text {
        font-size: 1rem !important;
    }
    
    .responsive-heading-span {
        font-size: inherit !important;
    }
    
    .responsive-subheading-span {
        font-size: inherit !important;
    }
    
    /* Media queries for responsive text */
    @media (max-width: 1400px) {
        .responsive-heading {
            font-size: 2.2rem !important;
        }
        
        .responsive-subheading {
            font-size: 1.6rem !important;
        }
        
        .responsive-text {
            font-size: 1rem !important;
        }
    }
    
    @media (max-width: 1200px) {
        .responsive-heading {
            font-size: 2rem !important;
        }
        
        .responsive-subheading {
            font-size: 1.4rem !important;
        }
        
        .responsive-text {
            font-size: 0.95rem !important;
        }
    }
    
    @media (max-width: 992px) {
        .responsive-heading {
            font-size: 1.8rem !important;
        }
        
        .responsive-subheading {
            font-size: 1.3rem !important;
        }
        
        .responsive-text {
            font-size: 0.9rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .responsive-heading {
            font-size: 1.6rem !important;
        }
        
        .responsive-subheading {
            font-size: 1.2rem !important;
        }
        
        .responsive-text {
            font-size: 0.9rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .responsive-heading {
            font-size: 1.4rem !important;
        }
        
        .responsive-subheading {
            font-size: 1.1rem !important;
        }
        
        .responsive-text {
            font-size: 0.85rem !important;
        }
    }
</style>
`);

// For Vue applications - listen for a custom event that can be dispatched after Vue components mount
document.addEventListener('vue-content-loaded', function() {
    // Process all content again after Vue has loaded and rendered content
    setTimeout(function() {
        processAllContent();
    }, 200); // Slight delay to ensure Vue has completed rendering
});
