document.addEventListener("DOMContentLoaded", () => {
    const fadeClasses = ["fade-in-left", "fade-in-right", "fade-in-bottom", "fade-in-top"];

    fadeClasses.forEach(fadeClass => {
        const elements = document.querySelectorAll(`.${fadeClass}`);
        if (elements.length > 0) {
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            elements.forEach(el => observer.observe(el));
        }
    });
});