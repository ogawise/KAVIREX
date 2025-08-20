<?phpsession_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAVIREX</title>
   <link rel="stylesheet" href="./assets/styles/style.css">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/logo.png">
    <style>
       main .main-index{
            min-height:100vh;
        }
    </style>
</head>
<body>
    <header>
        <nav>
             <div class="logo-container">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="GasGo" class="logo-img">
                <span class="tagline">Fast Gas Delivery</span>
            </a>
        </div>
            <ul>
                <li><a href="login_transit.php" class="login1">Login</a></li>
                <li><a href="./transit.php" class="signup1">SignUp</a></li>
            </ul>
        </nav>
    </header>
    <main class="main_index" style="min-height:80vh;">
        <section class="hero">
            <h1>ORDER GAS DELIVER FAST</h1>
            <p id="cta-p2">Get your gas cylinders delivered to your doorstep as soon as posible.</p>
            <a href="./transit.php" class="cta-button">Order Now</a>

        </section>
        <section class="text-cards">
            <div class="text-c">
                <h2>Fast Delivery</h2>
                <p>Get your gas as soon as you order, no deley just reliable</p>
            </div>
            <div class="text-c">
                <h2>Easy Ordering</h2>
                <p>Order in 3 clicks. No apps needed just your phone or computer.</p>
            </div>
            <div class="text-c">
                <h2> Safety Guaranteed</h2>
                <p>Certified cylinders and contactless delivery for your peace of mind.</p>
            </div>
       
            <div class="text-c">
                <h2>Pay on Delivery</h2>
                <p>Make your payment after you have recieved your product for transparency</p>
            </div>
            <div class="text-c">
                <h2>Order Tracking</h2>
                <p>Trace your order in real time untill it arive your doorstep.</p>
            </div>
            <div class="text-c">
                <h2> Quality</h2>
                <p>We offer high quality, durable and well filled gas.</p>
            </div>
        </section>
    </main>
    <?php include('./includes/footer.php'); ?>
    
  
</body>
</html>