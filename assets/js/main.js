
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
    const headerHeight = document.querySelector(".header-wrapper").offsetHeight || 500; // Fallback to 100 if header doesn't exist
    const threshold = 50; // When the background should turn green

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
});
// Mobile menu slide out and hamburger animation
document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuLinks = mobileMenu.querySelectorAll("a"); // Select all links inside the menu

    
    menuToggle.addEventListener('click', function () {
        this.classList.toggle('active'); // Animate bars
        mobileMenu.classList.toggle('active'); // Slide menu
    });

    // Optional: Close menu when clicking outside
    document.addEventListener('click', function (event) {
        if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    });
    // Close menu when clicking a menu link
    menuLinks.forEach(link => {
        link.addEventListener('click', function () {
            mobileMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });
});

// PAGE PRE LOADER
document.addEventListener("DOMContentLoaded", function () {
    window.addEventListener("load", function () {
        document.getElementById("preloader").classList.add("hidden-preloader");
    });
});

