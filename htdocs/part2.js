let currentIndex1 = 0; // Index for the first slider
let currentIndex2 = 0; // Index for the second slider
const slides1 = document.querySelector('#slides1'); // First slider element
const slides2 = document.querySelector('#slides2'); // Second slider element
const totalSlides1 = document.querySelectorAll('#slides1 img').length; // Total slides for the first slider
const totalSlides2 = document.querySelectorAll('#slides2 img').length; // Total slides for the second slider

// Function to calculate the current slide width
function getSlideWidth(slides) {
    return slides.clientWidth; // Get the current width of the slider element
}

function showNextSlide(slides, currentIndex, totalSlides) {
    const slideWidth = getSlideWidth(slides); // Get the current width of a slide
    currentIndex = (currentIndex + 1) % totalSlides; // Increment index and loop
    slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`; // Move slides based on dynamic width
    return currentIndex; // Return updated index
}

// Set intervals for both sliders
setInterval(() => {
    currentIndex1 = showNextSlide(slides1, currentIndex1, totalSlides1);
    currentIndex2 = showNextSlide(slides2, currentIndex2, totalSlides2);
}, 3000); // Change slide every 3 seconds

// Handle window resize to recalculate slide width
window.addEventListener('resize', () => {
    slides1.style.transform = `translateX(-${currentIndex1 * getSlideWidth(slides1)}px)`;
    slides2.style.transform = `translateX(-${currentIndex2 * getSlideWidth(slides2)}px)`;
});
