$(document).ready(function() {
    // Update quantity
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val());
        
        if (quantity < 1) {
            alert('Quantity must be at least 1.');
            $(this).val(1);
            return;
        }
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/ajax/update_cart.php',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Reload page to update totals
                    location.reload();
                } else {
                    alert(data.message);
                    location.reload();
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                location.reload();
            }
        });
    });
    
    // Remove from cart
    $('.remove-from-cart').on('click', function() {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }
        
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/ajax/remove_from_cart.php',
            method: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Remove item from table
                    $(`#cart-item-${productId}`).remove();
                    
                    // Update cart count
                    $('.badge.bg-danger').text(data.cart_count);
                    
                    // Update total
                    $('tfoot td:last').html(`<strong>${data.cart_total}</strong>`);
                    
                    // If cart is empty, show message
                    if (data.cart_count == 0) {
                        $('.table-responsive').html(`
                            <div class="alert alert-info">
                                Your cart is empty. <a href="<?php echo SITE_URL; ?>/public/products.php">Browse products</a> to add items to your cart.
                            </div>
                        `);
                    }
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});