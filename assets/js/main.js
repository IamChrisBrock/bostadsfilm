
// VIDEO
// Some browsers, stutter on video loop -> this is a helper that resets the video instead of looping it
document.addEventListener("DOMContentLoaded", () => {
    const video = document.querySelector("video");
    if (video) {
        video.addEventListener("ended", () => {
            video.currentTime = 0;
            video.play();
        });
    }
});



// Mobile Menu Toggle


// Menu Scroll Hide Effect
document.addEventListener("DOMContentLoaded", function () {
    let lastScrollTop = 0;
    const navBar = document.querySelector(".main_menu_nav_wrapper");
    const headerWrapper = document.querySelector(".header-wrapper");
    const headerHeight = headerWrapper ? headerWrapper.offsetHeight : 500; // Fallback to 500 if header doesn't exist
    const threshold = 50; // When the background should turn green

    // Only add scroll listener if navbar exists
    if (navBar) {
        window.addEventListener("scroll", function () {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            // If scrolling down, hide navbar
            if (currentScroll > lastScrollTop && currentScroll > headerHeight) {
                navBar.classList.add("hidden"); // Hide menu
            } 
            // If scrolling up, show navbar
            else {
                navBar.classList.remove("hidden"); // Show menu

                // If the scroll position is beyond the threshold, add green background
                if (currentScroll > threshold) {
                    navBar.classList.add("green-background");
                    navBar.classList.remove("transparent-background"); // Ensure it's not transparent
                }
            }

            // If the menu overlaps the header, reset to transparent
            if (currentScroll < headerHeight/2) {
                navBar.classList.remove("green-background");
                navBar.classList.add("transparent-background");
            }

            lastScrollTop = currentScroll;
        });
    }
});
// Handle menu links and anchor navigation
document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const allMenuLinks = document.querySelectorAll('.main_menu_nav_wrapper a, #mobile-menu a'); // Select all menu links
    const isHomePage = document.body.classList.contains('home');
    const homeUrl = window.location.protocol + '//' + window.location.host + '/';

    // Handle all menu links
    allMenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // If it's an anchor link and we're not on the home page
            if (href && href.startsWith('#') && !isHomePage) {
                e.preventDefault();
                window.location.href = homeUrl + href;
                return;
            }
            
            // If we're on the home page and it's an anchor link
            if (href && href.startsWith('#') && isHomePage) {
                e.preventDefault();
                const targetElement = document.querySelector(href);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    // Close mobile menu if open
                    if (mobileMenu) {
                        mobileMenu.classList.remove('active');
                        menuToggle.classList.remove('active');
                    }
                }
            }
        });
    });

    // Mobile menu toggle
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function () {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                mobileMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });
    }
});

// PAGE PRE LOADER
const hidePreloader = () => {
    const preloader = document.getElementById("preloader");
    if (preloader) {
        preloader.classList.add("hidden-preloader");
    }
};

// Hide preloader when everything is loaded
window.addEventListener("load", hidePreloader);

// Set a maximum time to show preloader
setTimeout(hidePreloader, 2000);

