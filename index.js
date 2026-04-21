// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Menu Toggle
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-links');
    const authButtons = document.querySelector('.auth-buttons');
    
    if (burger) {
        burger.addEventListener('click', function() {
            // Toggle Navigation Menu
            nav.classList.toggle('nav-active');
            authButtons.classList.toggle('nav-active');
            
            // Animate Burger Menu
            burger.classList.toggle('toggle');
        });
    }
    
    // Header Scroll Effect
    const header = document.querySelector('header');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Smooth Scrolling for Navigation Links
    const navLinks = document.querySelectorAll('.nav-links a, .footer-links a, .hero-buttons a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Check if the link is an internal anchor
            if (href.startsWith('#')) {
                e.preventDefault();
                
                const targetSection = document.querySelector(href);
                if (targetSection) {
                    // Close mobile menu if open
                    if (nav.classList.contains('nav-active')) {
                        nav.classList.remove('nav-active');
                        authButtons.classList.remove('nav-active');
                        burger.classList.remove('toggle');
                    }
                    
                    // Scroll to the section
                    window.scrollTo({
                        top: targetSection.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Waste Report Form — real POST to waste-report-submit.php
    const wasteReportForm = document.getElementById('waste-report-form');

    if (wasteReportForm) {
        wasteReportForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name        = document.getElementById('name').value.trim();
            const email       = document.getElementById('email').value.trim();
            const phone       = document.getElementById('phone').value.trim();
            const wasteType   = document.getElementById('waste-type').value;
            const location    = document.getElementById('location').value.trim();
            const description = document.getElementById('description').value.trim();

            if (!name || !email || !phone || !wasteType || !location || !description) {
                showAlert('Please fill in all required fields.', 'error'); return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showAlert('Please enter a valid email address.', 'error'); return;
            }
            if (!/^\d{10,15}$/.test(phone.replace(/[-\s]/g, ''))) {
                showAlert('Please enter a valid phone number (10-15 digits).', 'error'); return;
            }

            const submitBtn = wasteReportForm.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;

            const formData = new FormData(wasteReportForm);
            fetch('waste-report-submit.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    submitBtn.textContent = 'Submit Report';
                    submitBtn.disabled = false;
                    if (data.success) {
                        wasteReportForm.reset();
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(() => {
                    submitBtn.textContent = 'Submit Report';
                    submitBtn.disabled = false;
                    showAlert('Network error. Please try again.', 'error');
                });
        });
    }

    // Contact Form — real POST to contact-submit.php
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const fname   = document.getElementById('contact-fname').value.trim();
            const email   = document.getElementById('contact-email').value.trim();
            const message = document.getElementById('contact-message').value.trim();

            if (!fname || !email || !message) {
                showAlert('Please fill in all required fields.', 'error'); return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showAlert('Please enter a valid email address.', 'error'); return;
            }

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(contactForm);
            fetch('contact-submit.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    submitBtn.textContent = 'Send Message';
                    submitBtn.disabled = false;
                    if (data.success) {
                        contactForm.reset();
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(() => {
                    submitBtn.textContent = 'Send Message';
                    submitBtn.disabled = false;
                    showAlert('Network error. Please try again.', 'error');
                });
        });
    }
    
    // Newsletter Form Submission
    const newsletterForm = document.getElementById('newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                showAlert('Please enter a valid email address.', 'error');
                return;
            }
            
            // Simulate subscription
            emailInput.value = '';
            showAlert('Thank you for subscribing to our newsletter!', 'success');
        });
    }
    
    // Check session and update nav buttons
    fetch('session-check.php')
        .then(r => r.json())
        .then(data => {
            if (data.logged_in && authButtons) {
                authButtons.innerHTML = `
                    <span class="user-greeting">Hi, ${data.name}</span>
                    <a href="logout-user.php" class="login-btn">Logout</a>
                `;
            } else {
                const loginBtn = authButtons ? authButtons.querySelector('.login-btn') : null;
                const signupBtn = authButtons ? authButtons.querySelector('.signup-btn') : null;
                if (loginBtn) loginBtn.addEventListener('click', e => { e.preventDefault(); window.location.href = 'login-user.php'; });
                if (signupBtn) signupBtn.addEventListener('click', e => { e.preventDefault(); window.location.href = 'signup-user.php'; });
            }
        })
        .catch(() => {
            const loginBtn = authButtons ? authButtons.querySelector('.login-btn') : null;
            const signupBtn = authButtons ? authButtons.querySelector('.signup-btn') : null;
            if (loginBtn) loginBtn.addEventListener('click', e => { e.preventDefault(); window.location.href = 'login-user.php'; });
            if (signupBtn) signupBtn.addEventListener('click', e => { e.preventDefault(); window.location.href = 'signup-user.php'; });
        });
    
    // Helper Functions
    
    // Show Alert Message
    function showAlert(message, type = 'success') {
        // Create alert element
        const alertEl = document.createElement('div');
        alertEl.className = `alert alert-${type}`;
        alertEl.textContent = message;
        
        // Append to body
        document.body.appendChild(alertEl);
        
        // Show the alert
        setTimeout(() => {
            alertEl.classList.add('show');
        }, 10);
        
        // Remove the alert after 3 seconds
        setTimeout(() => {
            alertEl.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(alertEl);
            }, 300);
        }, 3000);
    }
    
    
    // Add some CSS for alerts and modals
    const style = document.createElement('style');
    style.textContent = `
        /* Alert Styles */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            background-color: #4CAF50;
        }
        
        .alert-error {
            background-color: #F44336;
        }
        
        /* Modal Styles */
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal-container.show {
            opacity: 1;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            position: relative;
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .modal-content.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .modal-content h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        
        .toggle-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .toggle-link a {
            color: #4CAF50;
            font-weight: 500;
        }
        
        .toggle-link a:hover {
            text-decoration: underline;
        }
        
        /* Add active class styling for mobile navigation */
        @media (max-width: 768px) {
            .nav-links.nav-active {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: white;
                padding: 20px 0;
                box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                z-index: 100;
            }
            
            .auth-buttons.nav-active {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: absolute;
                top: 270px;
                left: 0;
                width: 100%;
                background-color: white;
                padding: 0 0 20px 0;
                box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                z-index: 100;
            }
            
            .burger.toggle .line1 {
                transform: rotate(-45deg) translate(-5px, 6px);
            }
            
            .burger.toggle .line2 {
                opacity: 0;
            }
            
            .burger.toggle .line3 {
                transform: rotate(45deg) translate(-5px, -6px);
            }
        }
    `;
    
    document.head.appendChild(style);
}); 