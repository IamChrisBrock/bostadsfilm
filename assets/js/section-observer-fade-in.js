document.addEventListener("DOMContentLoaded", () => {
    // Create a single observer for all fade-in items
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Get all fade-in items within the parent container
                const parent = entry.target.closest('.fade-in-parent');
                if (parent) {
                    const items = parent.querySelectorAll('.fade-in-item');
                    items.forEach((item, index) => {
                        // Add visible class after a brief delay based on index
                        setTimeout(() => {
                            item.classList.add('visible');
                        }, index * 200); // 200ms delay between each item
                    });
                } else {
                    // If no parent, just add visible class to the current item
                    entry.target.classList.add('visible');
                }
                observer.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0,
        rootMargin: '-30% 0px'
    });

    // Observe all fade-in parents and individual fade items
    const fadeParents = document.querySelectorAll('.fade-in-parent');
    fadeParents.forEach(parent => observer.observe(parent));

    // Also observe individual fade items that aren't in a parent
    const fadeClasses = ['fade-in-left', 'fade-in-right', 'fade-in-bottom', 'fade-in-top'];
    fadeClasses.forEach(fadeClass => {
        const elements = document.querySelectorAll(`.${fadeClass}:not(.fade-in-parent .${fadeClass})`);
        elements.forEach(el => observer.observe(el));
    });
});