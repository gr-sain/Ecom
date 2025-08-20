<?php
require_once '../config/config.php';
require_once '../classes/Order.php';
require_once '../classes/Payment.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to place orders.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_data = json_decode(file_get_contents('php://input'), true);
    
    $order = new Order();
    $order->user_id = $_SESSION['user_id'];
    $order->total_amount = $order_data['total_amount'];
    $order->shipping_address = $order_data['shipping_address'];
    $order->billing_address = $order_data['billing_address'] ?? $order_data['shipping_address'];
    
    if ($order->create()) {
        // Process payment
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->amount = $order->total_amount;
        $payment->payment_method = $order_data['payment_method'];
        
        $result = $payment->processPayment();
        
        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Order placed successfully.', 'order_id' => $order->id]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create order.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>