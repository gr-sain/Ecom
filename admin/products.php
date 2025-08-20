<?php
$pageTitle = "Manage Products";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Product.php';


$functions->requireAdmin();

$product = new Product();
$message = '';

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $product->id = $_GET['delete_id'];
    if ($product->delete()) {
        $message = $functions->displaySuccess('Product deleted successfully.');
    } else {
        $message = $functions->displayError('Failed to delete product.');
    }
}

// Get all products
$products = $product->read();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Products</h1>
    <a href="<?php echo SITE_URL; ?>/admin/add_product.php" class="btn btn-primary">Add New Product</a>
</div>

<?php echo $message; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $row['image'] ? $row['image'] : 'placeholder.jpg'; ?>" alt="<?php echo $row['name']; ?>" width="50">
                </td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $functions->formatCurrency($row['price']); ?></td>
                <td><?php echo $row['stock_quantity']; ?></td>
                <td><?php echo $row['category_name']; ?></td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/admin/edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?php echo SITE_URL; ?>/admin/products.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>