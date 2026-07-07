document.addEventListener('DOMContentLoaded', () => {
    
    // 1. SELECT ELEMENTS
    const form = document.querySelector('.dummy-form');
    const navLinks = document.querySelectorAll('nav a');
    const sections = document.querySelectorAll('section');

    // 2. DUMMY FORM SUBMISSION & VALIDATION
    if (form) {
        form.addEventListener('submit', (event) => {
            // Prevent real reloading page behavior
            event.preventDefault();

            // Extract input values
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            // Basic alert handling validation check success
            if (name && email && message) {
                alert(`Thank you, ${name}! Your dummy message has been intercepted successfully.`);
                form.reset();
            } else {
                alert('Please fill out all fields correctly.');
            }
        });
    }

    // 3. SCROLL SPY (Highlight nav menu link based on current section viewport)
    window.addEventListener('scroll', () => {
        let currentSectionId = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            // Adjust calculation calculation offset for sticky header height
            if (window.scrollY >= (sectionTop - 150)) {
                currentSectionId = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${currentSectionId}`) {
                link.classList.add('active');
            }
        });
    });

      // 4. DARK MODE INTERACTION LOGIC
        const themeToggleBtn = document.getElementById('theme-toggle');
        
        // Check user preference saved in LocalStorage from previous visits
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme) {
            document.documentElement.setAttribute('data-theme', currentTheme);
            themeToggleBtn.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
        }

        themeToggleBtn.addEventListener('click', () => {
            let theme = 'light';
            
            // Toggle the data-theme property on the <html> tag
            if (document.documentElement.getAttribute('data-theme') !== 'dark') {
                theme = 'dark';
                themeToggleBtn.textContent = '☀️';
            } else {
                themeToggleBtn.textContent = '🌙';
            }
            
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme); // Save preference
        });

        // 5. MOBILE HAMBURGER MENU LOGIC
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');

        // Toggle active classes on click
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close menu when a navigation item link is clicked
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });

});