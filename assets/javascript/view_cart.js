document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart from session and local storage
    let cart = JSON.parse(localStorage.getItem('cart')) || initialCart;
    updateCartUI();
    
    // Quantity controls
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const cartItem = this.closest('.cart-item');
            const productId = cartItem.dataset.productId;
            const price = parseFloat(cartItem.dataset.price);
            const qtyDisplay = cartItem.querySelector('.qty-display');
            let newQty = parseInt(qtyDisplay.textContent);
            
            // Adjust quantity
            if (this.classList.contains('plus')) {
                newQty += 1;
            } else if (this.classList.contains('minus') && newQty > 1) {
                newQty -= 1;
            }
            
            // Update cart
            await updateCartItem(productId, newQty, price, cartItem);
        });
    });
    
    // Remove item buttons
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const cartItem = this.closest('.cart-item');
            
            if (confirm('Remove this item from your cart?')) {
                removeCartItem(productId, cartItem);
            }
        });
    });
    //SUBMIT ORDER
  document.getElementById('place-order-btn').addEventListener('click', async function(e) {
    e.preventDefault();
    const orderBtn = this;
    
    // UI Feedback
    orderBtn.disabled = true;
    const originalText = orderBtn.textContent;
    orderBtn.textContent = 'Processing...';
    
    try {
        const response = await fetch('api/submit_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        // Handle HTTP errors
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || 'Order processing failed');
        }

        const data = await response.json();

        // Handle specific cases
        if (data.error === 'login_required') {
            window.location.href = data.redirect || 'userlogin.php';
            return;
        }

        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            window.location.href = `order_status.php?order_id=${data.order_id}`;
        }

    } catch (error) {
        console.error('Order Error:', error);
        
        const message = error.message.includes('Product ID') 
            ? 'One of the products is no longer available'
            : error.message;
            
        alert(`Order Failed: ${message}`);
       
        if (error.response) {
            error.response.json().then(data => console.log('Server details:', data));
        }
    } finally {
        orderBtn.disabled = false;
        orderBtn.textContent = originalText;
    }
});
    // Update cart item function
    async function updateCartItem(productId, newQty, price, cartItem) {
        try {
            const response = await fetch('api/update_item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQty,
                    price: price
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update local cart
                cart[productId] = newQty;
                saveCart();
                
                // Update UI
                cartItem.querySelector('.qty-display').textContent = newQty;
                cartItem.querySelector('.subtotal').textContent = 'FCFA ' + (price * newQty).toFixed(2);
                document.querySelector('.cart_total').textContent = 'Total: FCFA ' + result.new_total;
                updateCartCount(result.cart_count);
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('Failed to update quantity');
        }
    }
    
    // Remove item function
    async function removeCartItem(productId, cartItem) {
        try {
            const response = await fetch('api/remove_item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update local cart
                delete cart[productId];
                saveCart();
                
                // Animate removal
                cartItem.classList.add('cart-item-removing');
                setTimeout(() => cartItem.remove(), 300);
                
                // Update totals
                document.querySelector('.cart_total').textContent = 'Total: FCFA ' + result.new_total;
                updateCartCount(result.cart_count);
                
                // Show empty message if cart is now empty
                if (result.cart_count === 0) {
                    document.getElementById('empty-cart-message').style.display = 'block';
                    document.getElementById('place-order-btn').style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Removal error:', error);
            alert('Failed to remove item');
        }
    }
    
    // Save cart to localStorage
    function saveCart() {
        localStorage.setItem('cart', JSON.stringify(cart));
    }
    
    // Update cart count display
    function updateCartCount(count) {
        document.querySelectorAll('.cart_count').forEach(el => {
            el.textContent = count;
        });
    }
    
    // Update entire cart UI
    function updateCartUI() {
        const isEmpty = Object.keys(cart).length === 0;
        document.getElementById('empty-cart-message').style.display = isEmpty ? 'block' : 'none';
        document.getElementById('place-order-btn').style.display = isEmpty ? 'none' : 'block';
    }
});