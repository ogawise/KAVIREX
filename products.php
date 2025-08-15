<?php session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAVIREX</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <script src="assets/javascript/cart.js" defer></script> 
     <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/logo.png">
     <head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/database.php');?>

    <main style="  min-height: 70vh;">
        <section class="product_c">
            <h1>Choose your favorite gas cylinder</h1>
            <div class="product_grid">
                <?php
                // fetch product from database  <a href="order.php?product_id='.$product['product_id'].'" class="order_btn">Place Order</a>
                $query="SELECT*FROM products WHERE stock>0";
                $result=$connection->query($query);
                while($product=$result->fetch_assoc()){
                    echo'
                 <div class="product_card">
                    <img src="'.$product['image_path'].'" alt="'.$product['name'].'">
                    <h3> '.$product['name'].'</h3>
                    <p class="price">'.$product['price'].'</p>
                    <p class="stock">In stock:'.$product['stock'].'</p>
                    <form method="post" class="add-to-cart-form">
                    <div class="card_controls">
                    <input type="number" name="quantity" value="1" min="1" max="'.$product['stock'].'" class="qqty_input">
                    <input type="hidden" name="product_id" value="'.$product['product_id'].'">
                     <input type="hidden" name="price" value="'.$product['price'].'">
                      <input type="hidden" name="name" value="'.htmlspecialchars($product['name']).'">
                    <button type="submit" class="add_to_cart" > Add to card</button>
                    </div>
                    </form>
                 </div>';
                }
                ?>
            </div>
        </section>
    </main>
                <div class="cart_summary" id="card_summery">
                    <span class="cart_count">0</span>Items
                    <a href="view_cart.php">View Cart</a>
                </div>
    <?php include('./includes/footer.php');?>
</body>
</html>