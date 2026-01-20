/**
 * North Star Wraps - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            offset: 100
        });
    }

    // Mobile Navigation Toggle
    initMobileNav();

    // Sticky Header
    initStickyHeader();

    // Back to Top Button
    initBackToTop();

    // Counter Animation
    initCounters();

    // FAQ Accordion
    initFAQ();

    // Gallery Filtering
    initGalleryFilter();

    // Lightbox
    initLightbox();

    // Form Validation
    initFormValidation();

    // Smooth Scroll for anchor links
    initSmoothScroll();

    // Price Calculator
    initPriceCalculator();
});

/**
 * Mobile Navigation
 */
function initMobileNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const hamburger = document.querySelector('.hamburger');

    if (!navToggle || !navMenu) return;

    navToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');

        // Animate hamburger
        if (navMenu.classList.contains('active')) {
            hamburger.style.background = 'transparent';
            hamburger.style.transform = 'rotate(45deg)';
        } else {
            hamburger.style.background = '';
            hamburger.style.transform = '';
        }
    });

    // Close menu when clicking a link
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            hamburger.style.background = '';
            hamburger.style.transform = '';
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('active');
            hamburger.style.background = '';
            hamburger.style.transform = '';
        }
    });
}

/**
 * Sticky Header
 */
function initStickyHeader() {
    const header = document.querySelector('.main-header');
    if (!header) return;

    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });
}

/**
 * Back to Top Button
 */
function initBackToTop() {
    const backToTop = document.getElementById('back-to-top');
    if (!backToTop) return;

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 500) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });

    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Animated Counters
 */
function initCounters() {
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (!counters.length) return;

    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));
}

function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-count'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    const timer = setInterval(function() {
        current += step;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

/**
 * FAQ Accordion
 */
function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    if (!faqItems.length) return;

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');

            // Close all other items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
            });

            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
}

/**
 * Gallery Filtering
 */
function initGalleryFilter() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryCards = document.querySelectorAll('.gallery-card');
    const visibleCount = document.getElementById('visible-count');

    if (!filterBtns.length || !galleryCards.length) return;

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter cards
            let count = 0;
            galleryCards.forEach(card => {
                const category = card.getAttribute('data-category');

                if (filter === 'all' || category === filter) {
                    card.classList.remove('hidden');
                    card.style.animation = 'fadeIn 0.5s ease forwards';
                    count++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Update count
            if (visibleCount) {
                visibleCount.textContent = count;
            }
        });
    });
}

/**
 * Lightbox
 */
function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    const zoomBtns = document.querySelectorAll('.gallery-zoom');
    const closeBtn = document.querySelector('.lightbox-close');
    const prevBtn = document.querySelector('.lightbox-prev');
    const nextBtn = document.querySelector('.lightbox-next');
    const titleEl = document.getElementById('lightbox-title');
    const descEl = document.getElementById('lightbox-description');
    const lightboxImg = document.getElementById('lightbox-img');

    if (!lightbox || !zoomBtns.length) return;

    const galleryItems = document.querySelectorAll('.gallery-card');
    let currentIndex = 0;

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightbox() {
        const card = galleryItems[currentIndex];
        if (!card) return;

        const title = card.querySelector('h3')?.textContent || '';
        const desc = card.querySelector('.gallery-card-content p')?.textContent || '';
        const imgSrc = card.querySelector('.gallery-card-image img')?.src || '';

        if (titleEl) titleEl.textContent = title;
        if (descEl) descEl.textContent = desc;
        if (lightboxImg && imgSrc) {
            lightboxImg.src = imgSrc;
            lightboxImg.alt = title;
        }
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % galleryItems.length;
        updateLightbox();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        updateLightbox();
    }

    zoomBtns.forEach((btn, index) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openLightbox(index);
        });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === 'ArrowLeft') prevSlide();
    });

    // Close on backdrop click
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });
}

/**
 * Form Validation
 */
