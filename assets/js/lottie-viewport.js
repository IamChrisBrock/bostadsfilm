document.addEventListener("DOMContentLoaded", function () {
    // Select all elements with a Lottie URL
    const lottieElements = document.querySelectorAll("[data-lottie-url]");


    if (lottieElements.length === 0) {
        
        return;
    }

    lottieElements.forEach((lottieContainer) => {
        const lottieURL = lottieContainer.getAttribute("data-lottie-url");

        if (!lottieURL) {

            return;
        }

        
        
        const animation = lottie.loadAnimation({
            
            container: lottieContainer,
            renderer: "svg",
            loop: false,
            autoplay: false,
            path: lottieURL
        });

        // After the animation is loaded, change color
        animation.addEventListener('DOMLoaded', function () {
            const color = lottieContainer.getAttribute('data-lottie-color') || '#000000'; // Default to white if no color is set

            // Get all SVG path elements and change their stroke color
            const svgPaths = lottieContainer.querySelectorAll('svg path');
            svgPaths.forEach((path) => {
                // Remove any existing stroke or fill attributes
                path.removeAttribute('fill');
                path.removeAttribute('stroke');

                // Set the new stroke color
                path.style.stroke = color; // Using inline style to override any other color
                path.style.fill = color;   // Optional: if you want to change the fill as well
            });

            
        });

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animation.goToAndPlay(0, true);
                    
                    // Check if this element should always play
                    const shouldAlwaysPlay = lottieContainer.hasAttribute('always-play');
                    if (!shouldAlwaysPlay) {
                        observer.unobserve(lottieContainer); // Stop observing after playing once
                    }
                }
            });
        }, { 
            threshold: 0.3
        }); // Lower threshold for better trigger

        observer.observe(lottieContainer);
    });
});
