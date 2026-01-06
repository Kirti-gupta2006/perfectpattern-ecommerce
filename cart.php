<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Handle updating quantities or removing items from cart
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]); // Remove item if quantity is 0 or less
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    header("Location: cart.php"); // Redirect to prevent form resubmission
    exit();
}

if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php"); // Redirect
    exit();
}

// Calculate total items in cart for the navbar icon (same as index.php)
$cart_item_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_item_count += $item['quantity'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - PerfectPattern</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <style>
    body{
        background-color: #544646de;
        padding-top: 56px;
        color: rgb(218, 211, 212);
    }
    .custom-navbar {
        background-color: #333;
        border-bottom: 2px solid #a69f9f;
        position: fixed;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1030;
    }
    .custom-navbar .navbar-brand,
    .custom-navbar .nav-link {
        color: rgb(218, 211, 212);
        font-weight: bold;
    }
    .custom-navbar .nav-link:hover {
        color: #ddd;
        background-color: #555;
        border-radius: 5px;
    }
    .cart-item-card {
        background-color: #a69f9f;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 8px;
        color: #333; /* Darker text for readability on lighter card */
        display: flex; /* For image and text alignment */
        align-items: center;
    }
    .cart-item-card img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
        border: 1px solid #777;
    }
    .cart-summary {
        background-color: #333;
        padding: 20px;
        border-radius: 8px;
        color: rgb(218, 211, 212);
    }
    .cart-summary h4, .cart-summary p {
        margin-bottom: 10px;
    }
    /* Style for quantity input */
    .quantity-input {
        width: 60px; /* Adjust as needed */
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
    }
    .btn-remove-item {
        background-color: #dc3545; /* Red for remove */
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-remove-item:hover {
        background-color: #c82333;
    }
    </style>
</head>
<body>

<!-- Navbar (Same as index.php) -->
<nav class="navbar navbar-expand-lg custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">PerfectPattern</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#dresses-section">Dresses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#tops-section">Tops</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#bottomwear-section">Bottomwear</a>
        </li>
        <!-- Cart Icon -->
        <li class="nav-item">
            <a class="nav-link" href="cart.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <span class="badge bg-secondary rounded-pill"><?php echo $cart_item_count; ?></span>
            </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info text-center" role="alert">
            Your cart is empty. <a href="index.php" class="alert-link">Start shopping now!</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <?php
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <div class="cart-item-card d-flex align-items-center">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid">
                        <div class="flex-grow-1">
                            <h5><?php echo $item['name']; ?></h5>
                            <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                            <div class="d-flex align-items-center">
                                <!-- Form for quantity update -->
                                <form method="post" action="cart.php" class="d-flex align-items-center me-2">
                                    <label for="quantity_<?php echo $product_id; ?>" class="me-2">Quantity:</label>
                                    <input type="number" name="quantity[<?php echo $product_id; ?>]" id="quantity_<?php echo $product_id; ?>" value="<?php echo $item['quantity']; ?>" min="1" class="form-control quantity-input">
                                    <button type="submit" name="update_cart" class="btn btn-sm btn-info ms-2">Update</button>
                                </form>
                                <!-- Separate form for remove item -->
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" name="remove_item" class="btn-remove-item btn-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                        <h5 class="ms-auto">₹<?php echo number_format($item_total, 2); ?></h5>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-md-4">
                <div class="cart-summary">
                    <h4>Cart Summary</h4>
                    <hr>
                    <p>Subtotal: <span class="float-end">₹<?php echo number_format($subtotal, 2); ?></span></p>
                    <p>Shipping: <span class="float-end">₹50.00</span></p> <!-- Example fixed shipping -->
                    <hr>
                    <h4>Total: <span class="float-end">₹<?php echo number_format($subtotal + 50, 2); ?></span></h4>
                    <a href="checkout.php" class="btn btn-primary w-100 mt-3">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container mt-5 py-3 border-top" style="grid-area: footer;">
      <footer>
        <p class="text-center text-white ">© 2025 , Kirti Gupta</p>
      </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close(); // Close the database connection
?>