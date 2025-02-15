document.addEventListener("DOMContentLoaded", function () {
    const line = document.getElementById("scroll-line");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    line.classList.add("visible");
                } else {
                    line.classList.remove("visible");
                }
            });
        },
        { threshold: 0.1,
            rootMargin: "-150px 0px" } // Trigger when at least 10% of the line is visible
    );

    observer.observe(line);
});