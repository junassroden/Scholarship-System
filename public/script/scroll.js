// Scroll animation
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.classList.add('show');
        }
    });
});
document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

// Accordion
document.querySelectorAll('.accordion').forEach(item => {
    item.addEventListener('click', () => {
        const content = item.querySelector('.accordion-content');
        content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
    });
});