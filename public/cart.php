<?php
$pageTitle = "Shopping Cart";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Cart.php';

$functions->requireLogin();

$cart = new Cart();
$cart->user_id = $_SESSION['user_id'];
$cartItems = $cart->getCartItems();
$cartTotal = $cart->getCartTotal();
?>
<h2 class="mb-4">Shopping Cart</h2>

<?php if ($cartItems->rowCount() > 0): ?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): ?>
            <tr id="cart-item-<?php echo $item['product_id']; ?>">
                <td>
                    <div class="d-flex align-items-center">
                        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $item['image'] ? $item['image'] : 'placeholder.jpg'; ?>" alt="<?php echo $item['name']; ?>" width="60" class="me-3">
                        <div><?php echo $item['name']; ?></div>
                    </div>
                </td>
                <td><?php echo $functions->formatCurrency($item['price']); ?></td>
                <td>
                    <input type="number" class="form-control quantity-input" data-product-id="<?php echo $item['product_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" style="width: 80px;">
                </td>
                <td><?php echo $functions->formatCurrency($item['total']); ?></td>
                <td>
                    <button class="btn btn-danger btn-sm remove-from-cart" data-product-id="<?php echo $item['product_id']; ?>">Remove</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td colspan="2"><strong><?php echo $functions->formatCurrency($cartTotal); ?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="<?php echo SITE_URL; ?>/public/products.php" class="btn btn-secondary">Continue Shopping</a>
    <a href="<?php echo SITE_URL; ?>/public/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
</div>
<?php else: ?>
<div class="alert alert-info">
    Your cart is empty. <a href="<?php echo SITE_URL; ?>/public/products.php">Browse products</a> to add items to your cart.
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>