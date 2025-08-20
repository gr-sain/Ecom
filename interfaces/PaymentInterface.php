<?php
interface PaymentInterface {
    public function processPayment($amount, $orderId, $paymentData);
    public function refundPayment($transactionId, $amount);
    public function getPaymentStatus($transactionId);
}
?>