function initFormValidation() {
    const form = document.getElementById('quote-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
                field.addEventListener('input', function() {
                    this.classList.remove('error');
                }, { once: true });
            }
        });

        // Email validation
        const emailField = form.querySelector('[type="email"]');
        if (emailField && emailField.value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailField.value)) {
                isValid = false;
                emailField.classList.add('error');
            }
        }

        // Phone validation (basic)
        const phoneField = form.querySelector('[type="tel"]');
        if (phoneField && phoneField.value) {
            const phonePattern = /^[\d\s\-\(\)\+]{10,}$/;
            if (!phonePattern.test(phoneField.value)) {
                isValid = false;
                phoneField.classList.add('error');
            }
        }

        // Honeypot check (spam protection)
        const honeypot = form.querySelector('[name="website"]');
        if (honeypot && honeypot.value) {
            isValid = false; // Bot detected
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.focus();
            }
        }
    });

    // Real-time phone formatting
    const phoneInput = form.querySelector('[type="tel"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = `(${value}`;
                } else if (value.length <= 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                } else {
                    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                }
            }
            e.target.value = value;
        });
    }
}

/**
 * Smooth Scroll
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Add CSS for form error state
 */
const style = document.createElement('style');
style.textContent = `
    .form-group input.error,
    .form-group select.error,
    .form-group textarea.error {
        border-color: #dc3545 !important;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-5px); }
        40%, 80% { transform: translateX(5px); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

/**
 * Price Calculator - Dropdown Version
 */
function initPriceCalculator() {
    // Check if we're on the calculator page
    if (!window.pricingConfig) {
        console.log('Calculator: No pricing config found, skipping init');
        return;
    }

    console.log('Calculator: Initializing with config', window.pricingConfig);

    const config = window.pricingConfig;
    const currency = config.currency;

    // Get dropdown elements
    const vehicleSelect = document.getElementById('vehicleType');
    const coverageSelect = document.getElementById('coverageType');
    const finishSelect = document.getElementById('finishType');
    const brandSelect = document.getElementById('brandTier');
    const conditionSelect = document.getElementById('condition');

    // Get checkbox elements
    const doorShutsCheckbox = document.getElementById('doorShuts');
    const wrapRemovalCheckbox = document.getElementById('wrapRemoval');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');

    // Get price display elements
    const totalPriceEl = document.getElementById('totalPrice');
    const priceBaseEl = document.getElementById('price-base');
    const priceVehicleEl = document.getElementById('price-vehicle');
    const priceFinishEl = document.getElementById('price-finish');
    const priceMaterialEl = document.getElementById('price-material');
    const priceConditionEl = document.getElementById('price-condition');
    const priceExtrasEl = document.getElementById('price-extras');
    const rowVehicle = document.getElementById('row-vehicle');
    const rowFinish = document.getElementById('row-finish');
    const rowMaterial = document.getElementById('row-material');
    const rowCondition = document.getElementById('row-condition');
    const rowExtras = document.getElementById('row-extras');

    // Helper: format number with commas
    function formatPrice(num) {
        return currency + Math.round(num).toLocaleString();
    }

    // Helper: find item in config array by ID
    function findById(array, id) {
        return array.find(item => item.id === id);
    }

    // Main calculation function
    function calculate() {
        console.log('Calculator: Running calculation...');

        // Get selected values
        const vehicleId = vehicleSelect ? vehicleSelect.value : '';
        const coverageId = coverageSelect ? coverageSelect.value : '';
        const finishId = finishSelect ? finishSelect.value : '';
        const brandId = brandSelect ? brandSelect.value : '';
        const conditionId = conditionSelect ? conditionSelect.value : '';

        console.log('Calculator: Selected IDs:', { vehicleId, coverageId, finishId, brandId, conditionId });

        // Get data from config
        const vehicle = findById(config.vehicleTypes, vehicleId);
        const coverage = findById(config.coverageTypes, coverageId);
        const finish = findById(config.finishTypes, finishId);
        const brand = findById(config.brandTiers, brandId);
        const condition = findById(config.conditions, conditionId);

        console.log('Calculator: Found data:', { vehicle, coverage, finish, brand, condition });

        // If no coverage selected, show zero
        if (!coverage) {
            console.log('Calculator: No coverage selected, showing £0');
            if (totalPriceEl) totalPriceEl.textContent = currency + '0';
            if (priceBaseEl) priceBaseEl.textContent = '-';
            if (rowVehicle) rowVehicle.style.display = 'none';
            if (rowFinish) rowFinish.style.display = 'none';
            if (rowMaterial) rowMaterial.style.display = 'none';
            if (rowCondition) rowCondition.style.display = 'none';
            if (rowExtras) rowExtras.style.display = 'none';
            return;
        }

        // Start with base price
        let basePrice = coverage.basePrice;
        priceBaseEl.textContent = formatPrice(basePrice);

        // Running total starts at base
        let runningTotal = basePrice;

        // Apply vehicle multiplier
        if (vehicle && vehicle.multiplier !== 1) {
            const vehicleAdjustment = basePrice * (vehicle.multiplier - 1);
            runningTotal += vehicleAdjustment;
            priceVehicleEl.textContent = (vehicleAdjustment >= 0 ? '+' : '') + formatPrice(vehicleAdjustment);
            rowVehicle.style.display = 'flex';
        } else {
            rowVehicle.style.display = 'none';
        }

        // Apply finish multiplier (on top of vehicle-adjusted price)
        if (finish && finish.multiplier !== 1) {
            const finishAdjustment = basePrice * (vehicle?.multiplier || 1) * (finish.multiplier - 1);
            runningTotal += finishAdjustment;
            priceFinishEl.textContent = (finishAdjustment >= 0 ? '+' : '') + formatPrice(finishAdjustment);
            rowFinish.style.display = 'flex';
        } else {
            rowFinish.style.display = 'none';
        }

        // Apply material/brand multiplier
        if (brand && brand.multiplier !== 1) {
            const currentSubtotal = basePrice * (vehicle?.multiplier || 1) * (finish?.multiplier || 1);
            const materialAdjustment = currentSubtotal * (brand.multiplier - 1);
            runningTotal += materialAdjustment;
            priceMaterialEl.textContent = (materialAdjustment >= 0 ? '+' : '') + formatPrice(materialAdjustment);
            rowMaterial.style.display = 'flex';
        } else {
            rowMaterial.style.display = 'none';
        }

        // Apply condition multiplier
        if (condition && condition.multiplier !== 1) {
            const currentSubtotal = basePrice * (vehicle?.multiplier || 1) * (finish?.multiplier || 1) * (brand?.multiplier || 1);
            const conditionAdjustment = currentSubtotal * (condition.multiplier - 1);
            runningTotal += conditionAdjustment;
            priceConditionEl.textContent = (conditionAdjustment >= 0 ? '+' : '') + formatPrice(conditionAdjustment);
            rowCondition.style.display = 'flex';
        } else {
            rowCondition.style.display = 'none';
        }

        // Calculate correct base with all multipliers
        const wrapSubtotal = basePrice *
            (vehicle?.multiplier || 1) *
            (finish?.multiplier || 1) *
            (brand?.multiplier || 1) *
            (condition?.multiplier || 1);

        // Calculate extras
        let extrasTotal = 0;

        if (doorShutsCheckbox && doorShutsCheckbox.checked) {
            extrasTotal += parseInt(doorShutsCheckbox.dataset.price) || 0;
        }

        if (wrapRemovalCheckbox && wrapRemovalCheckbox.checked) {
            extrasTotal += parseInt(wrapRemovalCheckbox.dataset.price) || 0;
        }

        addonCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                extrasTotal += parseInt(checkbox.dataset.price) || 0;
            }
        });

        if (extrasTotal > 0) {
            priceExtrasEl.textContent = '+' + formatPrice(extrasTotal);
            rowExtras.style.display = 'flex';
        } else {
            rowExtras.style.display = 'none';
        }

        // Final total
        const finalTotal = wrapSubtotal + extrasTotal;
        console.log('Calculator: Final total:', finalTotal, '(wrap:', wrapSubtotal, '+ extras:', extrasTotal, ')');

        if (totalPriceEl) {
            totalPriceEl.textContent = formatPrice(finalTotal);

            // Animate the price
            totalPriceEl.classList.remove('pulse');
            void totalPriceEl.offsetWidth; // Trigger reflow
            totalPriceEl.classList.add('pulse');
        }
    }

    // Add event listeners to all inputs
    [vehicleSelect, coverageSelect, finishSelect, brandSelect, conditionSelect].forEach(select => {
        if (select) {
            select.addEventListener('change', calculate);
        }
    });

    if (doorShutsCheckbox) doorShutsCheckbox.addEventListener('change', calculate);
    if (wrapRemovalCheckbox) wrapRemovalCheckbox.addEventListener('change', calculate);

    addonCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculate);
    });

    // Initial calculation
    calculate();
}
