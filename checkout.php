<?php
require_once('config.php');

//session 
session_start();

//count items in array
$cartItems = isset($_SESSION['tedi']) ? count($_SESSION['tedi']) : 0;
$cart = isset($_SESSION['tedi']) ? $_SESSION['tedi'] : [];

//redirect if self navigating pages
if ($cartItems < 1) {
   header("Location: cart.php");
   exit();
}

if (isset($_POST['submit'])) {
    $_SESSION['email'] = mysqli_real_escape_string($conn, $_POST['email']);
    $_SESSION['name'] = trim($_POST['name']);
    // Preserve the PGP message exactly as submitted
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['address2'] = trim($_POST['address2']);
    $_SESSION['city'] = trim($_POST['city']);
    $_SESSION['state'] = trim($_POST['state']);
    $_SESSION['zip'] = trim($_POST['zip']);
    $_SESSION['country'] = trim($_POST['country']);

    if (
        empty($_SESSION['email']) || 
        empty($_SESSION['name']) || 
        empty($_SESSION['address']) || 
        empty($_SESSION['city']) || 
        empty($_SESSION['zip'])
    ) {
        $message = "<span class='errMsg'>Form is incomplete!</span>";
    } else {
        header('Location: confirm.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<link rel="stylesheet" type="text/css" href="style.css">
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

<div id="miranHeader" class="tablink">
 <a href="http://miranz.net/" class="topleft">
 <img src="img/logo.png" alt="Logo" style="height:75px;">
 </a>
</div>

<div id="viewCart">
  <span id="viewTitle">Shipping Information</span><br>
  <form method="post">
  EMAIL<br>
  <input type="email" class="text" name="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>"><br>
  NAME<br>
  <input type="text" class="text" name="name" value="<?php if (isset($_SESSION['name'])) { echo $_SESSION['name']; } ?>"><br>
 Encrypted PGP Message<br>
<textarea class="text" name="address" rows="6" style="width: 100%;"><?php 
if (isset($_SESSION['address'])) { 
    echo htmlspecialchars($_SESSION['address']); // Ασφαλής εμφάνιση δεδομένων
} 
?></textarea><br>
<button type="button" class="pgp-button" onclick="window.open('pgpkey.txt');">VENDOR PGP PUBLIC</button><br>
  ADDRESS 2<br>
  <input type="text" class="text" name="address2" value="<?php if (isset($_SESSION['address2'])) { echo $_SESSION['address2']; } ?>"><br>
  CITY<br>
  <input type="text" class="text" name="city" value="<?php if (isset($_SESSION['city'])) { echo $_SESSION['city']; } ?>"><br>
  STATE/PROVINCE/REGION<br>
  <input type="text" class="text" name="state" value="<?php if (isset($_SESSION['state'])) { echo $_SESSION['state']; } ?>"><br>
  ZIP/POSTAL CODE<br>
  <input type="text" class="text" name="zip" value="<?php if (isset($_SESSION['zip'])) { echo $_SESSION['zip']; } ?>"><br>
  
  <div id="checkCont"><input type="submit" class="button" value="SUBMIT" name="submit"></form></div>
  <a href="cart.php">Go Back</a><br>
  <?php if (isset($message)) { echo $message; } ?>
</div>
<br>
</body>
</html>

