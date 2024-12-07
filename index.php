<?php
require_once('config.php');

//session 
session_start();

//create empty array for cart
if (!isset($_SESSION['tedi'])) {
    $_SESSION['tedi'] = array();
}

//get current exchange rate
if (!isset($_SESSION['exr'])) {
    $url = "https://www.bitstamp.net/api/v2/ticker/BTCUSD";
    $json = json_decode(file_get_contents($url), true);
    if (array_key_exists("last", $json)) {
        $price = $json["last"];
        $_SESSION['exr'] = $price;
    } else {
        //try another source
        $url = "https://blockchain.info/stats?format=json";
        $json = json_decode(file_get_contents($url), true);
        if (array_key_exists("market_price_usd", $json)) {
            $price = $json["market_price_usd"];
            $_SESSION['exr'] = $price;
        } else {
            die("Oops please try refreshing or try again later");
        }
    }
}

//count items in array
$cartItems = count($_SESSION['tedi']);
$cart = $_SESSION['tedi'];

//add to cart buttons
$queryProducts2 = "SELECT * FROM products WHERE in_stock > 0 ORDER BY id ASC";
$resultH2 = mysqli_query($conn, $queryProducts2) or die("database connection error check server log");
while ($outputsH2 = mysqli_fetch_assoc($resultH2)) {
    if (isset($_POST[$outputsH2['id']])) {
        array_push($_SESSION['tedi'], $outputsH2['id']);
        $cartItems = count($_SESSION['tedi']);
        $cart = $_SESSION['tedi'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $storeName; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
<meta charset="utf-8">
<meta name="vieport" content="width=device-width, initial-scale=1"
</head>

<body>
<style>
.tablink {
    background-color: #2b77f2;
    color: white;
    position: static;
    border: groove;
    font-size: 17px;
}
.topleft {
display: grid;
justify-content: center;
    font-size: 18px;
}
</style>

<div id="miranHeader" class="tablink" style="display: grid;">
    <a href="" class="topleft">
        <img src="img/logo.png" alt="Logo" style="height:75px;">
    </a>
<div class="header-buttons">
        <button onclick="window.location.href='news.html'">NEWS</button>
        <button onclick="window.location.href='faq.html'">FAQ</button>
    </div>
</div>
<div class="green" data-v-0d5bcece="" style="padding: 0px 8px; white-space: pre-line; background-color: #4caf50;  "><b data-v-0d5bcece="">04.12.2024 Patrasbay:</b><div data-v-0d5bcece="">Signal: Patrasbay.01<font></font>
Activity Shop: Offline</div></div>
<div id="cartCont" style="margin-top:30px;">
    <div id="cartHeader">Your Cart</div>
    <div id="cartSpace">

    <?php
    $usdOwed = 0;
    for ($i = 0; $i < $cartItems; $i++) {
        $queryLoopCart = "SELECT * FROM products WHERE id = '$cart[$i]'";
        $doLoopCart = mysqli_query($conn, $queryLoopCart);
        $rowLoopCart = mysqli_fetch_assoc($doLoopCart);
        $loopName = $rowLoopCart['name'];
        $loopPrice = $rowLoopCart['price'];
        $usdOwed += $loopPrice;
        echo $loopName . "<span class='cartPrice'>$" . $loopPrice . "</span>";
        echo "<br><br>";
    }

    echo "</div>";
    echo "<div id='cartCost'>$" . $usdOwed . "</div>";
    ?>

    <form action="cart.php"><input type="submit" value="View Cart"></form>
</div>

<?php
$queryProducts ="SELECT * FROM products WHERE in_stock > 0 ORDER BY id ASC";
$resultH = mysqli_query($conn, $queryProducts) or die("error fetching products table");
while ($outputsH = mysqli_fetch_assoc($resultH)) {
    echo "<div class='shopCont'>";
    echo "<div class='shopImg'><img src='" . $outputsH['image'] . "'></div>";
    echo "<div class='shopDesc'>";
    echo "<span class='itemName'>" . $outputsH['name'] . "</span>";
    echo "<span class='itemCost'>$" . $outputsH['price'] . "</span>";
    echo $outputsH['description'] . "</div>";
    echo "<div class='shopDetails'><a href='product_details/index.php?id=" . $outputsH['id'] . "'>Details</a></div>";
    echo "</div>";
    echo "<div class='shopCont'><hr></div>";
}
?>
<br>
</body>
</html>
