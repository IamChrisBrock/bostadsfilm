function isMobile() {
    return window.innerWidth <= 768;
}

function easeInOutQuint(t) {
    return t < 0.5 ? 16 * t * t * t * t * t : 1 - Math.pow(-2 * t + 2, 5) / 2;
}

function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function smoothScrollTo(targetPosition, duration = 2000) {
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;

    // Use different easing and duration for mobile
    const easingFunction = isMobile() ? easeInOutQuint : easeInOutCubic;
    const scrollDuration = isMobile() ? 1500 : duration; // 1.5s for mobile

    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / scrollDuration, 1);
        const easeProgress = easingFunction(progress);

        window.scrollTo(0, startPosition + distance * easeProgress);

        if (timeElapsed < scrollDuration) {
            requestAnimationFrame(animation);
        }
    }

    requestAnimationFrame(animation);
}

document.addEventListener('DOMContentLoaded', function() {
    // Wait for page load
    window.addEventListener('load', function() {
        // Check if we're on a portfolio or project page
        const portfolioContent = document.getElementById('portfolio-content');
        const projectContent = document.getElementById('project-content');
        const targetElement = portfolioContent || projectContent;

        if (targetElement) {
            // Wait 500ms before scrolling
            setTimeout(() => {
                // Get the header height for offset
                const headerWrapper = document.querySelector('.header-wrapper');
                const headerHeight = headerWrapper ? headerWrapper.offsetHeight : 0;
                const navHeight = document.querySelector('.main_menu_nav_wrapper')?.offsetHeight || 0;
                
                // Calculate the scroll position
                const targetPosition = targetElement.offsetTop - navHeight;

                // Smooth scroll to content
                smoothScrollTo(targetPosition);
            }, 500);
        }
    });
});
