document.addEventListener('DOMContentLoaded', function() {
    // Helper function to update Lottie colors
    function updateLottieColor(animation, parentLink) {
        if (!animation || !parentLink) return;
        
        // Determine which color scheme to use based on the parent link's classes
        const isPrimary = parentLink.classList.contains('primary-link-colors');
        const cssVar = isPrimary ? '--primary-link-color' : '--secondary-link-color';
        const cssVarHover = isPrimary ? '--primary-link-hover-color' : '--secondary-link-hover-color';
        
        const isHovered = parentLink.matches(':hover');
        const colorVar = isHovered ? cssVarHover : cssVar;
        
        const color = getComputedStyle(document.documentElement)
            .getPropertyValue(colorVar).trim();
            
        animation.renderer.svgElement.querySelectorAll('path, rect, circle, ellipse').forEach(path => {
            path.style.fill = color;
        });
    }

    const lottieElements = document.querySelectorAll('.hover-lottie');

    lottieElements.forEach(lottieElement => {
        let lottieContainer = document.createElement('div');
        lottieContainer.classList.add('lottie-arrow');
        lottieContainer.style.width = '100px';
        lottieContainer.style.height = '50px';
        lottieElement.appendChild(lottieContainer);

        // Find the parent portfolio-link
        const parentLink = lottieElement.closest('.portfolio-link');

        let animation = lottie.loadAnimation({
            container: lottieContainer,
            renderer: 'svg',
            loop: false,
            autoplay: false,
            path: lottieData.lottieArrowPath
        });

        // Set initial color
        animation.addEventListener('DOMLoaded', () => {
            updateLottieColor(animation, parentLink);
            // Set initial state
            animation.goToAndStop(0, true);
        });

        // Update colors when animation loads
        animation.addEventListener('DOMLoaded', () => {
            updateLottieColor(animation, parentLink);
        });

        let isPlaying = false;  // Track whether the animation is playing forward or backward

        if (parentLink) {
            // Play animation on hover of the parent link
            parentLink.addEventListener('mouseenter', () => {
                updateLottieColor(animation, parentLink);
                animation.setDirection(1);
                animation.goToAndPlay(0, true);
            });

            // Reverse animation on hover out
            parentLink.addEventListener('mouseleave', () => {
                updateLottieColor(animation, parentLink);
                animation.setDirection(-1);
                animation.play();
            });
        }
    });
});