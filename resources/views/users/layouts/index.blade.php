 @extends('users.layouts.website')
 @section('content')
 <!-- Hero Section -->
@if(auth()->check())
    <h1>Welcome, {{ auth()->user()->name }}!</h1>
    <p>You are logged in as a user.</p>
  @else
    <h1>Welcome to Our Website</h1>
    <p>Please log in or sign up to access more features.</p>
  @endif
        <section id="home" class="hero-section">
            <div class="hero-content">
                <h1>Create Beautiful Interfaces</h1>
                <p>This is a dummy website layout designed for frontend developers to practice CSS Grid, Flexbox, transitions, and responsive design frameworks.</p>
                <div class="hero-buttons">
                    <button class="btn btn-primary">Get Started</button>
                    <button class="btn btn-secondary">Learn More</button>
                </div>
            </div>
            <div class="hero-image">
                <!-- Placeholder image for UI layout -->
                <img src="https://placeholder.com" alt="Dummy Frontend Placeholder">
            </div>
        </section>

        <!-- Features Grid Section -->
        <section id="features" class="features-section">
            <h2>Our Core Features</h2>
            <div class="grid-container">
                <article class="card">
                    <div class="card-icon">⚡</div>
                    <h3>Fast Loading</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Angular, React, or vanilla styling ready.</p>
                </article>

                <article class="card">
                    <div class="card-icon">📱</div>
                    <h3>Fully Responsive</h3>
                    <p>Test your media queries on this card component easily across mobile, tablet, and desktop breakpoints.</p>
                </article>

                <article class="card">
                    <div class="card-icon">🎨</div>
                    <h3>Clean Semantic Markup</h3>
                    <p>Built using modern HTML5 tags like main, header, nav, and article for perfect accessibility scoring.</p>
                </article>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section id="contact" class="contact-section">
            <h2>Get In Touch</h2>
            <p>Fill out this dummy form to practice styling inputs, focus states, and button behaviors.</p>
            <form action="#" method="POST" class="dummy-form" onsubmit="event.preventDefault();">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Your message here..." required></textarea>
                </div>
                <button type="submit" class="btn btn-submit">Submit Message</button>
            </form>
        </section>

@endsection