   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>BluSolv – Where Design Meets Craftsmanships</title>
    
    <?php include 'head.php' ?>
</head>
<body>

<?php include 'header.php' ?>

    <!-- Hero Banner Slider (3 Slides with Auto-play) -->
    <div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <!-- Slide 1: Main Brand Introduction -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: url('uploads/banner1.jpg');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <h1>BLUSOLV</h1>
                        <p class="subtitle">Premium Lifestyle Accessories</p>
                        <p class="description">Crafting excellence in every stitch with unparalleled attention to detail and timeless elegance</p>
                        <div class="hero-cta">
                            <a href="products.php" class="hero-btn">Explore Collections</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Craftsmanship Focus -->
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('uploads/banner2.jpg');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <h1>TIMELESS CRAFTSMANSHIP</h1>
                        <p class="subtitle">Heritage Meets Modern Design</p>
                        <p class="description">Handcrafted by master artisans of Bengal with decades of experience</p>
                        <!--<div class="hero-cta">-->
                        <!--    <a href="#about" class="hero-btn">Discover Our Story</a>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>

            <!-- Slide 3: Custom Manufacturing -->
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('uploads/banner3.png');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <h1>CUSTOM MANUFACTURING</h1>
                        <p class="subtitle">Tailored To Your Vision</p>
                        <p class="description">From Brand Manufacturing to Gifting, we make it as you like it</p>
                        <!--<div class="hero-cta">-->
                        <!--    <a href="#contact" class="hero-btn">Start Your Project</a>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <i class="fas fa-chevron-left fa-2x"></i>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <i class="fas fa-chevron-right fa-2x"></i>
        </button>
    </div>

    <!-- Welcome Section -->
    <!--<section class="welcome-section" id="about">-->
    <!--    <div class="container">-->
    <!--        <h2 class="section-title">Welcome to Blusolv</h2>-->
    <!--        <p class="section-subtitle">Crafting Quality Since Day One</p>-->
            
    <!--        <div class="row g-4">-->
    <!--            <div class="col-md-4">-->
    <!--                <div class="feature-card text-center">-->
    <!--                    <div class="feature-icon">-->
    <!--                        <i class="fas fa-gem"></i>-->
    <!--                    </div>-->
    <!--                    <h3 class="feature-title">Premium Quality</h3>-->
    <!--                    <p class="feature-description">Every piece is crafted with the finest materials, ensuring durability and elegance that stands the test of time. Our commitment to excellence is unwavering.</p>-->
    <!--                </div>-->
    <!--            </div>-->
                
    <!--            <div class="col-md-4">-->
    <!--                <div class="feature-card text-center">-->
    <!--                    <div class="feature-icon">-->
    <!--                        <i class="fas fa-hands-helping"></i>-->
    <!--                    </div>-->
    <!--                    <h3 class="feature-title">Handcrafted Excellence</h3>-->
    <!--                    <p class="feature-description">Our skilled artisans bring decades of experience to create masterpieces that blend tradition with innovation, ensuring each product is unique.</p>-->
    <!--                </div>-->
    <!--            </div>-->
                
    <!--            <div class="col-md-4">-->
    <!--                <div class="feature-card text-center">-->
    <!--                    <div class="feature-icon">-->
    <!--                        <i class="fas fa-leaf"></i>-->
    <!--                    </div>-->
    <!--                    <h3 class="feature-title">Sustainable Practices</h3>-->
    <!--                    <p class="feature-description">We are committed to ethical sourcing and environmentally responsible manufacturing processes, creating a better future for generations.</p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->

    <!-- Who We Are Section -->
    <section class="who-we-are-section" id="who-we-are">
        <div class="container">
            <h2 class="section-title">Who We <span>Are</span></h2>
            <p class="section-subtitle">The Blusolv Story</p>
            
            <p class="who-description">
               Blusolv is built with a passionate belief that great manufacturing comes through high clarity of purpose, excellent craftsmanship and strong conscience to do good
            </p>
            
            <p class="who-description">We create a wide range of lifestyle accessories from bags like duffels, backpacks, totes, travel pouches etc to belts, wallets, combos and much more...</p>
             <p class="who-description">Every Blusolv product is designed to be useful, stylish and is responsibly made — reflecting the values of the brands we work with</p>
            
            <div class="text-center mt-5">
                <a href="about.php#about-bluesolv" class="read-more-btn">Learn More About Us</a>
            </div>
        </div>
    </section>

    <!-- Products Carousel Section -->
    <section class="products-carousel-section" id="products">
        <div class="container-fluid">
            <h2 class="section-title">Categories of <span>Product</span></h2>
            <p class="section-subtitle">Explore Our Premium Range</p>
            
            <div class="carousel-container">
                <button class="nav-button nav-prev" onclick="slideProducts(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="carousel-wrapper">
                    <div class="carousel-track" id="productTrack">
                        <div class="product-slide">
                            <a href="products.php?category=TOTE%20BAG">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/tote.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Premium</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Tote Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                        <div class="product-slide">
                            <a href="products.php?category=HAND BAG">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/hand-bag.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Elegant</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Hand Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                        <div class="product-slide">
                            <a href="products.php?category=HOBO BAG">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/hobo.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Modern</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Hobo Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                        <div class="product-slide">
                            <a href="products.php?category=SLING BAG">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/sling.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Travel</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">SLING BAGs</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                        <div class="product-slide">
                            <a href="products.php?category=CLUTCH BAG">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/clutch.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Professional</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">CLUTCH BAGs</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                        <div class="product-slide">
                            <a href="products.php?category=WOMEN'S WALLET">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/womens-wallet.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">WOMEN'S WALLET</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                         <div class="product-slide">
                             <a href="products.php?category=Women's Briefcase">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/womens-briefcase.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Women's Briefcase</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                           <div class="product-slide">
                               <a href="products.php?category=Men's Wallet">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/mens-wallet.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Men's Wallet</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                          <div class="product-slide">
                              <a href="products.php?category=Belt">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/belt.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Belts</p>
                                </div>
                            </div>
                            </a>
                        </div>
                          <div class="product-slide">
                           <a href="products.php?category=Men's Briefcase">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/mens-briefcase.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Men's Briefcases</p>
                                </div>
                            </div>
                            </a>
                        </div>
                         <div class="product-slide">
                             <a href="products.php?category=Backpacks">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/backpack.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Backpacks</p>
                                </div>
                            </div>
                            </a>
                        </div>
                         <div class="product-slide">
                             <a href="products.php?category=Duffel Bags">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/duffle-img.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Duffel Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                          <div class="product-slide">
                              <a href="products.php?category=Duffel Bags with Trolley">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/duffel-with-troley.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Duffel Bags with Trolley</p>
                                </div>
                            </div>
                            </a>
                        </div>
                          <div class="product-slide">
                              <a href="products.php?category=Toiletry Pouch">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/toiletry.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Toiletry Pouch</p>
                                </div>
                            </div>
                            </a>
                        </div>
                          <div class="product-slide">
                             <a href="products.php?category=Hard Luggage">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/hard-luggage.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Hard Luggage</p>
                                </div>
                            </div>
                            </a>
                        </div>
                           <div class="product-slide">
                             <a href="products.php?category=Soft Luggage">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/soft-luggage.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Soft Luggage</p>
                                </div>
                            </div>
                            </a>
                        </div>
                            <div class="product-slide">
                                <a href="products.php?category=Rucksack Combo Pack">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/rucksack.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Rucksack Combo Pack</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="product-slide">
                            <a href="products.php?category=Gym Bag">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/Gym-bag.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Gym Bag</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="product-slide">
                            <a href="products.php?category=Key Chain">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/key.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Key Chain</p>
                                </div>
                            </div>
                            </a>
                        </div>
                          <div class="product-slide">
                            <a href="products.php?category=Eyewear Case">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/eyewear.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Eyewear Case</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="product-slide">
                          <a href="products.php?category=Coaster">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/coaster.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Coaster</p>
                                </div>
                            </div>
                            </a>
                        </div>
                         <div class="product-slide">
                           <a href="products.php?category=Cigarette Case">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/cigarette-case.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Cigarette Case</p>
                                </div>
                            </div>
                            </a>
                        </div>
                         <div class="product-slide">
                            <a href="products.php?category=Shopping Bags">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/shopping-bag.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Shopping Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                         <div class="product-slide">
                             <a href="products.php?category=Dealer / Trade Bags">
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('Categories-of-Product-images/trade.png');">
                                    <div class="product-overlay">
                                        <h3 class="product-title">Casual</h3>
                                    </div>
                                </div>
                                <div class="product-name-section">
                                    <p class="product-name">Dealer / Trade Bags</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        
                    </div>
                </div>
                
                <button class="nav-button nav-next" onclick="slideProducts(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Materials Section -->
    <section class="material-section" id="materials">
        <div class="container">
            <h2 class="section-title">Premium <span>Materials</span></h2>
            <p class="section-subtitle">Quality You Can Feel</p>
            
            <div class="material-grid">
                <a href="products.php?material=Leather">
                <div class="material-card" style="background-image: url('Material-of-Product-images/leather-texture.png');">
                    <div class="material-overlay">
                        <h3 class="material-name">Leather</h3>
                    </div>
                </div>
                </a>
                
                   <a href="products.php?material=Pu">
                <div class="material-card" style="background-image: url('Material-of-Product-images/PU.png">
                    <div class="material-overlay">
                        <h3 class="material-name">PU</h3>
                    </div>
                </div>
                </a>
                
            
                
                  <a href="products.php?material=Nylon">
                <div class="material-card" style="background-image: url('Material-of-Product-images/nylon.png">
                    <div class="material-overlay">
                        <h3 class="material-name">Nylon</h3>
                    </div>
                </div>
                </a>
                
                    <a href="products.php?material=Jute">
                <div class="material-card" style="background-image: url('Material-of-Product-images/jute.png">
                    <div class="material-overlay">
                        <h3 class="material-name">Jute</h3>
                    </div>
                </div>
                </a>
                
                <a href="products.php?material=Canvas">
                 <div class="material-card" style="background-image: url('Material-of-Product-images/Canvas.png');">
                    <div class="material-overlay">
                        <h3 class="material-name">Canvas</h3>
                    </div>
                </div>
                </a>
                
            </div>
        </div>
    </section>

    <!-- Requirements Section -->
    <section class="requirement-section" id="requirements">
        <div class="container">
            <h2 class="section-title">NATURE OF <span>REQUIREMENT</span></h2>
            <p class="section-subtitle">Your Needs, Our Priority</p>
            
            <div class="requirement-grid">
                <a href="products.php?requirement=Brand Manufacturing">
                <div class="requirement-card">
                    <div class="requirement-image" style="background-image: url('https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800');"></div>
                    <div class="requirement-overlay">
                        <h3 class="requirement-title">Brand Manufacturing</h3>
                    </div>
                </div>
                </a>
                
                <a href="products.php?requirement=Exports">
                <div class="requirement-card">
                    <div class="requirement-image" style="background-image: url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800');"></div>
                    <div class="requirement-overlay">
                        <h3 class="requirement-title">Exports</h3>
                    </div>
                </div>
                </a>
                
                <a href="products.php?requirement=Corporate Gifting">
                <div class="requirement-card">
                    <div class="requirement-image" style="background-image: url('https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=800');"></div>
                    <div class="requirement-overlay">
                        <h3 class="requirement-title">Corporate Gifting</h3>
                    </div>
                </div>
                </a>
                <a href="products.php?requirement=Personal Gifting">
                <div class="requirement-card">
                    <div class="requirement-image" style="background-image: url('https://images.unsplash.com/photo-1513885535751-8b9238bd345a?w=800');"></div>
                    <div class="requirement-overlay">
                        <h3 class="requirement-title">Personal Gifting</h3>
                    </div>
                </div>
                </a>
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
                        <img src="uploads/ambirsh-sir.png" alt="Ambarish Bandyopadhyay">
                    </div>
                    <h3 class="team-name">Ambarish Bandyopadhyay</h3>
                    <p class="team-role">Founder & Managing <br> Director</p>
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
    
    
    <script>
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
        }, 5000);
    </script>

   <?php include 'footer.php'?>

  
