<?php
$pageTitle = "Product Details";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Product.php';
require_once '../config/config.php';


if (!isset($_GET['id'])) {
    $functions->redirect(SITE_URL . '/public/products.php');
}

$product = new Product();
$product->id = $_GET['id'];

if (!$product->readOne()) {
    $functions->redirect(SITE_URL . '/public/products.php');
}
?>
<div class="row">
    <div class="col-md-6">
        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $product->image ? $product->image : 'placeholder.jpg'; ?>" class="img-fluid" alt="<?php echo $product->name; ?>">
    </div>
    <div class="col-md-6">
        <h2><?php echo $product->name; ?></h2>
        <p class="text-muted">Category: <?php echo $product->category_id; ?></p>
        <p class="h3 text-primary"><?php echo $functions->formatCurrency($product->price); ?></p>
        <p><?php echo $product->description; ?></p>
        <p><strong>Stock: </strong> <?php echo $product->stock_quantity; ?> available</p>
        
        <?php if ($functions->isLoggedIn()): ?>
        <form id="add-to-cart-form" class="mt-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product->stock_quantity; ?>">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-lg mt-3 add-to-cart" data-product-id="<?php echo $product->id; ?>">
                Add to Cart
            </button>
        </form>
        <?php else: ?>
        <div class="alert alert-info">
            Please <a href="<?php echo SITE_URL; ?>/public/login.php">login</a> to add products to your cart.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>