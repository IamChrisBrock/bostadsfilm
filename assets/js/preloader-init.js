// Initialize preloader animation as soon as possible
(function() {
    console.log('Preloader init script started');

    var initPreloaderAnimation = function() {
        console.log('Checking for Lottie...', typeof lottie);

        if (typeof lottie === 'undefined') {
            console.log('Lottie not loaded yet, retrying...');
            setTimeout(initPreloaderAnimation, 50);
            return;
        }

        console.log('Lottie found, looking for preloader container...');
        var preloaderContainer = document.querySelector('#preloader .loader');
        if (!preloaderContainer) {
            console.log('Preloader container not found');
            return;
        }

        console.log('Found preloader container, creating animation container...');
        // Create container for Lottie animation
        var animContainer = document.createElement('div');
        animContainer.style.width = '60px';
        animContainer.style.height = '60px';
        preloaderContainer.appendChild(animContainer);

        var themeUrl = document.querySelector('meta[name="theme-url"]');
        if (!themeUrl) {
            console.error('Theme URL meta tag not found');
            return;
        }

        var animationPath = themeUrl.content + '/assets/lottie/loading-house.json';
        console.log('Loading animation from:', animationPath);

        try {
            // Load the animation
            var anim = lottie.loadAnimation({
                container: animContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: animationPath,
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid meet'
                }
            });

            anim.addEventListener('data_ready', function() {
                console.log('Animation data loaded successfully');
            });

            anim.addEventListener('error', function(error) {
                console.error('Animation error:', error);
            });

        } catch (error) {
            console.error('Error initializing animation:', error);
        }
    };

    // Start initialization immediately
    initPreloaderAnimation();
})();
