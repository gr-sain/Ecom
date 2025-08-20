<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to update cart.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($product_id > 0 && $quantity > 0) {
        $cart = new Cart();
        $cart->user_id = $_SESSION['user_id'];
        $cart->product_id = $product_id;
        $cart->quantity = $quantity;
        
        if ($cart->updateQuantity()) {
            $cartTotal = $cart->getCartTotal();
            echo json_encode(['success' => true, 'message' => 'Cart updated.', 'cart_total' => $cartTotal]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>