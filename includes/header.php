<?php $currentPage=basename($_SERVER['REQUEST_URI'],'?'.$_SERVER['QUERY_STRING']); //use to get the curent file name eg product.php, view_cart.php
?>
<?php
$isLogedIn = isset($_SESSION['user_id']);
?>


    <header class="main-header">
        <!-- Logo Section -->
        <div class="logo-container">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="GasGo" class="logo-img">
                <span class="tagline">Fast Gas Delivery</span>
            </a>
        </div>

        <!-- Main Navigation -->
        <nav class="main-nav">
            <ul class="nav_link">
                <!-- note the class under is a php syntax that help return a page when it is true else it return an empty -->
                <li><a href="products.php" class="<?=($currentPage=='products.php')?'active':''?>">Order Gas</a></li> 
                <li><a href="view_cart.php"class="<?=($currentPage=='view_cart.php')?'active':''?>">My Orders</a></li>
                <li><a href="order_status.php"class="<?=($currentPage=='order_status.php')?'active':''?>">Order Status</a></li>
                </ul> 
        </nav>
        <div class="user-actions">
                <a href="logout.php" class="btn-logout">Logout</a>
        </div>

        <!-- Mobile Menu Button (Hidden on desktop) -->
        <button class="mobile-menu-btn">☰</button>
    </header>

    <script>
        // Mobile Menu Toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    </script>
