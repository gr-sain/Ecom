<?php
require_once 'Database.php';

class Payment {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $order_id;
    public $amount;
    public $payment_method;
    public $transaction_id;
    public $status;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function processPayment() {
        // This is a simplified payment processing method
        // In a real application, you would integrate with a payment gateway
        
        // Simulate payment processing
        $success = rand(0, 1); // 50% chance of success for demo
        
        if($success) {
            $this->status = 'completed';
            
            // Update order payment status
            $order = new Order();
            $order->id = $this->order_id;
            $order->payment_status = 'completed';
            
            if($order->updatePaymentStatus()) {
                return array('success' => true, 'message' => 'Payment processed successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to update order status');
            }
        } else {
            $this->status = 'failed';
            
            // Update order payment status
            $order = new Order();
            $order->id = $this->order_id;
            $order->payment_status = 'failed';
            $order->updatePaymentStatus();
            
            return array('success' => false, 'message' => 'Payment failed. Please try again.');
        }
    }

    public function createPaymentRecord() {
        $query = "INSERT INTO payments 
                SET order_id=:order_id, amount=:amount, 
                payment_method=:payment_method, transaction_id=:transaction_id, 
                status=:status";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":transaction_id", $this->transaction_id);
        $stmt->bindParam(":status", $this->status);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getPaymentHistory($user_id) {
        $query = "SELECT p.*, o.total_amount 
                FROM payments p 
                JOIN orders o ON p.order_id = o.id 
                WHERE o.user_id = ? 
                ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>