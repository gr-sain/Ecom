<?php
require_once '../config/config.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to remove items from cart.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($product_id > 0) {
        $cart = new Cart();
        $cart->user_id = $_SESSION['user_id'];
        $cart->product_id = $product_id;
        
        if ($cart->removeFromCart()) {
            $cartCount = $cart->getCartCount();
            $cartTotal = $cart->getCartTotal();
            echo json_encode(['success' => true, 'message' => 'Product removed from cart.', 'cart_count' => $cartCount, 'cart_total' => $cartTotal]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product from cart.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>