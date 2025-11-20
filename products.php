<?php
// Database Configuration
class Database {
    private $host = 'localhost';
    private $username = 'blusolv_db';
    private $password = 'blusolv_db';
    private $database = 'blusolv_db';
    public $connection;
    
    public function __construct() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
}

// Product Filter Page (products.php)

// Get filter parameters
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$material = isset($_POST['material']) ? trim($_POST['material']) : '';
$requirement = isset($_POST['requirement']) ? trim($_POST['requirement']) : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Filter Page</title>
    
    <?php include 'head.php'?>
    <style>
    
        
       /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--charcoal-grey) 100%);
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(212, 165, 116, 0.03) 35px, rgba(212, 165, 116, 0.03) 70px);
            pointer-events: none;
        }
        
        .hero-title {
            font-size: 4rem;
            margin-bottom: 1rem;
            font-weight: 300;
            letter-spacing: 8px;
            text-transform: uppercase;
            color: var(--cream-beige);
            position: relative;
            z-index: 1;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
            letter-spacing: 2px;
            color: var(--champagne-gold);
            position: relative;
            z-index: 1;
        }
     
        
      
           /* Filter Section */
        .filter-section {
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            padding: 40px;
            border-radius: 0;
            margin: 40px 0;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: 2px solid var(--soft-taupe);
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .filter-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 10px;
            color: var(--deep-navy);
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
        }
        
        .filter-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--soft-taupe);
            border-radius: 0;
            font-size: 15px;
            background-color: white;
            color: var(--charcoal-grey);
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
        }
        
        .filter-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .search-btn, .clear-btn {
            padding: 16px 50px;
            border: 2px solid transparent;
            border-radius: 0;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.4s ease;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        .search-btn {
            background: var(--deep-navy);
            color: var(--cream-beige);
            border-color: var(--deep-navy);
        }
        
        .search-btn::before {
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
        
        .search-btn:hover::before {
            left: 0;
        }
        
        .search-btn:hover {
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 165, 116, 0.3);
        }
        
        .clear-btn {
            background: transparent;
            color: var(--charcoal-grey);
            border-color: var(--charcoal-grey);
        }
        
        .clear-btn:hover {
            background: var(--charcoal-grey);
            color: var(--cream-beige);
            transform: translateY(-2px);
        }
        
        /* Active Filter Tags */
        .active-filter-tags {
            margin: 30px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }
        
        .filter-tag {
            background: linear-gradient(135deg, var(--deep-navy), var(--charcoal-grey));
            color: var(--cream-beige);
            padding: 10px 20px;
            border-radius: 0;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 1px;
            border: 1px solid var(--champagne-gold);
        }
        
        .filter-tag .remove-tag {
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .filter-tag .remove-tag:hover {
            color: var(--champagne-gold);
            transform: scale(1.2);
        }
        
   /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }
        
        .product-card {
            background: var(--cream-beige);
            border-radius: 0;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid var(--soft-taupe);
        }
        
        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            border-color: var(--champagne-gold);
        }
        
        .product-image-wrapper {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            transition: transform 0.5s ease;
            background: white;
            padding: 0px;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        
        .product-info {
            padding: 25px;
        }
        
        .product-title {
            font-size: 1.4rem;
            font-weight: 400;
            color: var(--deep-navy);
            margin-bottom: 15px;
            letter-spacing: 2px;
            font-family: 'Cormorant Garamond', serif;
        }
        
        .product-details {
            color: var(--slate-grey);
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .product-details strong {
            color: var(--charcoal-grey);
            font-weight: 500;
        }
        
        .product-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .view-details-btn, .enquire-now-btn {
            padding: 12px 24px;
            border: 2px solid transparent;
            border-radius: 0;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.4s ease;
            flex: 1;
            font-size: 0.8rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        .view-details-btn {
            background: var(--deep-navy);
            color: var(--cream-beige);
            border-color: var(--deep-navy);
        }
        
        .view-details-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--champagne-gold);
            transition: left 0.4s ease;
            z-index: 0;
        }
        
        .view-details-btn:hover::before {
            left: 0;
        }
        
        .view-details-btn:hover {
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }
        
        .enquire-now-btn {
            background: transparent;
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }
        
        .enquire-now-btn:hover {
            background: var(--champagne-gold);
            color: var(--deep-navy);
        }
        
        /* Loading */
        .loading {
            display: none;
            text-align: center;
            padding: 60px 20px;
            font-size: 1.2rem;
            color: var(--slate-grey);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(26, 35, 50, 0.9);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: linear-gradient(135deg, var(--cream-beige) 0%, var(--ivory) 100%);
            margin: 3% auto;
            padding: 45px;
            border-radius: 0;
            width: 90%;
            max-width: 600px;
            position: relative;
            border: 2px solid var(--champagne-gold);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        
        .modal-content h2 {
            font-size: 2rem;
            color: var(--deep-navy);
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .close {
            position: absolute;
            right: 25px;
            top: 20px;
            color: var(--slate-grey);
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .close:hover {
            color: var(--champagne-gold);
            transform: rotate(90deg);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--deep-navy);
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--soft-taupe);
            border-radius: 0;
            font-size: 15px;
            background-color: white;
            color: var(--charcoal-grey);
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--champagne-gold);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            background: var(--deep-navy);
            color: var(--cream-beige);
            padding: 16px 50px;
            border: 2px solid var(--deep-navy);
            border-radius: 0;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--champagne-gold);
            transition: left 0.4s ease;
            z-index: 0;
        }
        
        .submit-btn:hover::before {
            left: 0;
        }
        
        .submit-btn:hover {
            color: var(--deep-navy);
            border-color: var(--champagne-gold);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
                letter-spacing: 4px;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 25px;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .search-btn, .clear-btn {
                width: 100%;
            }
            
            .modal-content {
                padding: 30px;
                margin: 10% auto;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'?>
      <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Find Your Product</h1>
            <p class="hero-subtitle">Discover our premium collection of leather goods and lifestyle accessories</p>
        </div>
    </section>
    <div class="container">
        
              <!-- Active Filter Tags -->
        <div class="active-filter-tags" id="activeFilterTags"></div>
        
       <div class="filter-section">
            <div class="filter-grid">
            <div class="filter-group">
                <label for="category">CATEGORY</label>
                <select id="category" name="category">
                    <option value="">Select Category</option>
                    <option value="TOTE BAG">TOTE BAG</option>
                    <option value="HAND BAG">HAND BAG</option>
                    <option value="HOBO BAG">HOBO BAG</option>
                    <option value="SLING BAG">SLING BAG</option>
                    <option value="CLUTCH BAG">CLUTCH BAG</option>
                    <option value="WOMEN'S WALLET">WOMEN'S WALLET</option>
                    <option value="Women's Briefcase">Women's Briefcase</option>
                    <option value="Men's Wallet">Men's Wallet</option>
                    <option value="Belt">Belt</option>
                    <option value="Men's Briefcase">Men's Briefcase</option>
                    <option value="Backpacks">Backpacks</option>
                    <option value="Duffel Bags">Duffel Bags</option>
                    <option value="Duffel Bags with Trolley">Duffel Bags with Trolley</option>
                    <option value="Toiletry Pouch">Toiletry Pouch</option>
                    <option value="Hard Luggage">Hard Luggage</option>
                    <option value="Soft Luggage">Soft Luggage</option>
                    <option value="Rucksack Combo Pack">Rucksack Combo Pack</option>
                    <option value="Gym Bag">Gym Bag</option>
                    <option value="Key Chain">Key Chain</option>
                    <option value="Eyewear Case">Eyewear Case</option>
                    <option value="Coaster">Coaster</option>
                    <option value="Cigarette Case">Cigarette Case</option>
                    <option value="Earpod Case">Earpod Case</option>
                    <option value="Shopping Bags">Shopping Bags</option>
                    <option value="Gifting / Combo Pouches">Gifting / Combo Pouches</option>
                    <option value="Dealer / Trade Bags">Dealer / Trade Bags</option>
                </select>
            </div>
           
           <div class="filter-group">
                <label for="material">MATERIAL</label>
                <select id="material" name="material">
                    <option value="">Select Material</option>
                    <option value="Leather">Leather</option>
                    <option value="PU">PU</option>
                    <option value="Nylon">Nylon</option>
                    <option value="ABS / PC / PP">ABS / PC / PP</option>
                    <option value="Jute">Jute</option>
                    <option value="Canvas">Canvas</option>
                    <option value="JuCo">JuCo</option>
                    <option value="Woven Fabric">Woven Fabric</option>
                    <option value="Non Woven Fabric">Non Woven Fabric</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="requirement">NATURE OF REQUIREMENT</label>
                <select id="requirement" name="requirement">
                    <option value="">Select Requirement</option>
                    <option value="Brand Manufacturing">Brand Manufacturing</option>
                    <option value="Exports">Exports</option>
                    <option value="Personal Gifting">Personal Gifting</option>
                    <option value="Festive / Seasonal Gifting">Festive / Seasonal Gifting</option>
                    <option value="Corporate Gifting">Corporate Gifting</option>
                </select>
            </div>
             </div>
           
             <div class="filter-actions">
                <button class="search-btn" onclick="filterProducts()">
                    <i class="fas fa-search"></i> Search Products
                </button>
                <button class="clear-btn" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear Filters
                </button>
            </div>
        </div>
        
        <div class="loading" id="loading">
            <p>Loading products...</p>
        </div>
        
        <div class="products-grid" id="products-grid">
            <!-- Products will be loaded here via AJAX -->
        </div>
    </div>
    
    <!-- Enquiry Modal -->
    <div id="enquiryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Product Enquiry</h2>
            <form id="enquiryForm">
                <input type="hidden" id="product_id" name="product_id">
                <input type="hidden" id="product_name" name="product_name">
                
                <div class="form-group" style="background: linear-gradient(135deg, var(--champagne-gold) 0%, var(--soft-taupe) 100%); padding: 15px; margin-bottom: 25px; border-left: 4px solid var(--deep-navy);">
                    <p style="margin: 0; color: var(--deep-navy); font-weight: 600; font-size: 1rem;">Selected Product: <span id="display_product_name" style="color: var(--charcoal-grey); font-weight: 400;"></span></p>
                </div>
                
                <div class="form-group">
                    <label for="customer_name">Full Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_email">Email Address *</label>
                    <input type="email" id="customer_email" name="customer_email" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_phone">Phone Number *</label>
                    <input type="tel" id="customer_phone" name="customer_phone" required>
                </div>
                
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name">
                </div>
                
                <div class="form-group">
                    <label for="quantity">Expected Quantity *</label>
                    <select id="quantity" name="quantity" required>
                        <option value="">Select Quantity Range</option>
                        <option value="1-50">1-50 pieces</option>
                        <option value="51-100">51-100 pieces</option>
                        <option value="101-500">101-500 pieces</option>
                        <option value="501-1000">501-1000 pieces</option>
                        <option value="1000+">1000+ pieces</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="requirements">Additional Requirements</label>
                    <textarea id="requirements" name="requirements" placeholder="Please describe any specific requirements, customizations, or questions..."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Enquiry</button>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
     // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const urlCategory = urlParams.get('category') || '';
        const urlMaterial = urlParams.get('material') || '';
        const urlRequirement = urlParams.get('requirement') || '';
        
        
        $(document).ready(function() {
            
             // Set filter values from URL
            if (urlCategory) $('#category').val(urlCategory);
            if (urlMaterial) $('#material').val(urlMaterial);
            if (urlRequirement) $('#requirement').val(urlRequirement);
            
                // Show active filter tags
            updateFilterTags();
            
              // Load products with URL filters
            filterProducts();
            
            // Modal functionality
            const modal = document.getElementById('enquiryModal');
            const closeBtn = document.getElementsByClassName('close')[0];
            
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
            
            // Form submission
            $('#enquiryForm').on('submit', function(e) {
                e.preventDefault();
                submitEnquiry();
            });
        });
        
        
         function updateFilterTags() {
            let tagsHtml = '';
            const category = $('#category').val();
            const material = $('#material').val();
            const requirement = $('#requirement').val();
            
            if (category) {
                tagsHtml += `<span class="filter-tag">Category: ${category} <span class="remove-tag" onclick="removeFilter('category')">×</span></span>`;
            }
            if (material) {
                tagsHtml += `<span class="filter-tag">Material: ${material} <span class="remove-tag" onclick="removeFilter('material')">×</span></span>`;
            }
            if (requirement) {
                tagsHtml += `<span class="filter-tag">Requirement: ${requirement} <span class="remove-tag" onclick="removeFilter('requirement')">×</span></span>`;
            }
            
            $('#activeFilterTags').html(tagsHtml);
            
        }
        
        function removeFilter(filterType) {
            $('#' + filterType).val('');
            updateFilterTags();
            filterProducts();
        }
        
        
        function clearFilters() {
            $('#category').val('');
            $('#material').val('');
            $('#requirement').val('');
            updateFilterTags();
            
            // Update URL without parameters
            window.history.pushState({}, '', 'products.php');
            
            filterProducts();
        }
        
        
          function filterProducts() {
            updateFilterTags();
            // Add your AJAX call here
            console.log('Filtering products...');
        }
        
        function filterProducts() {
            $('#loading').show();
            $('#products-grid').hide();
            
            
            
            const category = $('#category').val();
            const material = $('#material').val();
            const requirement = $('#requirement').val();
            
            $.ajax({
                url: 'ajax_filter_products.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    category: category,
                    material: material,
                    requirement: requirement
                },
                success: function(response) {
                    $('#loading').hide();
                    $('#products-grid').show();
                    
                    if (response.success) {
                        displayProducts(response.products);
                    } else {
                        $('#products-grid').html('<p>Error loading products: ' + response.message + '</p>');
                    }
                },
                error: function() {
                    $('#loading').hide();
                    $('#products-grid').show();
                    $('#products-grid').html('<p>Error loading products. Please try again.</p>');
                }
            });
        }
        
    function displayProducts(products) {
    let html = '';
    
    if (products.length === 0) {
        html = '<p>No products found matching your criteria.</p>';
    } else {
        products.forEach(function(product) {
            html += `
                <div class="product-card" onclick="window.location.href='product-detail.php?id=${product.id}'">
                    <div class="product-image-wrapper">
                        <img src="${product.image || 'placeholder-image.jpg'}" alt="${product.name}" class="product-image">
                    </div>
                    <div class="product-info">
                        <div class="product-title">${product.name}</div>
                        <div class="product-details">
                            <strong>Category:</strong> ${product.category}<br>
                            <strong>Material:</strong> ${product.material}<br>
                            <strong>Code:</strong> ${product.product_code || 'N/A'}
                        </div>
                        <div class="product-actions">
                            <button class="view-details-btn" onclick="event.stopPropagation(); window.location.href='product-detail.php?id=${product.id}'">
                                View Details
                            </button>
                            <button class="enquire-now-btn" onclick="event.stopPropagation(); openEnquiry(${product.id}, '${product.name}')">
                                Enquire Now
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#products-grid').html(html);
}
        
        function openEnquiry(productId, productName) {
            $('#product_id').val(productId);
            $('#product_name').val(productName);
            $('#display_product_name').text(productName);
            $('#enquiryModal').show();
        }
        
        function submitEnquiry() {
            const formData = $('#enquiryForm').serialize();
            
            $.ajax({
                url: 'ajax_submit_enquiry.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Thank you for your enquiry! We will contact you soon.');
                        $('#enquiryModal').hide();
                        $('#enquiryForm')[0].reset();
                    } else {
                        alert('Error submitting enquiry: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error submitting enquiry. Please try again.');
                }
            });
        }
    </script>
       
     <?php include 'footer.php'?>
</body>
</html>