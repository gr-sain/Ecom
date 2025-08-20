<?php


$pageTitle = "Home";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/Product.php';

// Get featured products
$product = new Product();
$featuredProducts = $product->read();
?>
<div class="row">
    <div class="col-md-12">
        <div id="carouselExample" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://via.placeholder.com/1200x400/007bff/ffffff?text=Welcome+to+Our+Store" class="d-block w-100" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="https://via.placeholder.com/1200x400/28a745/ffffff?text=Amazing+Deals" class="d-block w-100" alt="Slide 2">
                </div>
                <div class="carousel-item">
                    <img src="https://via.placeholder.com/1200x400/dc3545/ffffff?text=New+Arrivals" class="d-block w-100" alt="Slide 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>

<h2 class="mb-4">Featured Products</h2>
<div class="row">
    <?php while ($row = $featuredProducts->fetch(PDO::FETCH_ASSOC)): ?>
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
</div>

<?php require_once '../includes/footer.php'; ?>