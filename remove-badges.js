// Remove any external badges or watermarks
(function() {
    'use strict';
    
    // Function to remove unwanted elements
    function removeBadges() {
        // Remove elements with common badge/watermark classes
        
        
        selectors.forEach(selector => {
            try {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    // Check if it's not part of the SADIWA content
                    if (!el.closest('.card') && !el.closest('table')) {
                        el.remove();
                    }
                });
            } catch(e) {
                console.log('Selector error:', selector);
            }
        });
    }
    
    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeBadges);
    } else {
        removeBadges();
    }
    
    // Run periodically to catch dynamically added badges
    setInterval(removeBadges, 1000);
    
    // Use MutationObserver to detect and remove badges immediately
    const observer = new MutationObserver(removeBadges);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();