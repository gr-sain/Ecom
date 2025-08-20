<?php
require_once 'Database.php';

class Order {
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";

    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $payment_status;
    public $shipping_address;
    public $billing_address;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        // Start transaction
        $this->conn->beginTransaction();

        try {
            // Insert order
            $query = "INSERT INTO " . $this->table_name . " 
                    SET user_id=:user_id, total_amount=:total_amount, 
                    shipping_address=:shipping_address, billing_address=:billing_address";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":total_amount", $this->total_amount);
            $stmt->bindParam(":shipping_address", $this->shipping_address);
            $stmt->bindParam(":billing_address", $this->billing_address);
            
            if(!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }
            
            $this->id = $this->conn->lastInsertId();
            
            // Insert order items
            $cart = new Cart();
            $cart->user_id = $this->user_id;
            $cart_items = $cart->getCartItems();
            
            while ($item = $cart_items->fetch(PDO::FETCH_ASSOC)) {
                $query = "INSERT INTO " . $this->items_table . " 
                        SET order_id=:order_id, product_id=:product_id, 
                        quantity=:quantity, price=:price";
                
                $stmt = $this->conn->prepare($query);
                
                $stmt->bindParam(":order_id", $this->id);
                $stmt->bindParam(":product_id", $item['product_id']);
                $stmt->bindParam(":quantity", $item['quantity']);
                $stmt->bindParam(":price", $item['price']);
                
                if(!$stmt->execute()) {
                    throw new Exception("Failed to add order items");
                }
            }
            
            // Clear cart
            if(!$cart->clearCart()) {
                throw new Exception("Failed to clear cart");
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            return false;
        }
    }

    public function read() {
        $query = "SELECT o.*, u.username, u.first_name, u.last_name 
                FROM " . $this->table_name . " o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT o.*, u.username, u.first_name, u.last_name, u.email, u.phone 
                FROM " . $this->table_name . " o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = ? 
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->user_id = $row['user_id'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            $this->payment_status = $row['payment_status'];
            $this->shipping_address = $row['shipping_address'];
            $this->billing_address = $row['billing_address'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function getUserOrders() {
        $query = "SELECT o.* 
                FROM " . $this->table_name . " o 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }

    public function getOrderItems() {
        $query = "SELECT oi.*, p.name, p.image 
                FROM " . $this->items_table . " oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt;
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                SET status = :status 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePaymentStatus() {
        $query = "UPDATE " . $this->table_name . " 
                SET payment_status = :payment_status 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":payment_status", $this->payment_status);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>