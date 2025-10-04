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
    
    // Form Validation for Waste Report Form
    const wasteReportForm = document.getElementById('waste-report-form');
    
    if (wasteReportForm) {
        wasteReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const wasteType = document.getElementById('waste-type').value;
            const location = document.getElementById('location').value.trim();
            const description = document.getElementById('description').value.trim();
            const image = document.getElementById('image').files[0];
            
            // Validate form values
            if (!name || !email || !phone || !wasteType || !location || !description) {
                showAlert('Please fill in all required fields.', 'error');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Please enter a valid email address.', 'error');
                return;
            }
            
            // Phone validation (simple validation for now)
            const phoneRegex = /^\d{10,15}$/;
            if (!phoneRegex.test(phone.replace(/[-\s]/g, ''))) {
                showAlert('Please enter a valid phone number.', 'error');
                return;
            }
            
            // If all validations pass, submit the form (simulated for now)
            simulateFormSubmission(wasteReportForm, 'Your waste report has been submitted successfully!');
        });
    }
    
    // Form Validation for Contact Form
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const name = document.getElementById('contact-name').value.trim();
            const email = document.getElementById('contact-email').value.trim();
            const subject = document.getElementById('contact-subject').value.trim();
            const message = document.getElementById('contact-message').value.trim();
            
            // Validate form values
            if (!name || !email || !subject || !message) {
                showAlert('Please fill in all required fields.', 'error');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Please enter a valid email address.', 'error');
                return;
            }
            
            // If all validations pass, submit the form (simulated for now)
            simulateFormSubmission(contactForm, 'Your message has been sent successfully!');
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
    
    // Login and Signup button click handlers
    const loginBtn = document.querySelector('.login-btn');
    const signupBtn = document.querySelector('.signup-btn');
    
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal('login');
        });
    }
    
    if (signupBtn) {
        signupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal('signup');
        });
    }
    
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
    
    // Simulate Form Submission (for demo purposes)
    function simulateFormSubmission(form, successMessage) {
        // Show loading indicator
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
        
        // Simulate API call delay
        setTimeout(() => {
            // Reset form
            form.reset();
            
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            // Show success message
            showAlert(successMessage);
        }, 1500);
    }
    
    // Show Login/Signup Modal
    function showModal(type) {
        // Create modal container
        const modalContainer = document.createElement('div');
        modalContainer.className = 'modal-container';
        
        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content';
        
        // Close button
        const closeBtn = document.createElement('span');
        closeBtn.className = 'close-btn';
        closeBtn.innerHTML = '&times;';
        
        // Modal header
        const modalHeader = document.createElement('h2');
        modalHeader.textContent = type === 'login' ? 'Login' : 'Sign Up';
        
        // Create form
        const form = document.createElement('form');
        
        // Email field
        const emailGroup = document.createElement('div');
        emailGroup.className = 'form-group';
        
        const emailLabel = document.createElement('label');
        emailLabel.textContent = 'Email';
        emailLabel.setAttribute('for', `${type}-email`);
        
        const emailInput = document.createElement('input');
        emailInput.type = 'email';
        emailInput.id = `${type}-email`;
        emailInput.required = true;
        
        emailGroup.appendChild(emailLabel);
        emailGroup.appendChild(emailInput);
        
        // Password field
        const passwordGroup = document.createElement('div');
        passwordGroup.className = 'form-group';
        
        const passwordLabel = document.createElement('label');
        passwordLabel.textContent = 'Password';
        passwordLabel.setAttribute('for', `${type}-password`);
        
        const passwordInput = document.createElement('input');
        passwordInput.type = 'password';
        passwordInput.id = `${type}-password`;
        passwordInput.required = true;
        
        passwordGroup.appendChild(passwordLabel);
        passwordGroup.appendChild(passwordInput);
        
        // Additional fields for signup
        let nameGroup;
        if (type === 'signup') {
            // Name field
            nameGroup = document.createElement('div');
            nameGroup.className = 'form-group';
            
            const nameLabel = document.createElement('label');
            nameLabel.textContent = 'Full Name';
            nameLabel.setAttribute('for', 'signup-name');
            
            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.id = 'signup-name';
            nameInput.required = true;
            
            nameGroup.appendChild(nameLabel);
            nameGroup.appendChild(nameInput);
            
            // Confirm password field
            const confirmPasswordGroup = document.createElement('div');
            confirmPasswordGroup.className = 'form-group';
            
            const confirmPasswordLabel = document.createElement('label');
            confirmPasswordLabel.textContent = 'Confirm Password';
            confirmPasswordLabel.setAttribute('for', 'signup-confirm-password');
            
            const confirmPasswordInput = document.createElement('input');
            confirmPasswordInput.type = 'password';
            confirmPasswordInput.id = 'signup-confirm-password';
            confirmPasswordInput.required = true;
            
            confirmPasswordGroup.appendChild(confirmPasswordLabel);
            confirmPasswordGroup.appendChild(confirmPasswordInput);
            
            passwordGroup.after(confirmPasswordGroup);
        }
        
        // Submit button
        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.className = 'submit-btn';
        submitBtn.textContent = type === 'login' ? 'Login' : 'Sign Up';
        
        // Assemble form
        if (type === 'signup') {
            form.appendChild(nameGroup);
        }
        form.appendChild(emailGroup);
        form.appendChild(passwordGroup);
        form.appendChild(submitBtn);
        
        // Add link to toggle between login and signup
        const toggleLink = document.createElement('p');
        toggleLink.className = 'toggle-link';
        toggleLink.innerHTML = type === 'login' 
            ? 'Don\'t have an account? <a href="#" class="toggle-form">Sign Up</a>' 
            : 'Already have an account? <a href="#" class="toggle-form">Login</a>';
        
        // Assemble modal
        modalContent.appendChild(closeBtn);
        modalContent.appendChild(modalHeader);
        modalContent.appendChild(form);
        modalContent.appendChild(toggleLink);
        modalContainer.appendChild(modalContent);
        
        // Add modal to document
        document.body.appendChild(modalContainer);
        document.body.style.overflow = 'hidden';
        
        // Show modal with animation
        setTimeout(() => {
            modalContainer.classList.add('show');
            modalContent.classList.add('show');
        }, 10);
        
        // Close modal event
        closeBtn.addEventListener('click', () => {
            closeModal(modalContainer, modalContent);
        });
        
        // Close modal when clicking outside
        modalContainer.addEventListener('click', (e) => {
            if (e.target === modalContainer) {
                closeModal(modalContainer, modalContent);
            }
        });
        
        // Toggle between login and signup
        const toggleFormLink = modalContent.querySelector('.toggle-form');
        toggleFormLink.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(modalContainer, modalContent);
            setTimeout(() => {
                showModal(type === 'login' ? 'signup' : 'login');
            }, 300);
        });
        
        // Form submission (simulated)
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Show loading indicator
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;
            
            // Simulate API call delay
            setTimeout(() => {
                closeModal(modalContainer, modalContent);
                showAlert(`${type === 'login' ? 'Login' : 'Registration'} successful!`);
            }, 1500);
        });
    }
    
    // Close Modal Function
    function closeModal(container, content) {
        content.classList.remove('show');
        container.classList.remove('show');
        
        setTimeout(() => {
            document.body.removeChild(container);
            document.body.style.overflow = '';
        }, 300);
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