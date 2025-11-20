 <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <h3 class="footer-title">BLUSOLV</h3>
                    <p class="footer-text">
                        Premium lifestyle accessories crafted with excellence. Your trusted partner for quality products and customized solutions that stand the test of time
                    </p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <h3 class="footer-title">Quick Links</h3>
                    <a href="index.php" class="footer-link">Home</a>
                    <a href="about.php" class="footer-link">About Us</a>
                    <a href="products.php" class="footer-link">Products</a>
                    <a href="contact.php" class="footer-link">Contact</a>
                </div>
                
                <div class="col-md-3">
                    <h3 class="footer-title">Products</h3>
                    <a href="products.php?category=Men's Wallet" class="footer-link">Men's Wallet</a>
                    <a href="products.php?category=Belt" class="footer-link">Belts</a>
                    <a href="products.php?category=Backpacks" class="footer-link">Backpacks</a>
                    <a href="products.php?category=Duffel%20Bags" class="footer-link">Duffel Bags</a>
                </div>
                
                <div class="col-md-3">
                    <h3 class="footer-title">Contact Us</h3>
                    <p class="footer-text">
                        <i class="fas fa-map-marker-alt"></i> GURUGRAM, HARYANA, INDIA- 122002
                    </p>
                    <p class="footer-text">
                        <i class="fas fa-phone"></i> +91 9311106923
                    </p>
                    <p class="footer-text">
                        <i class="fas fa-envelope"></i> sales@blusolv.com
                    </p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Blusolv. All rights reserved. | Designed with passion and precision</p>
            </div>
        </div>
    </footer>

    <!-- Floating Contact Buttons -->
    <div class="floating-contact">
        <a href="https://api.whatsapp.com/send?phone=917738192732&text=Hi%20I%27m%20interested%20in%20Blusolv" class="contact-btn" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+919311106923" class="contact-btn" title="Call Us">
            <i class="fas fa-phone"></i>
        </a>
    </div>
    
    
      <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    

        <script>
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar-custom');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
    
            // Product carousel
            let currentSlide = 0;
            const track = document.getElementById('productTrack');
            const slides = document.querySelectorAll('.product-slide');
            const totalSlides = slides.length;
    
            function slideProducts(direction) {
                const slideWidth = slides[0].offsetWidth + 20; // width + gap
                currentSlide += direction;
                
                if (currentSlide < 0) {
                    currentSlide = 0;
                } else if (currentSlide > totalSlides - 4) {
                    currentSlide = totalSlides - 4;
                }
                
                track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
            }
            
               // Auto-slide every 2 seconds
        setInterval(() => {
            slideProducts(1);
        }, 2000);
    
            // Close mobile menu on link click
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                });
            });
            
            
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


    </script>
     
   
    </body>
</html>
   