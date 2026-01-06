<?php
session_start(); // Start the session
include 'db_connect.php'; // Include your database connection file
// open website : http://localhost/perfectpattern/index.php

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Buy Now action (This still works via direct form submission to checkout.php)
// It's kept here because "Buy Now" often means a direct redirect,
// but you could convert this to AJAX too if you want.
if (isset($_POST['buy_now'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    $_SESSION['cart'] = []; // Clear existing items for direct purchase
    $_SESSION['cart'][$product_id] = [
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image,
        'quantity' => 1
    ];
    header("Location: checkout.php"); // Redirect to checkout page
    exit();
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
    <title>PerfectPattern.com</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
     <style>
     body{
        background-color: #544646de;
        padding-top: 56px; /* Adjust this value if your navbar height changes */
     }
        header {
      padding: 15px 25px;
      text-align: center;
    }

    /* Custom Navbar Styling */
    .custom-navbar {
        background-color: #333; /* Dark background for visibility */
        border-bottom: 2px solid #a69f9f; /* Match card border */
        position: fixed; /* Keep navbar at the top */
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1030; /* Ensure it's above other content, Bootstrap's default is 1030 */
    }

    .custom-navbar .navbar-brand,
    .custom-navbar .nav-link {
        color: rgb(218, 211, 212); /* Lighter text for contrast */
        font-weight: bold;
    }

    .custom-navbar .nav-link:hover {
        color: #ddd; /* Lighter on hover */
        background-color: #555; /* Slight background change on hover */
        border-radius: 5px;
    }

    .carousel-item img {
    max-height: 15rem;
      object-fit: cover;
    }
    .brandname h1{
        font-style: italic;
        font-weight: bolder;
        color: rgb(218, 211, 212);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size:40px;
          }
    .brandname h6{
        /* font-style: italic; */
        color: #d1cace;
        font-style: oblique;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .cards .row{
        display: flex;
        flex-direction: row;
        justify-content: center;
        padding: 10px;
        margin: 20px;
        gap: 150px;
    }
    .card{
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px; /* Add some space between cards rows */
    }
    .card img{
        /* padding: 10px; */
        margin: 10px;
        border-radius: 25px;
        border: 2px solid rgb(168, 151, 151);
    }
    .cards .row .card img{
        transition: height 2s , width 2s;
      }

    .cards .row .card img:hover{
      width: 300px;
      height: 400px;
    }
    .cards .row .card{
      background-color: #a69f9f;
    }
    .cards h3{
      font-style: italic;
        font-weight: bolder;
        color: rgb(218, 211, 212);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    /* Styles for the notification toast */
    .toast-container {
        position: fixed;
        top: 70px; /* Below navbar */
        right: 20px;
        z-index: 1050; /* Above navbar and other content */
    }
     </style>
</head>
<body>

<!-- Navbar Added Here -->
<nav class="navbar navbar-expand-lg custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">PerfectPattern</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto"> <!-- ms-auto pushes items to the right -->
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#top">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#dresses-section">Dresses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#tops-section">Tops</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#bottomwear-section">Bottomwear</a>
        </li>
        <!-- Cart Icon -->
        <li class="nav-item">
            <a class="nav-link" href="cart.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <span class="badge bg-secondary rounded-pill" id="cart-item-count"><?php echo $cart_item_count; ?></span>
            </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Toast Container for notifications -->
<div class="toast-container">
  <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-message">
        <!-- Message will be inserted here -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>


<div class="header" style="grid-area: header;">
    <header id="top"> <!-- Added id="top" for the Home link -->
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./images/image.png" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./images/image2.png" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./images/image3.jpg" class="d-block w-100" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </header>
</div>
<div class="brandname">
    <div><h1>PerfectPattern</h1></div>
    <div><h6>Style that speaks for itself</h6></div>
</div>

<div class="cards">
    <hr>
    <h3 id="dresses-section">DRESSES</h3>
    <hr>

    <div class="row">
    <?php
    $sql_dresses = "SELECT * FROM products WHERE category = 'Dresses'";
    $result_dresses = $conn->query($sql_dresses);

    if ($result_dresses->num_rows > 0) {
        while($row = $result_dresses->fetch_assoc()) {
            echo '<div class="card" style="width: 18rem; background-color: #a69f9f;">';
            echo '  <img src="' . $row["image_url"] . '" class="card-img-top" alt="' . $row["name"] . '">';
            echo '  <div class="card-body">';
            echo '    <h5 class="card-title">' . $row["name"] . '</h5>';
            echo '    <h5>₹' . number_format($row["price"], 2) . '</h5>';
            echo '    <p class="card-text">' . $row["description"] . '</p>';
            // Form for Add to Cart (now AJAX) and Buy Now (direct submit)
            echo '    <form class="add-to-cart-form" method="post" action="index.php">'; // Add class for JS targeting
            echo '      <input type="hidden" name="product_id" value="' . $row["id"] . '">';
            echo '      <input type="hidden" name="product_name" value="' . $row["name"] . '">';
            echo '      <input type="hidden" name="product_price" value="' . $row["price"] . '">';
            echo '      <input type="hidden" name="product_image" value="' . $row["image_url"] . '">';
            echo '      <button type="submit" name="buy_now" class="btn btn-primary">Buy Now</button>'; // This still submits directly to index.php for now
            echo '      <button type="button" class="btn btn-secondary add-to-cart-btn">Add to Cart</button>'; // Changed to type="button"
            echo '    </form>';
            echo '  </div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-white text-center'>No dresses found.</p>";
    }
    ?>
    </div>

    <hr>
    <h3 id="tops-section">TOPS</h3>
    <hr>

    <div class="row">
    <?php
    $sql_tops = "SELECT * FROM products WHERE category = 'Tops'";
    $result_tops = $conn->query($sql_tops);

    if ($result_tops->num_rows > 0) {
        while($row = $result_tops->fetch_assoc()) {
            echo '<div class="card" style="width: 18rem; background-color: #a69f9f;">';
            echo '  <img src="' . $row["image_url"] . '" class="card-img-top" alt="' . $row["name"] . '">';
            echo '  <div class="card-body">';
            echo '    <h5 class="card-title">' . $row["name"] . '</h5>';
            echo '    <h5>₹' . number_format($row["price"], 2) . '</h5>';
            echo '    <p class="card-text">' . $row["description"] . '</p>';
            echo '    <form class="add-to-cart-form" method="post" action="index.php">'; // Add class for JS targeting
            echo '      <input type="hidden" name="product_id" value="' . $row["id"] . '">';
            echo '      <input type="hidden" name="product_name" value="' . $row["name"] . '">';
            echo '      <input type="hidden" name="product_price" value="' . $row["price"] . '">';
            echo '      <input type="hidden" name="product_image" value="' . $row["image_url"] . '">';
            echo '      <button type="submit" name="buy_now" class="btn btn-primary">Buy Now</button>';
            echo '      <button type="button" class="btn btn-secondary add-to-cart-btn">Add to Cart</button>';
            echo '    </form>';
            echo '  </div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-white text-center'>No tops found.</p>";
    }
    ?>
    </div>

    <hr>
    <h3 id="bottomwear-section">BOTTOMWEAR</h3>
    <hr>

    <div class="row">
    <?php
    $sql_bottomwear = "SELECT * FROM products WHERE category = 'Bottomwear'";
    $result_bottomwear = $conn->query($sql_bottomwear);

    if ($result_bottomwear->num_rows > 0) {
        while($row = $result_bottomwear->fetch_assoc()) {
            echo '<div class="card" style="width: 18rem; background-color: #a69f9f;">';
            echo '  <img src="' . $row["image_url"] . '" class="card-img-top" alt="' . $row["name"] . '">';
            echo '  <div class="card-body">';
            echo '    <h5 class="card-title">' . $row["name"] . '</h5>';
            echo '    <h5>₹' . number_format($row["price"], 2) . '</h5>';
            echo '    <p class="card-text">' . $row["description"] . '</p>';
            echo '    <form class="add-to-cart-form" method="post" action="index.php">'; // Add class for JS targeting
            echo '      <input type="hidden" name="product_id" value="' . $row["id"] . '">';
            echo '      <input type="hidden" name="product_name" value="' . $row["name"] . '">';
            echo '      <input type="hidden" name="product_price" value="' . $row["price"] . '">';
            echo '      <input type="hidden" name="product_image" value="' . $row["image_url"] . '">';
            echo '      <button type="submit" name="buy_now" class="btn btn-primary">Buy Now</button>';
            echo '      <button type="button" class="btn btn-secondary add-to-cart-btn">Add to Cart</button>';
            echo '    </form>';
            echo '  </div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-white text-center'>No bottomwear found.</p>";
    }
    ?>
    </div>

</div>
<div class="container mt-5 py-3 border-top" style="grid-area: footer;">
      <footer>
        <p class="text-center text-white ">© 2025 , Kirti Gupta</p>
      </footer>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all toasts
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
        });

        const cartItemCountSpan = document.getElementById('cart-item-count');
        const cartToast = document.getElementById('cartToast');
        const toastMessage = document.getElementById('toast-message');

        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                // event.preventDefault(); // Not strictly needed for type="button" but good practice for other scenarios

                const form = this.closest('.add-to-cart-form');
                const productId = form.querySelector('input[name="product_id"]').value;
                const productName = form.querySelector('input[name="product_name"]').value;
                const productPrice = form.querySelector('input[name="product_price"]').value;
                const productImage = form.querySelector('input[name="product_image"]').value;

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('product_name', productName);
                formData.append('product_price', productPrice);
                formData.append('product_image', productImage);

                fetch('add_to_cart_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItemCountSpan.textContent = data.cart_count;
                        toastMessage.textContent = data.message;
                        cartToast.classList.remove('bg-danger'); // Ensure it's green for success
                        cartToast.classList.add('bg-success');
                        bootstrap.Toast.getInstance(cartToast).show(); // Show the toast
                    } else {
                        // Handle error (e.g., show a different toast or alert)
                        toastMessage.textContent = data.message || 'Error adding item to cart.';
                        cartToast.classList.remove('bg-success'); // Ensure it's red for error
                        cartToast.classList.add('bg-danger');
                        bootstrap.Toast.getInstance(cartToast).show();
                        console.error('Error adding to cart:', data.message);
                    }
                })
                .catch(error => {
                    toastMessage.textContent = 'Network error. Could not add item to cart.';
                    cartToast.classList.remove('bg-success');
                    cartToast.classList.add('bg-danger');
                    bootstrap.Toast.getInstance(cartToast).show();
                    console.error('Fetch error:', error);
                });
            });
        });
    });
</script>
</body>
</html>
<?php
$conn->close(); // Close the database connection
?>