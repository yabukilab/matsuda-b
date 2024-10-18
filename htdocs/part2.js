let currentIndex1 = 0; // Index for the first slider
let currentIndex2 = 0; // Index for the second slider
const slides1 = document.querySelector('#slides1'); // First slider element
const slides2 = document.querySelector('#slides2'); // Second slider element
const totalSlides1 = document.querySelectorAll('#slides1 img').length; // Total slides for the first slider
const totalSlides2 = document.querySelectorAll('#slides2 img').length; // Total slides for the second slider

function showNextSlide(slides, currentIndex, totalSlides) {
    currentIndex = (currentIndex + 1) % totalSlides; // Increment index and loop
    slides.style.transform = `translateX(-${currentIndex * 400}px)`; // Move slides
    return currentIndex; // Return updated index
}

// Set intervals for both sliders
setInterval(() => {
    currentIndex1 = showNextSlide(slides1, currentIndex1, totalSlides1);
    currentIndex2 = showNextSlide(slides2, currentIndex2, totalSlides2);
}, 3000); // Change slide every 3 seconds
