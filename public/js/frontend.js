// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');

    if (mobileMenuToggle && navMenu) {
        // Toggle menu
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            
            // Toggle icon
            const icon = mobileMenuToggle.querySelector('i');
            if (icon) {
                if (navMenu.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a, button');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Close menu on window resize (if resizing to desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // Hero slider - Clean implementation
    const heroSlides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.hero-nav-prev');
    const nextBtn = document.querySelector('.hero-nav-next');
    let currentSlide = 0;
    let slideInterval = null;

    // Preload images and ensure background styles are applied
    heroSlides.forEach(slide => {
        const bgImage = slide.getAttribute('data-bg-image');
        if (bgImage) {
            const img = new Image();
            img.src = bgImage;
            img.onload = function() {
                // Force background image after load with !important
                slide.style.setProperty('background-image', `url('${bgImage}')`, 'important');
                slide.style.setProperty('background-size', 'cover', 'important');
                slide.style.setProperty('background-position', 'center', 'important');
                slide.style.setProperty('background-repeat', 'no-repeat', 'important');
            };
            // Set immediately as well with !important
            slide.style.setProperty('background-image', `url('${bgImage}')`, 'important');
            slide.style.setProperty('background-size', 'cover', 'important');
            slide.style.setProperty('background-position', 'center', 'important');
            slide.style.setProperty('background-repeat', 'no-repeat', 'important');
        }
    });

    function getSlideDuration(index) {
        const slide = heroSlides[index];
        if (slide) {
            const duration = parseInt(slide.getAttribute('data-duration')) || 5;
            return Math.max(1, Math.min(30, duration)) * 1000; // Convert to milliseconds, clamp between 1-30 seconds
        }
        return 5000; // Default 5 seconds
    }

    function showSlide(index) {
        // Hide all slides
        heroSlides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remove active from all indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('active');
        });

        // Show selected slide
        if (heroSlides[index]) {
            heroSlides[index].classList.add('active');
        }
        
        // Activate corresponding indicator
        if (indicators[index]) {
            indicators[index].classList.add('active');
        }
        
        currentSlide = index;
    }

    function nextSlide() {
        if (heroSlides.length > 1) {
            const next = (currentSlide + 1) % heroSlides.length;
            showSlide(next);
            // Restart auto-slide with the new slide's duration
            stopAutoSlide();
            startAutoSlide();
        }
    }

    function prevSlide() {
        if (heroSlides.length > 1) {
            const prev = (currentSlide - 1 + heroSlides.length) % heroSlides.length;
            showSlide(prev);
            // Restart auto-slide with the new slide's duration
            stopAutoSlide();
            startAutoSlide();
        }
    }

    function startAutoSlide() {
        if (heroSlides.length > 1) {
            const duration = getSlideDuration(currentSlide);
            slideInterval = setInterval(nextSlide, duration);
        }
    }

    function stopAutoSlide() {
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
    }

    // Start auto-slide
    startAutoSlide();

    // Pause on hover
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', stopAutoSlide);
        heroSection.addEventListener('mouseleave', startAutoSlide);
    }

    // Navigation arrow clicks
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            prevSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            nextSlide();
        });
    }

    // Indicator clicks
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            showSlide(index);
            // Restart auto-slide after manual navigation
            stopAutoSlide();
            startAutoSlide();
        });
    });
});

