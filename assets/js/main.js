// PMB Politeknik Mulia Rokan - Main JS
document.addEventListener('DOMContentLoaded', function () {
  // Mobile Menu Toggle
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function () {
      mobileMenu.classList.toggle('hidden');
    });
  }

  // Smooth Scroll for Nav Links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId.length > 1) {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
          }
        }
      }
    });
  });

  // Accordion FAQ
  const faqButtons = document.querySelectorAll('.faq-toggle');
  faqButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const content = this.nextElementSibling;
      const icon = this.querySelector('.faq-icon');
      if (content) {
        content.classList.toggle('hidden');
      }
      if (icon) {
        icon.classList.toggle('rotate-180');
      }
    });
  });
});
