document.addEventListener("DOMContentLoaded", () => {

    // Get all of the images that are marked up to lazy load
    const images = document.querySelectorAll('picture.webp-image');
    const config = {
        // If the image gets within 50px in the Y axis, start the download.
        rootMargin: '50px 0px',
        threshold: 0.01
    };

    if (!('IntersectionObserver' in window)) {
        // Show images for browsers that don't support IntersectionObserver
        Array.from(images).forEach((image) => {
            image.querySelector('.remove-image').parentNode.removeChild(image.querySelector('.remove-image'));
        });
    } else {
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
    }

});
