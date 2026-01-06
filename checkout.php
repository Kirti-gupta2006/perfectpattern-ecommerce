<?php
session_start();
include 'db_connect.php';

// Check if cart is empty, redirect to cart if no items to checkout
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate total for display on checkout page
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_cost = 50.00; // Example fixed shipping
$total_amount = $subtotal + $shipping_cost;

// Handle "Place Order" if form was submitted
if (isset($_POST['place_order'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $shipping_address = $_POST['shipping_address'];

    // --- Start Transaction (for atomicity) ---
    $conn->begin_transaction();

    try {
        // 1. Insert into 'orders' table
        $stmt_order = $conn->prepare("INSERT INTO orders (customer_name, customer_email, shipping_address, total_amount) VALUES (?, ?, ?, ?)");
        $stmt_order->bind_param("sssd", $customer_name, $customer_email, $shipping_address, $total_amount);
        $stmt_order->execute();
        $order_id = $conn->insert_id; // Get the ID of the newly inserted order

        // 2. Insert into 'order_items' table for each item in the cart
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
        $stmt_item->bind_param("iiid", $order_id_param, $product_id_param, $quantity_param, $price_at_order_param);

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $order_id_param = $order_id;
            $product_id_param = $product_id;
            $quantity_param = $item['quantity'];
            $price_at_order_param = $item['price']; // Store the price at the time of order

            $stmt_item->execute();
        }

        $conn->commit(); // Commit the transaction if all inserts were successful

        unset($_SESSION['cart']); // Clear the cart after successful order placement
        $_SESSION['order_success'] = "Your order (ID: #{$order_id}) has been placed successfully!";
        header("Location: confirmation.php"); // Redirect to a final confirmation page
        exit();

    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction on error
        $_SESSION['order_error'] = "Error placing your order. Please try again. " . $e->getMessage();
        header("Location: checkout.php"); // Redirect back to checkout with error
        exit();
    } finally {
        // Close prepared statements
        if (isset($stmt_order)) $stmt_order->close();
        if (isset($stmt_item)) $stmt_item->close();
    }
}

// Calculate total items in cart for the navbar icon
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
    <title>Checkout - PerfectPattern</title>
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
    .checkout-summary, .checkout-details {
        background-color: #a69f9f;
        padding: 25px;
        border-radius: 8px;
        color: #333;
        margin-bottom: 20px;
    }
    .checkout-summary h4, .checkout-summary p,
    .checkout-details h4, .checkout-details p {
        margin-bottom: 10px;
    }
    .checkout-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 10px;
    }
    .form-control {
        background-color: #f8f9fa; /* Light background for inputs */
        border-color: #ced4da;
        color: #495057;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }
    .form-label {
        color: #333; /* Dark text for labels */
        font-weight: bold;
    }
    </style>
</head>
<body>

<!-- Navbar (Same as other pages) -->
<nav class="navbar navbar-expand-lg custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">PerfectPattern</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
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
    <h2 class="mb-4 text-center">Checkout</h2>

    <?php if (isset($_SESSION['order_error'])): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?php echo $_SESSION['order_error']; ?>
            <?php unset($_SESSION['order_error']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="checkout-details">
                <h4>Order Details</h4>
                <hr>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="d-flex align-items-center mb-3 checkout-item">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <div class="flex-grow-1">
                            <p class="mb-0"><strong><?php echo $item['name']; ?></strong></p>
                            <small><?php echo $item['quantity']; ?> x ₹<?php echo number_format($item['price'], 2); ?></small>
                        </div>
                        <p class="mb-0 float-end">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
                <hr>
                <p>Subtotal: <span class="float-end">₹<?php echo number_format($subtotal, 2); ?></span></p>
                <p>Shipping: <span class="float-end">₹<?php echo number_format($shipping_cost, 2); ?></span></p>
                <h4>Total: <span class="float-end">₹<?php echo number_format($total_amount, 2); ?></span></h4>
            </div>
        </div>

        <div class="col-md-5">
            <div class="checkout-summary">
                <h4>Shipping Information</h4>
                <form method="post" action="checkout.php">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-success w-100">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 py-3 border-top">
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