<?php
require_once 'Database.php';

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $image;
    public $category_id;
    public $stock_quantity;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function read() {
        $query = "SELECT p.*, c.name as category_name 
                FROM " . $this->table_name . " p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT p.id, p.name, p.description, p.price, p.category_id, 
                        p.image, p.stock_quantity, p.created_at
                FROM " . $this->table_name . " p
                WHERE p.id = :id
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        
        // Debug: Check if ID is set
        if (empty($this->id)) {
            error_log("Product readOne(): ID is empty");
            return false;
        }
        
        $stmt->bindParam(':id', $this->id);
        
        try {
            $stmt->execute();
            
            // Debug: Log the query and parameters
            error_log("Product readOne(): Query executed for ID: " . $this->id);
            error_log("Product readOne(): Row count: " . $stmt->rowCount());
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->name = $row['name'];
                $this->description = $row['description'];
                $this->price = $row['price'];
                $this->category_id = $row['category_id'];
                $this->image = $row['image'];
                $this->stock_quantity = $row['stock_quantity'];
                $this->created_at = $row['created_at'];
                
                return true;
            }
            
            return false;
            
        } catch(PDOException $e) {
            error_log("Product readOne(): Database error: " . $e->getMessage());
            return false;
        }
    }


    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET name=:name, description=:description, price=:price, 
                image=:image, category_id=:category_id, stock_quantity=:stock_quantity";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET name=:name, description=:description, price=:price, 
                image=:image, category_id=:category_id, stock_quantity=:stock_quantity 
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function search($keywords) {
        $query = "SELECT p.*, c.name as category_name 
                FROM " . $this->table_name . " p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? 
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        $stmt->execute();
        return $stmt;
    }
}
?>