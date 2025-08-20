$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        let quantity = 1;
        
        // Check if quantity input exists
        const quantityInput = $('#quantity');
        if (quantityInput.length) {
            quantity = parseInt(quantityInput.val());
            if (quantity < 1) {
                alert('Please enter a valid quantity.');
                return;
            }
        }
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/ajax/add_to_cart.php',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update cart count
                    $('.badge.bg-danger').text(data.cart_count);
                    alert('Product added to cart successfully!');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
});