<?php
$isLogedIn = isset($_SESSION['user_id']);
?>


    <header class="main-header">
        <!-- Logo Section -->
        <div class="logo-container">
            <a href="index.php">
                <img src="../assets/images/logo.png" alt="GasGo" class="logo-img">
                <span class="tagline">Fast Gas Delivery</span>
            </a>
        </div>

     
        <div class="user-actions">
                <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <script>
        // Mobile Menu Toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    </script>
