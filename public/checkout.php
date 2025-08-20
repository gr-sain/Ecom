<?php
$pageTitle = "Checkout";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Cart.php';


$functions->requireLogin();

$cart = new Cart();
$cart->user_id = $_SESSION['user_id'];
$cartItems = $cart->getCartItems();
$cartTotal = $cart->getCartTotal();

if ($cartItems->rowCount() == 0) {
    $functions->redirect(SITE_URL . '/public/cart.php');
}

$user = new User();
$user->id = $_SESSION['user_id'];
$user->emailExists(); // This will load user data

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order = new Order();
    $order->user_id = $_SESSION['user_id'];
    $order->total_amount = $cartTotal;
    $order->shipping_address = $_POST['shipping_address'];
    $order->billing_address = $_POST['billing_address'] ? $_POST['billing_address'] : $_POST['shipping_address'];
    
    if ($order->create()) {
        // Process payment
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->amount = $order->total_amount;
        $payment->payment_method = $_POST['payment_method'];
        
        $result = $payment->processPayment();
        
        if ($result['success']) {
            $_SESSION['success_message'] = 'Order placed successfully! Your order ID is: ' . $order->id;
            $functions->redirect(SITE_URL . '/public/order_success.php?id=' . $order->id);
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Failed to create order. Please try again.';
    }
}
?>
<h2 class="mb-4">Checkout</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $cartItems->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $functions->formatCurrency($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo $functions->formatCurrency($item['total']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong><?php echo $functions->formatCurrency($cartTotal); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Shipping Information</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo $user->address; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="billing_address" class="form-label">Billing Address (if different)</label>
                        <textarea class="form-control" id="billing_address" name="billing_address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Order Total</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span><?php echo $functions->formatCurrency($cartTotal); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total:</strong>
                    <strong><?php echo $functions->formatCurrency($cartTotal); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>