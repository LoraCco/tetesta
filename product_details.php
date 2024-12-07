<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (!isset($_GET['id'])) {
    die("No product ID provided");
}

$productId = intval($_GET['id']);
$query = "SELECT * FROM products WHERE id = $productId";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching product details");
}

$product = mysqli_fetch_assoc($result);
if (!$product) {
    die("Product not found");
}

// Ανάκτηση παραλλαγών προϊόντος
$queryVariants = "SELECT * FROM product_variants WHERE product_id = $productId";
$resultVariants = mysqli_query($conn, $queryVariants);
$variants = mysqli_fetch_all($resultVariants, MYSQLI_ASSOC);

// Διαχείριση προσθήκης στο καλάθι
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount']) && isset($_POST['variant_id'])) {
    $amount = $_POST['amount'];
    $variantId = $_POST['variant_id'];
    $pricePerUnit = $_POST['price'];
    $totalPrice = $pricePerUnit * $amount;

    // Ανάκτηση πληροφοριών παραλλαγής
    $variantQuery = "SELECT * FROM product_variants WHERE id = $variantId";
    $variantResult = mysqli_query($conn, $variantQuery);
    $variant = mysqli_fetch_assoc($variantResult);

    // Δημιουργία αντικειμένου για το καλάθι
    $cartItem = [
        'id' => $productId,
        'name' => $product['name'],
        'variant' => $variant['variant_name'] . ': ' . $variant['variant_value'], // Όνομα παραλλαγής
        'amount' => $amount,
        'pricePerUnit' => $pricePerUnit,
        'totalPrice' => $totalPrice,
    ];

    // Έλεγχος αν υπάρχει το ίδιο προϊόν με την ίδια παραλλαγή
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId && $item['variant'] == $cartItem['variant']) {
            $item['totalPrice'] += $totalPrice; // Ενημέρωση ποσότητας
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $cartItem;
    }

    echo "<script>alert('Product added to cart!');</script>";
}

$cartCount = count($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product['name']); ?> Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }
        .product-header {
            display: flex;
            flex-wrap: wrap;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            padding: 1.5rem;
            border-radius: 10px;
        }
        .product-header img {
            max-width: 100%;
            border-radius: 10px;
        }
        .product-details {
            flex: 1;
            margin-left: 2rem;
        }
        .price-table {
            margin-top: 2rem;
        }
        .price-table table {
            width: 100%;
            background-color: #fff;
            border-collapse: collapse;
        }
        .price-table table thead {
            background-color: #00796b;
            color: #fff;
        }
        .price-table table th, .price-table table td {
            padding: 0.75rem;
            text-align: center;
            border: 1px solid #ddd;
        }
        .price-table table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-header">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-details">
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> €<?php echo number_format($product['price'], 2); ?></p>
            </div>
        </div>

        <!-- Πίνακας Τιμών -->
        <div class="price-table">
            <h5>Price Table:</h5>
            <table>
                <thead>
                    <tr>
                        <th>Variant</th>
                        <th>Price per Unit (€)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($variants as $variant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($variant['variant_name']) . ': ' . htmlspecialchars($variant['variant_value']); ?></td>
                            <td>€<?php echo number_format($variant['price'], 2); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="amount" value="1"> <!-- Μπορείς να προσθέσεις ένα πεδίο για ποσότητα -->
                                    <input type="hidden" name="price" value="<?php echo $variant['price']; ?>">
                                    <input type="hidden" name="variant_id" value="<?php echo $variant['id']; ?>"> <!-- ID παραλλαγής -->
                                    <button class="btn-small green" type="submit">Add</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Link για επιστροφή -->
        <div class="row">
            <div class="col s12">
                <a href="index.php" class="btn grey">Back to Store</a>
            </div>
        </div>
    </div>
</body>
</html>

