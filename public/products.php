<?php
$pageTitle = "Products";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Product.php';
require_once '../config/config.php';

$product = new Product();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

if (!empty($search)) {
    $products = $product->search($search);
    $totalProducts = $products->rowCount();
} else {
    $products = $product->read();
    $totalProducts = $products->rowCount();
}

// Get products for current page
$products = $product->read();
?>
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Our Products</h2>
    </div>
    <div class="col-md-4">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="row">
    <?php if ($products->rowCount() > 0): ?>
        <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $row['image'] ? $row['image'] : 'placeholder.jpg'; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['name']; ?></h5>
                    <p class="card-text"><?php echo substr($row['description'], 0, 100); ?>...</p>
                    <p class="card-text"><strong><?php echo $functions->formatCurrency($row['price']); ?></strong></p>
                    <a href="<?php echo SITE_URL; ?>/public/product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                    <?php if ($functions->isLoggedIn()): ?>
                    <button class="btn btn-outline-success add-to-cart" data-product-id="<?php echo $row['id']; ?>">Add to Cart</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">No products found.</div>
        </div>
    <?php endif; ?>
</div>

<?php
// Display pagination
echo $functions->paginate($totalProducts, $perPage, $page, SITE_URL . '/public/products.php');
?>

<?php require_once '../includes/footer.php'; ?>