"use strict";

document.addEventListener("DOMContentLoaded", function () {
  // Get all of the images that are marked up to lazy load
  var images = document.querySelectorAll('picture.webp-image');
  var config = {
    // If the image gets within 50px in the Y axis, start the download.
    rootMargin: '50px 0px',
    threshold: 0.01
  }; // The observer for the images on the page

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      // Are we in viewport?
      if (entry.intersectionRatio > 0) {
        // Stop watching and load the image
        observer.unobserve(entry.target);
        entry.target.querySelector('.remove-image').parentNode.removeChild(entry.target.querySelector('.remove-image'));
      }
    });
  }, config);
  images.forEach(function (image) {
    observer.observe(image);
  });
});