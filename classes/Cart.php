<?php
require_once 'Database.php';

class Cart {
    private $conn;
    private $table_name = "cart";

    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getCartItems() {
        $query = "SELECT c.*, p.name, p.price, p.image, (c.quantity * p.price) as total 
                FROM " . $this->table_name . " c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    public function addToCart() {
        // Check if product already exists in cart
        $query = "SELECT id, quantity FROM " . $this->table_name . " 
                WHERE user_id = ? AND product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            // Update quantity if product exists
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $row['quantity'] + $this->quantity;
            
            $query = "UPDATE " . $this->table_name . " 
                    SET quantity = ? 
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $new_quantity);
            $stmt->bindParam(2, $row['id']);
        } else {
            // Insert new item
            $query = "INSERT INTO " . $this->table_name . " 
                    SET user_id=:user_id, product_id=:product_id, quantity=:quantity";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":quantity", $this->quantity);
        }
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateQuantity() {
        $query = "UPDATE " . $this->table_name . " 
                SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":product_id", $this->product_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function removeFromCart() {
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE user_id = ? AND product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function clearCart() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getCartTotal() {
        $query = "SELECT SUM(p.price * c.quantity) as total 
                FROM " . $this->table_name . " c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }

    public function getCartCount() {
        $query = "SELECT SUM(quantity) as count 
                FROM " . $this->table_name . " 
                WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ? $row['count'] : 0;
    }
}
?>