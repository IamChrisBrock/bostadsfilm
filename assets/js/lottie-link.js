document.addEventListener('DOMContentLoaded', function() {
    const lottieLinks = document.querySelectorAll('.lottie-hover-link');

    lottieLinks.forEach(link => {
        // Get the animation path and colors from data attributes
        const animationPath = link.dataset.lottiePath;
        const defaultColor = link.dataset.color;
        const hoverColor = link.dataset.hoverColor;

        if (!animationPath) return;

        // Create container for the animation
        const container = document.createElement('div');
        container.style.width = '100%';
        container.style.height = '100%';
        link.appendChild(container);

        // Initialize Lottie animation
        const animation = lottie.loadAnimation({
            container: container,
            renderer: 'svg',
            loop: false,
            autoplay: false,
            path: animationPath
        });

        // Function to update colors
        function updateColors(isHovered) {
            const color = isHovered ? (hoverColor || defaultColor) : defaultColor;
            if (!color) return;

            animation.renderer.svgElement.querySelectorAll('path, rect, circle, ellipse, polyline, line').forEach(element => {
                if (element.getAttribute('fill') !== 'none') {
                    element.style.fill = color;
                }
                if (element.getAttribute('stroke') !== 'none') {
                    element.style.stroke = color;
                }
            });
        }

        // Set initial color when animation loads
        animation.addEventListener('DOMLoaded', () => {
            updateColors(false);
            animation.goToAndStop(0, true);
        });

        // Handle hover events
        const parentLink = link.closest('a') || link.parentElement;
        if (parentLink) {
            parentLink.addEventListener('mouseenter', () => {
                updateColors(true);
                animation.setDirection(1);
                animation.play();
            });

            parentLink.addEventListener('mouseleave', () => {
                updateColors(false);
                animation.setDirection(-1);
                animation.play();
            });
        }
    });
});
