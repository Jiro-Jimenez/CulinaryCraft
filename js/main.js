
// Main JavaScript for Culinary Workshop

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('nav ul');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }
    
    // Favorite recipe functionality
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const recipeId = this.getAttribute('data-recipe-id');
            const isFavorite = this.classList.contains('active');
            
            // Send AJAX request to add/remove favorite
            fetch('includes/ajax-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${isFavorite ? 'remove_favorite' : 'add_favorite'}&recipe_id=${recipeId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle active class
                    this.classList.toggle('active');
                    
                    // Update icon
                    const icon = this.querySelector('i');
                    if (isFavorite) {
                        icon.className = 'far fa-heart';
                    } else {
                        icon.className = 'fas fa-heart';
                    }
                } else {
                    // If not logged in, redirect to login page
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get form fields
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');
            
            // Clear previous error messages
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.remove();
            });
            
            // Validate name
            if (name.value.trim() === '') {
                displayError(name, 'Name is required');
                isValid = false;
            }
            
            // Validate email
            if (email.value.trim() === '') {
                displayError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                displayError(email, 'Please enter a valid email address');
                isValid = false;
            }
            
            // Validate subject
            if (subject.value.trim() === '') {
                displayError(subject, 'Subject is required');
                isValid = false;
            }
            
            // Validate message
            if (message.value.trim() === '') {
                displayError(message, 'Message is required');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get form fields
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            
            // Clear previous error messages
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.remove();
            });
            
            // Validate username
            if (username.value.trim() === '') {
                displayError(username, 'Username is required');
                isValid = false;
            }
            
            // Validate password
            if (password.value.trim() === '') {
                displayError(password, 'Password is required');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Register form validation
    const registerForm = document.getElementById('register-form');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get form fields
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            // Clear previous error messages
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.remove();
            });
            
            // Validate first name
            if (firstName.value.trim() === '') {
                displayError(firstName, 'First name is required');
                isValid = false;
            }
            
            // Validate last name
            if (lastName.value.trim() === '') {
                displayError(lastName, 'Last name is required');
                isValid = false;
            }
            
            // Validate username
            if (username.value.trim() === '') {
                displayError(username, 'Username is required');
                isValid = false;
            }
            
            // Validate email
            if (email.value.trim() === '') {
                displayError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                displayError(email, 'Please enter a valid email address');
                isValid = false;
            }
            
            // Validate password
            if (password.value.trim() === '') {
                displayError(password, 'Password is required');
                isValid = false;
            } else if (password.value.length < 6) {
                displayError(password, 'Password must be at least 6 characters');
                isValid = false;
            }
            
            // Validate confirm password
            if (confirmPassword.value.trim() === '') {
                displayError(confirmPassword, 'Please confirm your password');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                displayError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Service enrollment form validation
    const enrollForm = document.getElementById('enroll-form');
    
    if (enrollForm) {
        enrollForm.addEventListener('submit', function(e) {
            // If user is not logged in, redirect to login page
            const isLoggedIn = enrollForm.getAttribute('data-logged-in') === 'true';
            
            if (!isLoggedIn) {
                e.preventDefault();
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
            }
        });
    }
    
    // Helper functions
    function displayError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#F44336';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '0.3rem';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
        input.style.borderColor = '#F44336';
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
});