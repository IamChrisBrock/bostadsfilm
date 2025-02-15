document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
            else{
                entry.target.classList.remove('animate');
            }
        });
    }, {
        threshold: 0.2 // Trigger when 20% of the element is visible
    });

    // Observe all animated lines
    document.querySelectorAll('.animated-line').forEach(line => {
        observer.observe(line);
    });
});
