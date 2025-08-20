<?php
interface OrderInterface {
    public function createOrder($userId, $items, $shippingAddress, $billingAddress);
    public function updateOrderStatus($orderId, $status);
    public function getOrderDetails($orderId);
    public function cancelOrder($orderId);
}
?>