<?php
session_start();
// No DB connection needed for this simple page, as cart is cleared and no new data is fetched.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - PerfectPattern</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <style>
    body{
        background-color: #544646de;
        padding-top: 56px;
        color: rgb(218, 211, 212);
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Make body at least viewport height */
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
    .confirmation-card {
        background-color: #a69f9f;
        padding: 30px;
        border-radius: 8px;
        color: #333;
        text-align: center;
        margin-top: 50px;
        flex-grow: 1; /* Allow the card to take available vertical space */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    footer {
        margin-top: auto; /* Push footer to the bottom */
        width: 100%;
    }
    </style>
</head>
<body>

<!-- Navbar (Same as other pages, cart count will be 0 here) -->
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
                <span class="badge bg-secondary rounded-pill">0</span> <!-- Cart will be 0 after successful order -->
            </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <div class="confirmation-card col-md-8 col-lg-6">
        <h2 class="mb-3 text-success">Order Placed Successfully!</h2>
        <?php if (isset($_SESSION['order_success'])): ?>
            <p class="lead"><?php echo $_SESSION['order_success']; ?></p>
            <?php unset($_SESSION['order_success']); // Clear the message after displaying ?>
        <?php else: ?>
            <p class="lead">Thank you for your purchase! Your order has been received.</p>
        <?php endif; ?>
        <p>You can continue browsing our amazing collection:</p>
        <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
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