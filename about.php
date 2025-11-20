<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Blusolv</title>

<?php include 'head.php'?>

 
</head>
<body>
    
  <?php include 'header.php'?>
    <!-- BREADCRUMB BANNER -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">About Blusolv</h1>
            <p class="hero-subtitle">Crafting premium leather accessories with passion and precision since our inception</p>
        </div>
    </section>

    <!-- ABOUT BLUSOLV SECTION -->
    <section class="about-section" id="about-bluesolv">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title">About <span>Blusolv</span></h2>
            </div>
            <div class="about-content">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <p class="about-text">
                            At Blusolv, we're reimagining how lifestyle accessories are designed, made, and delivered. We are a modern, premium manufacturing company focused on creating functional, stylish, and sustainable products for global brands and corporate gifting programs.
                        </p>
                        <p class="about-text">
                            Blusolv brings together a team of experienced professionals from diverse backgrounds in marketing, business development, design, and strategy — united by a single purpose: to deliver world-class quality with integrity and innovation.
                        </p>
                        <p class="about-text">
                            Blusolv is built on the belief that great manufacturing combines craftsmanship, clarity, and conscience. Every Blusolv product is designed to be useful, stylish, and responsibly made — reflecting the values of the brands we work with.
                        </p>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="about-image">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Blusolv Factory">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHO WE ARE SECTION -->
   
    <section class="who-we-are-section" id="who-we-are">
        <div class="container">
            <h2 class="section-title">Who We <span>Are</span></h2>
            <p class="section-subtitle">The Blusolv Story</p>
            
            <p class="who-description">
               Blusolv is built on a passionate belief that great manufacturing comes through high clarity of purpose, excellent craftsmanship and a strong conscience to do good.
            </p>
            
            <p class="who-description">We create a wide range of lifestyle accessories from bags like duffels, backpacks, totes, travel pouches etc to belts, wallets, combos and much more...</p>
             <p class="who-description">Every Blusolv product is designed to be useful, stylish, and responsibly made — reflecting the values of the brands we work with.</p>
            
            
        </div>
    </section>
    <!-- TEAM SECTION --> 
    <section class="team-section" id="our-team">
        <div class="container">
            <div class="section-head text-center">
                <h2 class="section-title">Meet Our <span>Team</span></h2>
                <p class="section-subtitle">The professionals behind our success</p>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">
                        <img src="uploads/ambrish-sir.png" alt="Ambarish Bandyopadhyay">
                    </div>
                    <h3 class="team-name">Ambarish Bandyopadhyay</h3>
                    <p class="team-role">Founder & Managing <br>Director</p>
                    <p class="team-bio">Building Blusolv with a vision of creating new standards in lifestyle accessories through responsible manufacturing</p>
                     <div class="text-center mt-5">
                <a href="https://www.linkedin.com/in/ambarish-bandyopadhyay-7a67ab5/" target="_blank" class="read-more-btn">Know More</a>
            </div>
                </div>

                <div class="team-card">
                    <div class="team-avatar">
                      <img src="uploads/sarat.jpg" alt="Sarat Kumar Nayar">
                    </div>
                    <h3 class="team-name">Sarat Kumar Nayar</h3>
                    <p class="team-role">Head of Business Development - Brand Manufacturing</p>
                    <p class="team-bio">With strong back ground in accessories and business development, spearheading Blusolv's Brand Manufacturing business.</p>
                     <div class="text-center mt-5">
                <a href="https://www.linkedin.com/in/saratnayar/" target="_blank" class="read-more-btn">Know More</a>
            </div>
                </div>

                <div class="team-card">
                    <div class="team-avatar">
                        <img src="uploads/ayon-sir.png" alt="Ayon Paul">
                    </div>
                    <h3 class="team-name">Ayon Paul</h3>
                    <p class="team-role">Head of Business Development - Gifting</p>
                    <p class="team-bio">With extremely diverse background of business development across geographies leading Blusolv's Gifting Business</p>
                      <div class="text-center mt-5">
                <a href="https://www.linkedin.com/in/ayon-paul-732a9124" target="_blank" class="read-more-btn">Know More</a>
            </div>
                </div>
            </div>
        </div>
    </section>
    
  <!-- WHY CHOOSE US - TABS SECTION -->
    <section class="why-us-section" id="why-us">
        <div class="container">
            <div class="section-head text-center">
                <h2 class="section-title">Why <span>Choose Us</span>?</h2>
                <p class="section-subtitle">Our commitment to excellence</p>
            </div>

            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" onclick="switchTab(0)">
                        <i class="fas fa-award"></i> Quality @ Blusolv
                    </button>
                    <button class="tab-button" onclick="switchTab(1)">
                        <i class="fas fa-palette"></i> Customize @ Blusolv
                    </button>
                    <button class="tab-button" onclick="switchTab(2)">
                        <i class="fas fa-hands-helping"></i> Skill @ Blusolv
                    </button>
                    <button class="tab-button" onclick="switchTab(3)">
                        <i class="fas fa-tag"></i> Price @ Blusolv
                    </button>
                    <button class="tab-button" onclick="switchTab(4)">
                        <i class="fas fa-users"></i> People @ Blusolv
                    </button>
                    <button class="tab-button" onclick="switchTab(5)">
                        <i class="fas fa-heart"></i> Making a Difference @ Blusolv
                    </button>
                </div>

                <!-- Tab 0: Quality -->
                <div class="tab-content active" id="tab-0">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>Quality @ Blusolv</h3>
                            <p>We use only the finest materials and employ rigorous quality control measures to ensure every product meets our exacting standards and exceeds your expectations.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/image1.png" alt="Quality Control">
                        </div>
                    </div>
                </div>

                <!-- Tab 1: Customize -->
                <div class="tab-content" id="tab-1">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>Customize @ Blusolv</h3>
                            <p>From corporate branding to personalized designs, we offer comprehensive customization services to meet your specific needs and bring your vision to life.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/IMG2.png" alt="Customization">
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Skill -->
                <div class="tab-content" id="tab-2">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>Skill @ Blusolv</h3>
                            <p>Our team of skilled artisans brings decades of expertise, ensuring every piece is crafted with precision, care, and attention to the finest details.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/img4.png" alt="Skilled Artisans">
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Price -->
                <div class="tab-content" id="tab-3">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>Price @ Blusolv</h3>
                            <p>We offer exceptional value without compromising on quality, making luxury accessible to all and ensuring the best return on your investment.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/img5.png" alt="Value Pricing">
                        </div>
                    </div>
                </div>

                <!-- Tab 4: People -->
                <div class="tab-content" id="tab-4">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>People @ Blusolv</h3>
                            <p>Blusolv is a passion project of friends who after having spent 25 years + in corporate world have joined hands to create a company to change the way accessories are envisioned and created in India.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/img6.png" alt="Team Collaboration">
                        </div>
                    </div>
                </div>
                
                <!-- Tab 5: Making a Difference -->
                <div class="tab-content" id="tab-5">
                    <div class="tab-panel">
                        <div class="tab-text">
                            <h3>Making a Difference @ Blusolv</h3>
                            <p>Blusolv is an initiative deeply embedded in Bengal to promote the ageless craftsmanship of the Bengali artisans and thereby create a difference in their lives.</p>
                        </div>
                        <div class="tab-image">
                            <img src="uploads/img7.png" alt="Social Impact">
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Video Section -->
            <div class="video-container">
                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--deep-navy); margin-bottom: 2rem; text-align: center;">Factory Overview</h3>
                <div class="video-wrapper">
                    <!-- Replace YOUR_VIDEO_ID_HERE with your actual YouTube video ID -->
                    <img class="img-fuild" src="uploads/factory-img.png" width="100%" alt="Factory Overview">
                </div>
            </div>
        </div>
    </section>
    
    
     <!-- OUR PARTNERS SECTION -->
    <!--<section class="partners-section" id="our-partners">-->
    <!--    <div class="container">-->
    <!--        <div class="section-head text-center">-->
    <!--            <h2 class="section-title">Our <span>Partners</span></h2>-->
    <!--            <p class="section-subtitle">Trusted by leading brands worldwide</p>-->
    <!--        </div>-->

    <!--        <div class="partners-container">-->
                <!-- Left Column: Partner Logos Slider -->
    <!--            <div class="partners-logos-column">-->
    <!--                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--deep-navy); margin-bottom: 2rem; text-align: center;">Trusted Partners</h3>-->
    <!--                <div class="logos-slider-wrapper">-->
    <!--                    <div class="logos-track" id="logosTrack">-->
                            <!-- Logo 1 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+1" alt="Partner 1">-->
    <!--                        </div>-->
                            <!-- Logo 2 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+2" alt="Partner 2">-->
    <!--                        </div>-->
                            <!-- Logo 3 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+3" alt="Partner 3">-->
    <!--                        </div>-->
                            <!-- Logo 4 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+4" alt="Partner 4">-->
    <!--                        </div>-->
                            <!-- Logo 5 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+5" alt="Partner 5">-->
    <!--                        </div>-->
                            <!-- Logo 6 -->
    <!--                        <div class="partner-logo-item">-->
    <!--                            <img src="https://via.placeholder.com/200x100/003366/ffffff?text=Partner+6" alt="Partner 6">-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->

                <!-- Right Column: Testimonials Carousel -->
    <!--            <div class="testimonials-column">-->
    <!--                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--deep-navy); margin-bottom: 2rem; text-align: center;">What They Say</h3>-->
    <!--                <div class="testimonials-carousel">-->
    <!--                    <button class="testimonial-nav testimonial-prev" onclick="changeTestimonial(-1)">-->
    <!--                        <i class="fas fa-chevron-left"></i>-->
    <!--                    </button>-->
                        
    <!--                    <div class="testimonials-wrapper">-->
                            <!-- Testimonial 1 -->
    <!--                        <div class="testimonial-item active">-->
    <!--                            <div class="testimonial-quote">-->
    <!--                                <i class="fas fa-quote-left"></i>-->
    <!--                            </div>-->
    <!--                            <p class="testimonial-text">-->
    <!--                                "Blusolv has been an exceptional partner in creating our branded merchandise. Their attention to detail and commitment to quality is unmatched. Highly recommended!"-->
    <!--                            </p>-->
    <!--                            <div class="testimonial-author">-->
    <!--                                <h4>John Anderson</h4>-->
    <!--                                <p>CEO, TechCorp Inc.</p>-->
    <!--                            </div>-->
    <!--                        </div>-->

                            <!-- Testimonial 2 -->
    <!--                        <div class="testimonial-item">-->
    <!--                            <div class="testimonial-quote">-->
    <!--                                <i class="fas fa-quote-left"></i>-->
    <!--                            </div>-->
    <!--                            <p class="testimonial-text">-->
    <!--                                "Working with Blusolv transformed our corporate gifting program. The customization options and quality of craftsmanship exceeded our expectations."-->
    <!--                            </p>-->
    <!--                            <div class="testimonial-author">-->
    <!--                                <h4>Sarah Mitchell</h4>-->
    <!--                                <p>Marketing Director, Global Solutions</p>-->
    <!--                            </div>-->
    <!--                        </div>-->

                            <!-- Testimonial 3 -->
    <!--                        <div class="testimonial-item">-->
    <!--                            <div class="testimonial-quote">-->
    <!--                                <i class="fas fa-quote-left"></i>-->
    <!--                            </div>-->
    <!--                            <p class="testimonial-text">-->
    <!--                                "The team at Blusolv truly understands what brands need. Their innovative approach and dedication to sustainability align perfectly with our values."-->
    <!--                            </p>-->
    <!--                            <div class="testimonial-author">-->
    <!--                                <h4>Michael Chen</h4>-->
    <!--                                <p>Brand Manager, EcoWear</p>-->
    <!--                            </div>-->
    <!--                        </div>-->

                            <!-- Testimonial 4 -->
    <!--                        <div class="testimonial-item">-->
    <!--                            <div class="testimonial-quote">-->
    <!--                                <i class="fas fa-quote-left"></i>-->
    <!--                            </div>-->
    <!--                            <p class="testimonial-text">-->
    <!--                                "From concept to delivery, Blusolv maintained exceptional standards. Their skilled artisans and modern approach make them stand out in the industry."-->
    <!--                            </p>-->
    <!--                            <div class="testimonial-author">-->
    <!--                                <h4>Priya Sharma</h4>-->
    <!--                                <p>Procurement Head, Lifestyle Brands Ltd.</p>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </div>-->

    <!--                    <button class="testimonial-nav testimonial-next" onclick="changeTestimonial(1)">-->
    <!--                        <i class="fas fa-chevron-right"></i>-->
    <!--                    </button>-->
    <!--                </div>-->
                    
                    <!-- Testimonial Dots -->
    <!--                <div class="testimonial-dots" id="testimonialDots"></div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    

    <!-- LIFE @ BLUSOLV SECTION -->
    <!--<section class="life-section" id="life-@-bluesolv">-->
    <!--    <div class="container">-->
    <!--        <div class="section-head text-center">-->
    <!--            <h2 class="section-title">Life @ <span>Blusolv</span></h2>-->
    <!--            <p class="section-subtitle">Explore our workspace, culture, and craft</p>-->
    <!--        </div>-->

            <!-- Gallery Slider -->
    <!--        <div class="gallery-slider">-->
                    <!-- Navigation Buttons -->
    <!--            <button class="slider-nav slider-prev" onclick="slideGallery(-1)">-->
    <!--                <i class="fas fa-chevron-left"></i>-->
    <!--            </button>-->
    <!--            <div class="slider-wrapper">-->
    <!--            <div class="slider-track" id="sliderTrack">-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1599720033173-e3999ead4cb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Workspace 1">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Modern Workspace</div>-->
    <!--                </div>-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Production Floor">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Production Facility</div>-->
    <!--                </div>-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1606933248051-5ce98bdc2e9d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Team Collaboration">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Team Collaboration</div>-->
    <!--                </div>-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Design Studio">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Design Studio</div>-->
    <!--                </div>-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Craftsmanship">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Master Craftsmanship</div>-->
    <!--                </div>-->
    <!--                <div class="gallery-item">-->
    <!--                    <img src="https://images.unsplash.com/photo-1599720033173-e3999ead4cb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Quality Control">-->
    <!--                    <div class="gallery-overlay">-->
    <!--                        <i class="fas fa-play play-icon"></i>-->
    <!--                    </div>-->
    <!--                    <div class="gallery-label">Quality Control</div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            </div>-->
            
    <!--            <button class="slider-nav slider-next" onclick="slideGallery(1)">-->
    <!--                <i class="fas fa-chevron-right"></i>-->
    <!--            </button>-->

                <!-- Dots Navigation -->
    <!--            <div class="slider-dots" id="sliderDots"></div>-->
    <!--        </div>-->

            
    <!--    </div>-->
    <!--</section>-->

   

    
       <?php include 'footer.php'?>
       
           <script>
           
           
            // Life @ BlueSolv SLider
           
            let currentItem = 0;
            const sliderTrack = document.getElementById('sliderTrack');
            const Items = document.querySelectorAll('.gallery-item');
            const totalItems = Items.length;
           
            
            function slideGallery(direction) {
              const itemWidth = Items[0].offsetWidth + 20; // width + gap
                currentItem += direction;
                
                if (currentItem < 0) {
                    currentItem = 0;
                } else if (currentItem > totalItems) {
                    currentItem = 0;
                }
                
                const translateXX = currentItem * -300;
                 sliderTrack.style.transform = `translateX(-${currentItem * itemWidth}px)`;
            }
    
     
      

        
  
        
         function switchTab(tabIndex) {
            // Hide all tabs
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            document.getElementById('tab-' + tabIndex).classList.add('active');
            
            // Add active class to clicked button
            tabButtons[tabIndex].classList.add('active');
        }

       

        
 // Testimonials Carousel Functionality
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonial-item');
        const totalTestimonials = testimonials.length;

        // Create dots
        const dotsContainer = document.getElementById('testimonialDots');
        for (let i = 0; i < totalTestimonials; i++) {
            const dot = document.createElement('span');
            dot.className = 'testimonial-dot' + (i === 0 ? ' active' : '');
            dot.onclick = () => goToTestimonial(i);
            dotsContainer.appendChild(dot);
        }

        function changeTestimonial(direction) {
            testimonials[currentTestimonial].classList.remove('active');
            document.querySelectorAll('.testimonial-dot')[currentTestimonial].classList.remove('active');
            
            currentTestimonial += direction;
            
            if (currentTestimonial < 0) {
                currentTestimonial = totalTestimonials - 1;
            } else if (currentTestimonial >= totalTestimonials) {
                currentTestimonial = 0;
            }
            
            testimonials[currentTestimonial].classList.add('active');
            document.querySelectorAll('.testimonial-dot')[currentTestimonial].classList.add('active');
        }

        function goToTestimonial(index) {
            testimonials[currentTestimonial].classList.remove('active');
            document.querySelectorAll('.testimonial-dot')[currentTestimonial].classList.remove('active');
            
            currentTestimonial = index;
            
            testimonials[currentTestimonial].classList.add('active');
            document.querySelectorAll('.testimonial-dot')[currentTestimonial].classList.add('active');
        }

        // Auto-change testimonials every 5 seconds
        setInterval(() => {
            changeTestimonial(1);
        }, 3000);

    </script>
</body>
</html>