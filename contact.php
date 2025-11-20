<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MANUFACTURING FACILITY - Blusolv - Leather Executive & Duffle Bags</title>

<?php include 'head.php'?>


    <style>
     /* Contact Container */
        .contact-main {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            position: relative;
        }

        .contact-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(212, 165, 116, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(107, 39, 55, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .contact-container {
            background: var(--ivory);
            border-radius: 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 2px solid var(--champagne-gold);
            position: relative;
            z-index: 1;
        }

        /* Contact Info Section */
        .contact-info-section {
            padding: 60px 50px;
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            position: relative;
        }

        .contact-info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(90deg, transparent, transparent 50px, rgba(212, 165, 116, 0.05) 50px, rgba(212, 165, 116, 0.05) 100px);
            pointer-events: none;
        }

        .info-title {
            font-size: 2.5rem;
            color: var(--champagne-gold);
            margin-bottom: 3rem;
            letter-spacing: 3px;
            position: relative;
            z-index: 1;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 40px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .contact-info-item:hover {
            transform: translateX(10px);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 25px;
            flex-shrink: 0;
            background: rgba(212, 165, 116, 0.2);
            border: 2px solid var(--champagne-gold);
            transition: all 0.3s ease;
        }

        .contact-info-item:hover .contact-icon {
            background: var(--champagne-gold);
            transform: scale(1.1);
        }

        .contact-icon i {
            font-size: 24px;
            color: var(--champagne-gold);
            transition: all 0.3s ease;
        }

        .contact-info-item:hover .contact-icon i {
            color: var(--deep-navy);
        }

        .contact-details h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--cream-beige);
            margin-bottom: 8px;
            letter-spacing: 2px;
        }

        .contact-details p {
            color: var(--soft-taupe);
            margin: 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        /* Map Container */
        .map-container {
            width: 100%;
            height: 250px;
            border-radius: 0;
            overflow: hidden;
            margin-top: 40px;
            border: 2px solid var(--champagne-gold);
        }

        .map-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(212, 165, 116, 0.2), rgba(26, 35, 50, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--champagne-gold);
        }

        .map-placeholder i {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        /* Contact Form Section */
        .contact-form-section {
            padding: 60px 50px;
            background: var(--ivory);
        }

        .form-section-title {
            font-size: 2.5rem;
            font-weight: 400;
            color: var(--deep-navy);
            margin-bottom: 3rem;
            letter-spacing: 3px;
        }

        .form-control {
            border: 2px solid var(--soft-taupe);
            border-radius: 0;
            padding: 15px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: var(--cream-beige);
            color: var(--charcoal-grey);
        }

        .form-control:focus {
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.2);
            background: var(--ivory);
        }

        .form-control::placeholder {
            color: var(--slate-grey);
            letter-spacing: 1px;
        }

        .message-textarea {
            height: 150px;
            resize: vertical;
        }

        .send-btn {
            background: var(--deep-navy);
            color: var(--cream-beige);
            padding: 15px 50px;
            border: 2px solid var(--deep-navy);
            border-radius: 0;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .send-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--champagne-gold);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .send-btn:hover::before {
            left: 0;
        }

        .send-btn:hover {
            border-color: var(--champagne-gold);
            color: var(--deep-navy);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 165, 116, 0.4);
        }

        /* Footer */
        .site-footer {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            color: var(--cream-beige);
            padding: 80px 0 30px;
            position: relative;
        }

        .site-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--champagne-gold) 50%, transparent);
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
        }

        .footer-column h4 {
            font-size: 1.4rem;
            color: var(--champagne-gold);
            margin-bottom: 25px;
            letter-spacing: 2px;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 12px;
            color: var(--soft-taupe);
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .footer-column ul li:hover {
            color: var(--champagne-gold);
            padding-left: 10px;
        }

        .footer-column ul li i {
            margin-right: 10px;
            color: var(--champagne-gold);
        }

        .footer-logo {
            font-size: 2rem;
            color: var(--champagne-gold);
            margin-bottom: 20px;
            letter-spacing: 3px;
            font-family: 'Cormorant Garamond', serif;
        }

        .footer-social-icons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .footer-social-icons a {
            width: 45px;
            height: 45px;
            border: 2px solid var(--champagne-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cream-beige);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 50%;
        }

        .footer-social-icons a:hover {
            background: var(--champagne-gold);
            color: var(--deep-navy);
            transform: translateY(-5px) rotate(360deg);
        }

        .footer-bottom {
            border-top: 1px solid rgba(212, 165, 116, 0.2);
            margin-top: 50px;
            padding-top: 30px;
            text-align: center;
            color: var(--slate-grey);
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-bar-content {
                flex-direction: column;
                gap: 10px;
            }

            .top-bar-left {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero-title {
                font-size: 3rem;
                letter-spacing: 5px;
            }

            .contact-info-section,
            .contact-form-section {
                padding: 40px 30px;
            }

            .form-section-title,
            .info-title {
                font-size: 2rem;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2.2rem;
                letter-spacing: 3px;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .contact-info-section,
            .contact-form-section {
                padding: 30px 20px;
            }
            .contact-details p {
            color: var(--soft-taupe);
            margin: 0;
            font-size: 11px !important;
            letter-spacing: 0.5px;
        }
        }

        
    </style>
</head>
<body>
    
    <?php include 'header.php'?>
   
<!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Get In Touch</h1>
            <p class="hero-subtitle">
                Let's discuss how we can bring your vision to life with our premium craftsmanship
            </p>
        </div>
    </section>

    <!-- Contact Section -->
    <div class="contact-main">
        <div class="container">
            <div class="contact-container">
                <div class="row g-0">
                    <!-- Contact Information -->
                    <div class="col-lg-6">
                        <div class="contact-info-section">
                            <h2 class="info-title">Contact Information</h2>
                            
                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Phone</h5>
                                    <p>+91 9311106923/7738192732/6291784169/7042626362</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Email</h5>
                                    <p>sales@blusolv.com</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Website</h5>
                                    <p>www.blusolv.com</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Location</h5>
                                    <p>Gurugram, Haryana, India - 122002</p>
                                </div>
                            </div>

                            <div class="map-container">
                                <div class="map-placeholder">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <div>Interactive Map Location</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="col-lg-6">
                        <div class="contact-form-section">
                            <h2 class="form-section-title">Send Us A Message</h2>
                            
                            <form id="contactForm" onsubmit="handleFormSubmit(event)">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" placeholder="Your Name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="email" class="form-control" placeholder="Your Email" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" placeholder="Company Name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="tel" class="form-control" placeholder="Phone Number">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <textarea class="form-control message-textarea" placeholder="Your Message" required></textarea>
                                </div>
                                
                                <button type="submit" class="send-btn">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     
     <?php include 'footer.php' ?>