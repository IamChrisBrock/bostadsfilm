document.addEventListener('DOMContentLoaded', function() {
    // Register ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Get all animated words
    const lines = document.querySelectorAll('.animated-text-line');
    
    lines.forEach(line => {
        // Get all words in this line
        const words = line.querySelectorAll('.animated-word');
        
        // Create a timeline for this line
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: line,
                start: 'top 70%',  // Start when top of line hits 70% down the viewport
                end: 'top 30%',    // End when top of line hits 30% down the viewport
                scrub: 0.5,        // Smooth scrubbing
                markers: false,     // Set to true for debugging
            }
        });
        
        // Add each word to the timeline
        words.forEach((word, i) => {
            // Set initial state
            gsap.set(word, {
                opacity: 0.1,
                y: 0
            });
            
            // Add to timeline with stagger
            tl.to(word, {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: 'power1.out'
            }, i * 0.1);  // Stagger each word by 0.1 seconds
        });
    });
});

