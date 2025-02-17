// More reliable device detection
function getDeviceType() {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
        return 'tablet';
    }
    if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
        return 'mobile';
    }
    return 'desktop';
}

// Simplified easing function that works well across devices
function easeInOutQuart(t) {
    return t < 0.5 
        ? 8 * t * t * t * t 
        : 1 - 8 * (--t) * t * t * t;
}

// Fallback for browsers that don't support smooth scrolling
function isReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function smoothScrollTo(targetPosition) {
    // Check if native smooth scroll is supported and no reduced motion
    if ('scrollBehavior' in document.documentElement.style && !isReducedMotion()) {
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
        return;
    }

    // Fallback for browsers without smooth scroll support
    const startPosition = window.pageYOffset || document.documentElement.scrollTop;
    const distance = targetPosition - startPosition;
    const deviceType = getDeviceType();
    
    // Adjust duration based on device and distance - much slower animation
    let duration = Math.min(Math.abs(distance) * 2.5, 3500); // Much slower base duration
    if (deviceType !== 'desktop') {
        duration = Math.min(duration, 2500); // Slower on mobile but still optimized
    }

    let startTime = null;
    
    function animation(currentTime) {
        if (!startTime) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        const easeProgress = easeInOutQuart(progress);
        
        const scrollPosition = startPosition + (distance * easeProgress);
        window.scrollTo(0, scrollPosition);

        if (timeElapsed < duration) {
            requestAnimationFrame(animation);
        }
    }

    requestAnimationFrame(animation);
}

// Main initialization
function initAutoScroll() {
    const targetElement = document.getElementById('portfolio-content') || 
                         document.getElementById('project-content');
    
    if (!targetElement) return;

    // Function to calculate and perform scroll
    function performScroll() {
        // Ensure all elements are properly loaded and measured
        const headerWrapper = document.querySelector('.header-wrapper');
        const mainNav = document.querySelector('.main_menu_nav_wrapper');
        
        if (!headerWrapper || !mainNav) {
            // If elements aren't ready, retry after a short delay
            setTimeout(performScroll, 100);
            return;
        }

        const navHeight = mainNav.offsetHeight || 0;
        const targetPosition = targetElement.offsetTop - navHeight;

        // Add a small delay to ensure accurate measurements
        setTimeout(() => smoothScrollTo(targetPosition), 1200);
    }

    // Initialize scroll after everything is loaded
    if (document.readyState === 'complete') {
        performScroll();
    } else {
        window.addEventListener('load', performScroll);
    }
}

// Initialize once DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAutoScroll);
} else {
    initAutoScroll();
}
