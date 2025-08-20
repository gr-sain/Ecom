<?php
$pageTitle = "Admin Dashboard";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Order.php';


$functions->requireAdmin();

// Get statistics
$database = new Database();
$conn = $database->getConnection();

// Total users
$stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Total products
$stmt = $conn->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Total orders
$stmt = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

// Total revenue
$stmt = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders WHERE payment_status = 'completed'");
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?: 0;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text display-4"><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text display-4"><?php echo $total_products; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Orders</h5>
                <p class="card-text display-4"><?php echo $total_orders; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text display-4"><?php echo $functions->formatCurrency($total_revenue); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Orders</h5>
            </div>
            <div class="card-body">
                <?php
                $order = new Order();
                $orders = $order->read();
                $recent_orders = $orders->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (count($recent_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_orders, 0, 5) as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                <td><?php echo $functions->formatCurrency($order['total_amount']); ?></td>
                                <td><span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : 'warning'; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">View All Orders</a>
                <?php else: ?>
                <p class="text-muted">No orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline-primary">Manage Products</a>
                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-outline-success">Manage Orders</a>
                    <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-outline-info">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>