<?php
session_start();
// No database connection needed here unless you want to verify product existence,
// but for a simple add to cart, the data from the form is enough.

header('Content-Type: application/json'); // Tell the browser to expect JSON

$response = ['success' => false, 'message' => 'An unknown error occurred.', 'cart_count' => 0];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['product_id'], $_POST['product_name'], $_POST['product_price'], $_POST['product_image'])) {
    $product_id = (int)$_POST['product_id']; // Cast to int for safety
    $product_name = htmlspecialchars($_POST['product_name']);
    $product_price = (float)$_POST['product_price']; // Cast to float
    $product_image = htmlspecialchars($_POST['product_image']);

    // Basic validation
    if ($product_id > 0 && $product_price >= 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image,
                'quantity' => 1
            ];
        }

        // Recalculate cart item count
        $cart_item_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_item_count += $item['quantity'];
        }

        $response['success'] = true;
        $response['message'] = "Added '{$product_name}' to cart!";
        $response['cart_count'] = $cart_item_count;

    } else {
        $response['message'] = "Invalid product data.";
    }
} else {
    $response['message'] = "Missing product details for adding to cart.";
}

echo json_encode($response);
exit();
?>