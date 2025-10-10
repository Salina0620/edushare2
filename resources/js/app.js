import './bootstrap';
// ---- Parallax blobs in hero ----
document.querySelectorAll('[data-parallax]').forEach(layer => {
  const strength = parseFloat(layer.dataset.parallax || '10');
  window.addEventListener('mousemove', (e) => {
    const { innerWidth: w, innerHeight: h } = window;
    const x = (e.clientX - w / 2) / w;
    const y = (e.clientY - h / 2) / h;
    layer.style.transform = `translate(${x * strength}px, ${y * strength}px)`;
  });
});

// ---- Scroll reveal ----
const sr = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('animate-fadeup');
      sr.unobserve(e.target);
    }
  });
}, { threshold: 0.08 });

document.querySelectorAll('[data-reveal]').forEach(el => sr.observe(el));

// ---- Card tilt (subtle 3D) ----
document.querySelectorAll('[data-tilt]').forEach(card => {
  const max = 8; // deg
  card.addEventListener('mousemove', (e) => {
    const r = card.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width;
    const y = (e.clientY - r.top) / r.height;
    const rx = (y - 0.5) * -max;
    const ry = (x - 0.5) *  max;
    card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg) translateZ(0)`;
  });
  card.addEventListener('mouseleave', () => {
    card.style.transform = 'rotateX(0) rotateY(0)';
  });
});
