window.onload = function() {
    let currentIdx = 0;
    const slides = document.querySelectorAll('.my-slide');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (slides.length === 0) return;

    function showSlide(n) {
        slides[currentIdx].classList.remove('active');
        
        currentIdx = (n + slides.length) % slides.length;
        
        slides[currentIdx].classList.add('active');
    }

    prevBtn.onclick = function() {
        showSlide(currentIdx - 1);
    };

    nextBtn.onclick = function() {
        showSlide(currentIdx + 1);
    };
};