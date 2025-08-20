<?php
$pageTitle = "Manage Orders";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Order.php';


$functions->requireAdmin();

$order = new Order();
$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order->id = $_POST['order_id'];
    $order->status = $_POST['status'];
    
    if ($order->updateStatus()) {
        $message = $functions->displaySuccess('Order status updated successfully.');
    } else {
        $message = $functions->displayError('Failed to update order status.');
    }
}

// Get all orders
$orders = $order->read();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Orders</h1>
</div>

<?php echo $message; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                <td><?php echo $functions->formatCurrency($row['total_amount']); ?></td>
                <td>
                    <span class="badge bg-<?php 
                    switch($row['status']) {
                        case 'pending': echo 'warning'; break;
                        case 'processing': echo 'info'; break;
                        case 'shipped': echo 'primary'; break;
                        case 'delivered': echo 'success'; break;
                        case 'cancelled': echo 'danger'; break;
                        default: echo 'secondary';
                    }
                    ?>"><?php echo ucfirst($row['status']); ?></span>
                </td>
                <td>
                    <span class="badge bg-<?php echo $row['payment_status'] == 'completed' ? 'success' : 'warning'; ?>"><?php echo ucfirst($row['payment_status']); ?></span>
                </td>
                <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/admin/order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">View</a>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $row['id']; ?>">Update Status</button>
                    
                    <!-- Status Update Modal -->
                    <div class="modal fade" id="statusModal<?php echo $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Order Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $row['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $row['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $row['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>