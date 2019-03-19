document.addEventListener("DOMContentLoaded", () => {

    // Get all of the images that are marked up to lazy load
    const images = document.querySelectorAll('picture.webp-image');
    const config = {
        // If the image gets within 50px in the Y axis, start the download.
        rootMargin: '50px 0px',
        threshold: 0.01
    };

    // The observer for the images on the page
    let observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            // Are we in viewport?
            if (entry.intersectionRatio > 0) {

                // Stop watching and load the image
                observer.unobserve(entry.target);
                entry.target.querySelector('.remove-image').parentNode.removeChild(entry.target.querySelector('.remove-image'));
            }
        });
    }, config);
    images.forEach(image => {
        observer.observe(image);
    });
});
