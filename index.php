    <?php
    session_start();

    require_once 'db_connect.php'; // Make sure this connects $mysqli

    // Fetch categories from database
    $categories = $mysqli->query("SELECT * FROM main_categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

      <title>Lumineux Opticals — Home</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

      <!-- Local Bootstrap (kept as a local file you uploaded) -->
      <link href="bootstrap.min.css" rel="stylesheet">

      <!-- Inlined theme CSS (style.css you provided) -->
      <style>
    /********** Template CSS (inlined from style.css) **********/

    .back-to-top {
        position: fixed;
        display: none;
        right: 45px;
        bottom: 45px;
        z-index: 99;
    }


    /*** Spinner ***/
    #spinner {
        opacity: 0;
        visibility: hidden;
        transition: opacity .5s ease-out, visibility 0s linear .5s;
        z-index: 99999;
    }

    #spinner.show {
        transition: opacity .5s ease-out, visibility 0s linear 0s;
        visibility: visible;
        opacity: 1;
    }


    /*** Button ***/
    .btn {
        font-weight: 600;
        transition: .5s;
        border-radius: 50px;
    }

    .btn-square {
        width: 38px;
        height: 38px;
    }

    .btn-sm-square {
        width: 32px;
        height: 32px;
    }

    .btn-lg-square {
        width: 48px;
        height: 48px;
    }

    .btn-square,
    .btn-sm-square,
    .btn-lg-square {
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: inherit;
    }

    .btn-primary {
        color: var(--bs-white);
    }


    /*** Navbar ***/
    .navbar {
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 9;
        transition: .5s;
    }

    .navbar .navbar-nav .nav-link {
        margin-right: 25px;
        padding: 40px 0;
        color: var(--bs-white);
        font-size: 17px;
        text-transform: uppercase;
        outline: none;
        transition: .5s;
    }

    .navbar .navbar-nav .nav-link:hover,
    .navbar .navbar-nav .nav-link.active {
        color: var(--bs-primary);
    }

    @media (max-width: 991.98px) {
        .navbar .navbar-nav .nav-link,
        .navbar.bg-dark .navbar-nav .nav-link {
            margin-right: 0;
            padding: 10px 0;
        }

        .navbar .navbar-nav {
            margin-top: 8px;
            border-top: 1px solid var(--bs-light);
        }
    }

    @media (min-width: 992px) {
        .navbar.bg-dark .navbar-nav .nav-link {
            padding: 20px 0;
        }

        .navbar .nav-item .dropdown-menu {
            display: block;
            border: none;
            margin-top: 0;
            top: 150%;
            opacity: 0;
            visibility: hidden;
            transition: .5s;
        }

        .navbar .nav-item:hover .dropdown-menu {
            top: 100%;
            visibility: visible;
            transition: .5s;
            opacity: 1;
        }
    }

    .navbar .dropdown-toggle::after {
        border: none;
        content: "\f107";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        vertical-align: middle;
        margin-left: 8px;
    }


    /*** Header ***/
    .carousel-caption {
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        padding-top: 100px;
        background: rgba(0, 0, 0, .7);
        z-index: 1;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 10%;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 3rem;
        height: 3rem;
    }

    @media (max-width: 768px) {
        #header-carousel .carousel-item {
            position: relative;
            min-height: 550px;
        }
        
        #header-carousel .carousel-item img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    }

    .page-header {
        padding-top: 200px;
        background: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)), url(../img/carousel-1.jpg) top center no-repeat;
        background-size: cover;
    }

    .page-header .breadcrumb-item + .breadcrumb-item::before {
        color: var(--bs-white);
    }


    /*** Title ***/
    .title {
        margin-bottom: 2rem;
    }

    .title .title-left,
    .title .title-center,
    .title .title-right {
        display: inline-block;
        text-transform: uppercase;
        overflow: hidden;
    }

    .title .title-center {
        text-align: center;
    }

    .title .title-right {
        text-align: right;
    }

    .title .title-left h5,
    .title .title-center h5,
    .title .title-right h5 {
        position: relative;
        display: inline-block;
        font-size: 18px;
        font-weight: 300;
    }

    .title .title-left h5::after,
    .title .title-center h5::before,
    .title .title-center h5::after,
    .title .title-right h5::before {
        position: absolute;
        content: "";
        width: 500%;
        height: 0;
        top: 9px;
        border-bottom: 1px solid var(--bs-white);
    }

    .title .title-left h5::after,
    .title .title-center h5::after {
        left: calc(100% + 15px);
    }

    .title .title-right h5::before,
    .title .title-center h5::before {
        right: calc(100% + 15px);
    }

    .title .title-left h1,
    .title .title-center h1,
    .title .title-right h1 {
        border-bottom: 1px solid var(--bs-white);
    }


    /*** Service ***/
    .service-item {
        position: relative;
        margin-top: 2.5rem;
        overflow: hidden;
    }

    .service-item .service-img {
        position: relative;
        display: inline-block;
    }

    .service-item .service-img::before {
        position: absolute;
        content: "";
        width: calc(100% - 12rem);
        height: calc(100% - 12rem);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 3rem solid rgba(0, 0, 0, .5);
        border-radius: 300px;
        z-index: 1;
    }

    .service-item.service-item-left {
        border-radius: 500px 0 0 500px;
        background: linear-gradient(to right, var(--bs-secondary), var(--bs-dark));
    }

    .service-item.service-item-right {
        border-radius: 0 500px 500px 0;
        background: linear-gradient(to left, var(--bs-secondary), var(--bs-dark));
    }

    @media (max-width: 767.98px) {
        .service-item.service-item-left,
        .service-item.service-item-right {
            border-radius: 500px 500px 0 0;
            background: linear-gradient(to bottom, var(--bs-secondary), var(--bs-dark));
            text-align: center;
        }
    }


    /*** Team ***/
    .team-item {
        position: relative;
    }

    .team-item .team-name {
        position: absolute;
        width: 100%;
        height: 60px;
        left: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, .7);
    }

    .team-item .team-body {
        position: relative;
        overflow: hidden;
    }

    .team-item .team-body .team-before,
    .team-item .team-body .team-after {
        position: absolute;
        content: "";
        width: 0;
        height: calc(100% - 60px);
        top: 0;
        left: 0;
        background: rgba(0, 0, 0, .7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: .5s;
    }

    .team-item .team-body .team-after {
        left: auto;
        right: 0;
    }

    .team-item .team-body .team-before {
        text-align: right;
    }

    .team-item:hover .team-body .team-before,
    .team-item:hover .team-body .team-after {
        width: 50%;
    }

    .team-item .team-body .team-before span,
    .team-item .team-body .team-after span {
        margin: 5px;
        color: var(--bs-white);
        opacity: 0;
        transition: .5s;
    }

    .team-item:hover .team-body .team-before span,
    .team-item:hover .team-body .team-after span {
        opacity: 1;
        transition-delay: .2s;
    }


    /*** Testimonial ***/
    .testimonial-carousel {
        max-width: 700px;
        margin: 0 auto;
    }

    .testimonial-carousel .owl-dots {
        margin-top: 35px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .testimonial-carousel .owl-dots .owl-dot {
        width: 60px;
        height: 60px;
        margin: 0 5px;
        padding: 10px;
        background: var(--bs-dark);
        border-radius: 100px;
        transition: .5s;
    }

    .testimonial-carousel .owl-dots .owl-dot.active {
        width: 100px;
        height: 100px;
    }

    .testimonial-carousel .owl-dots .owl-dot img {
        opacity: .1;
        transition: .5s;
        border-radius: 100px;
    }

    .testimonial-carousel .owl-dots .owl-dot.active img {
        opacity: 1;
    }


    /*** Footer ***/
    @keyframes footerAnimatedBg {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: -1000px 0;
        }
    }

    .footer {
        background-image: url(../img/footer-bg.png);
        background-position: 0px 0px;
        background-repeat: repeat-x;
        animation: footerAnimatedBg 50s linear infinite;
    }

    /* Small local overrides to blend with Lumineux */
    .brand-small { font-family: 'Playfair Display', serif; font-weight: 600; color: #fff; }
    .navbar { background: transparent; }
    .navbar.position-fixed { position: fixed !important; top: 0; left: 0; right: 0; }
    .carousel-caption .display-1 { font-family: 'Josefin Sans', sans-serif; font-weight:700; }
    .card-img-top { object-fit: cover; height:230px; }
    @media (max-width: 768px) { .card-img-top { height:160px; } }

    /* Basic layout padding tweaks */
    .container-fluid.py-5 { padding-top:3rem; padding-bottom:3rem; }
      </style>
    </head>
    <body>

      <!-- Spinner -->
      <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

      <!-- Header / Navbar Start -->
      <div class="container-fluid p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-lg-5">
            <a href="index.php" class="navbar-brand ms-4 ms-lg-0 d-flex align-items-center">
                <img src="assets/images/FullLogo1.png" alt="Lumineux Logo" style="height:42px; margin-right:10px;">
                <div class="brand-small">Lumineux Opticals</div>
            </a>

            <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">

                <div class="navbar-nav mx-auto p-4 p-lg-0">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="about.php" class="nav-item nav-link">About</a>

                    <!-- Categories Dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="catsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="catsDropdown">
                            <?php if(!empty($categories)): ?>
                                <?php foreach($categories as $cat): ?>
                                    <li><a class="dropdown-item" href="category.php?id=<?= intval($cat['id']) ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">No categories</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                </div>


                <!-- Desktop icons -->
                <div class="d-none d-lg-flex align-items-center gap-2 me-3">

                    

                    <?php if(isset($_SESSION['user_id'])): ?>
                        
                    <?php else: ?>
                    <?php endif; ?>

                    
<div class="d-flex align-items-center ms-auto" style="gap:14px;">

    <a href="cart.php"
       style="width:42px;height:42px;border-radius:50%;
              display:flex;align-items:center;justify-content:center;
              background:rgba(255,255,255,0.12);
              color:#fff;text-decoration:none;
              transition:0.2s;">
        <i class="fa-solid fa-bag-shopping"></i>
    </a>

    <a href="wishlist.php"
       style="width:42px;height:42px;border-radius:50%;
              display:flex;align-items:center;justify-content:center;
              background:rgba(255,255,255,0.12);
              color:#ff69b4;text-decoration:none;
              transition:0.2s;">
        <i class="fa-solid fa-heart"></i>
    </a>

    
</div>

                    <!-- Profile Dropdown (DESKTOP ONLY) -->
                    <div class="nav-item dropdown">
                        <button class="btn btn-outline-light border-2 dropdown-toggle" id="profileDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" style="padding:6px 12px;">
                            <i class="fa-regular fa-circle-user"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="wishlist.php">Wishlist</a></li>
                                <li><a class="dropdown-item" href="cart.php">Cart</a></li>
                                <li><a class="dropdown-item" href="user-order-history.php">Orders</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="login.php">Login</a></li>
                                <li><a class="dropdown-item" href="register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>


                <!-- Mobile buttons -->
                <div class="d-lg-none w-100 px-4 pb-3">
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-light w-100" href="shop.php">Shop</a>
                        <a class="btn btn-light w-100" href="login.php">Account</a>
                    </div>
                </div>

            </div>
        </nav>
    </div>

        <!-- Header carousel -->
        <div id="header-carousel" class="carousel slide" data-ride="carousel" aria-label="Eyewear carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="w-100" src="assets/images/slider-image1.jpg" alt="Eyewear Banner 1">
              <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="title mx-5 px-5">
                  <div class="title-center">
                    <h5>Discover</h5>
                    <h1 class="display-1">Premium Eyewear</h1>
                  </div>
                </div>
                <p class="fs-5 mb-4">Curated luxury frames & lenses — crafted for style and comfort.</p>
              </div>
            </div>

            <div class="carousel-item">
              <img class="w-100" src="assets/images/slider-image2.jpeg" alt="Eyewear Banner 2">
              <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="title mx-5 px-5">
                  <div class="title-center">
                    <h5>New Arrivals</h5>
                    <h1 class="display-1">Season Collection</h1>
                  </div>
                </div>
                <p class="fs-5 mb-4">Timeless designs for men & women — explore the collection.</p>
              </div>
            </div>

            <div class="carousel-item">
              <img class="w-100" src="assets/images/slider-image3.jpg" alt="Eyewear Banner 3">
              <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="title mx-5 px-5">
                  <div class="title-center">
                    <h5>Signature</h5>
                    <h1 class="display-1">Designer Frames</h1>
                  </div>
                </div>
                <p class="fs-5 mb-4">Premium materials. Expert craftsmanship. Limited editions.</p>
              </div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-action="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-action="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
      <!-- Header End -->

      <!-- ABOUT -->
      <div class="container-fluid bg-light py-5">
      <div class="container">
        <div class="row g-5 align-items-center">
          <div class="col-lg-7 py-5">
            <h5 style="color: #000; font-size: 22px;">About</h5>
            <h1 style="color: #000; font-size: 42px; font-weight: 700;">Luxury Eyewear, Exceptional Service</h1>
            <p class="mb-4" style="color: #333; font-size: 18px;">
              Lumineux Opticals offers handpicked frames and lenses, personalised fittings, and aftercare. Quality craftsmanship combined with modern aesthetics.
            </p>

            <ul class="list-unstyled mb-4" style="color: #333; font-size: 16px;">
              <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Wide selection of designer frames</li>
              <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Expert eye testing and fittings</li>
              <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i>Fast shipping & easy returns</li>
            </ul>

            <div class="row">
              
              <div class="col-6">
                <a href="contact.php" class="btn btn-primary w-100 py-3" style="font-size: 18px;">For any Query </a>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <img class="img-fluid" src="assets/images/FullLogo.png" alt="About Lumineux">
          </div>
        </div>
      </div>
    </div>


      <!-- CATEGORIES -->
      <!-- Category Section Replaced With Video -->
<div class="container-fluid p-0">
    <video autoplay muted loop playsinline style="width:100%; height:auto; display:block; object-fit:cover;">
        <source src="assets/videos/eyewear2.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>
</div>


      <!-- FEATURED PRODUCTS -->
      <div class="container-fluid py-5 bg-light">
        <div class="container">
          <div class="text-center mb-4">
            <h2>Featured</h5>
            <h1>Up Coming Sunglasses</h1>
          </div>

          <div class="row g-4">
            <!-- Static example products; replace with DB loop if you want -->
            <div class="col-lg-3 col-md-6">
              <div class="card h-100">
                <img class="card-img-top" src="assets/images/product-1.jpg" alt="Product 1">
                <div class="card-body text-center">
                  <h5 class="card-title"style="color: black;">Aviator Classic</h5>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card h-100">
                <img class="card-img-top" src="assets/images/product-2.jpg" alt="Product 2">
                <div class="card-body text-center">
                  <h5 class="card-title" style="color: black;">Round Vintage</h5>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card h-100">
                <img class="card-img-top" src="assets/images/product-3.jpg" alt="Product 3">
                <div class="card-body text-center">
                  <h5 class="card-title"style="color: black;">Rectangular Edge</h5>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card h-100">
                <img class="card-img-top" src="assets/images/product-4.jpg" alt="Product 4">
                <div class="card-body text-center">
                  <h5 class="card-title"style="color: black;">Signature Cat-Eye</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      

<!-- FOOTER -->
<footer style="background:#000; padding:80px 0 40px; color:#fff;">
    <div class="container">
        <div class="row">

            <!-- Column 1: Logo + Brand + Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h4 style="color:#ff69b4; font-weight:700;">Lumineux Opticals</h4>
                <p style="color:#ccc;">
                    Discover premium eyewear crafted for clarity, comfort, and unmatched luxury. 
                    Elevate your vision with style.
                </p>
                <div class="footer-social mt-3" style="display:flex; gap:15px;">
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color:#ff69b4; font-size:20px;"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Column 2: About -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">About</h5>
                <p style="color:#ccc;">
                    Lumineux Opticals offers high-quality frames and lenses to elevate your style while ensuring clarity and comfort.
                </p>
            </div>

            <!-- Column 4: Quick Links -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">Quick Links</h5>
                <ul style="list-style:none; padding:0;">
                    <li><a href="index.php" style="color:#ccc; text-decoration:none;">Home</a></li>
                    <li><a href="about.php" style="color:#ccc; text-decoration:none;">About</a></li>
                    <li><a href="category.php" style="color:#ccc; text-decoration:none;">Category</a></li>
                    <li><a href="contact.php" style="color:#ccc; text-decoration:none;">Contact</a></li>
                </ul>
            </div>


            <!-- Column 3: Contact -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 style="font-weight:700; color:#ff69b4;">Contact Us</h5>
                <p style="color:#ccc;">
                    <i class="fas fa-map-marker-alt" style="margin-right:8px;"></i> Ahmedabad, Gujarat, India<br>
                    <i class="fas fa-phone" style="margin-right:8px;"></i> +91 98765 43210<br>
                    <i class="fas fa-envelope" style="margin-right:8px;"></i> support@lumineux.com
                </p>
            </div>

            
        </div>

        <hr style="border-color:#333; margin:30px auto; width:80%;">

        <p class="text-center text-muted" style="color:#777;">
            © 2026 Lumineux Opticals — All Rights Reserved.
        </p>
    </div>
</footer>


    <!-- Scroll to Top -->
    <a href="#" id="scrollTopBtn" class="scroll-top"> ↑ </a>

    <!-- Scripts -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/popper.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/others/plugins.js"></script>
    <script src="js/active.js"></script>

    <script>
        // Scroll to top
        const scrollBtn = document.getElementById("scrollTopBtn");
        window.addEventListener("scroll", () => {
            scrollBtn.style.display = (window.pageYOffset > 300) ? "block" : "none";
        });
        scrollBtn.addEventListener("click", (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    </script>
</body>
</html>



      <!-- Back to Top -->
      <a href="#" id="backToTop" class="btn btn-outline-primary border-2 btn-lg-square back-to-top width-2xl">
      <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 24 24"><path fill="currentColor" d="M8.53 10.53a.75.75 0 1 1-1.06-1.06l4-4a.75.75 0 0 1 1.06 0l4 4a.75.75 0 1 1-1.06 1.06l-2.72-2.72v9.69a.75.75 0 0 1-1.5 0V7.81z"/></svg>
    </a>


      <!-- Inlined JS: converts theme JS to vanilla JS (no jQuery dependency) -->
      <script>
      (function(){
        "use strict";

        // Spinner: hide immediately
        document.addEventListener('DOMContentLoaded', function() {
          var s = document.getElementById('spinner');
          if(s) {
            s.classList.remove('show');
            // ensure it's hidden after tiny delay
            setTimeout(function(){ s.style.display = 'none'; }, 300);
          }
        });

        // Sticky Navbar on scroll
        function handleNavbarSticky() {
          var navbar = document.querySelector('.navbar');
          if(!navbar) return;
          if(window.scrollY > 0) {
            navbar.classList.add('position-fixed', 'bg-dark', 'shadow-sm');
          } else {
            navbar.classList.remove('position-fixed', 'bg-dark', 'shadow-sm');
          }
        }
        window.addEventListener('scroll', handleNavbarSticky);
        handleNavbarSticky();

        // Back to top button
        var backBtn = document.querySelector('.back-to-top');
        function handleBackBtn() {
          if(window.scrollY > 300) {
            backBtn && (backBtn.style.display = 'inline-flex');
          } else {
            backBtn && (backBtn.style.display = 'none');
          }
        }
        window.addEventListener('scroll', handleBackBtn);
        handleBackBtn();

        if(backBtn){
          backBtn.addEventListener('click', function(e){
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
          });
        }

        // Simple header carousel (vanilla)
        (function(){
          var carousel = document.getElementById('header-carousel');
          if(!carousel) return;
          var slides = carousel.querySelectorAll('.carousel-item');
          var prevBtn = carousel.querySelector('[data-action="prev"]');
          var nextBtn = carousel.querySelector('[data-action="next"]');
          var index = 0;
          var total = slides.length;
          function show(i){
            slides.forEach(function(s, idx){
              s.classList.toggle('active', idx === i);
            });
          }
          function next(){
            index = (index + 1) % total;
            show(index);
          }
          function prev(){
            index = (index - 1 + total) % total;
            show(index);
          }
          // auto rotate
          var interval = setInterval(next, 3500);
          if(nextBtn) nextBtn.addEventListener('click', function(){ next(); clearInterval(interval); });
          if(prevBtn) prevBtn.addEventListener('click', function(){ prev(); clearInterval(interval); });
          show(index);
        })();

        // Simple testimonial carousel (vanilla)
        (function(){
          var wrap = document.querySelector('.testimonial-carousel');
          if(!wrap) return;
          var items = Array.prototype.slice.call(wrap.querySelectorAll('.testimonial-item'));
          if(items.length === 0) return;
          var tIndex = 0;
          function showTest(i){
            items.forEach(function(it, idx){
              it.style.display = (idx === i) ? 'block' : 'none';
            });
          }
          showTest(tIndex);
          setInterval(function(){
            tIndex = (tIndex + 1) % items.length;
            showTest(tIndex);
          }, 4000);
        })();

      })();
      </script>

      <!-- FontAwesome icons (using CDN) -->
      <!-- If you don't want external CDN for icons, replace icons with images or local icon font -->
      <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>

      <script>
      // Simple accessibility: focus outlines for keyboard users
      document.documentElement.classList.add('js-enabled');
      </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>
    </html>