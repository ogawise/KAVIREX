document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.add-to-cart-form');
    
    // Initialize cart count from localStorage if available
    updateCartCount();
    
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                // Optimistic UI update - add to local storage first
                const productId = form.querySelector('[name="product_id"]').value;
                const quantity = form.querySelector('[name="quantity"]').value || 1;
                
                // Update local storage
                updateLocalCart(productId, quantity);
                
                // Then sync with server
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {'Accept': 'application/json'},
                    body: new FormData(form)
                });

                if (!response.ok) {
                    throw new Error('Server error');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Show notification
                    showNotification('Added to cart!');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error: ' + error.message, true);
            }
        });
    });
    
    // Helper functions
    function updateLocalCart(productId, quantity) {
        let cart = JSON.parse(localStorage.getItem('cart')) || {};
        
        if (cart[productId]) {
            cart[productId] += parseInt(quantity);
        } else {
            cart[productId] = parseInt(quantity);
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }
    
    function updateCartCount() {
        // Check both localStorage and session for count
        let localCount = 0;
        const localCart = JSON.parse(localStorage.getItem('cart')) || {};
        localCount = Object.values(localCart).reduce((a, b) => a + b, 0);
        
        document.querySelectorAll('.cart_count').forEach(el => {
            el.textContent = localCount;
        });
    }
    
    function showNotification(message, isError = false) {
        const notification = document.createElement('div');
        notification.className = `cart-notification ${isError ? 'error' : ''}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }
});