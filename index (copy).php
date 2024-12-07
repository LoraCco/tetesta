<?php
require_once('config.php');

// Ξεκινάμε την session
session_start();

// Ελέγχουμε αν ο χρήστης έχει ήδη λύσει το captcha μέσα στην τρέχουσα συνεδρία
if (isset($_SESSION['captcha_verified']) && $_SESSION['captcha_verified'] == true) {
    // Εάν ο χρήστης είναι ήδη πιστοποιημένος, ανακατευθύνουμε στο store.php
    header('Location: store.php');
    exit;
}

// Δημιουργία τυχαίας ερώτησης αν δεν υπάρχει ήδη στην συνεδρία
if (!isset($_SESSION['question']) || !isset($_SESSION['question_time']) || time() - $_SESSION['question_time'] > 300) { // 300 δευτερόλεπτα = 5 λεπτά
    // Δημιουργία δύο τυχαίων αριθμών από το 1 έως το 10 για την ερώτηση
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    
    // Αποθήκευση της ερώτησης και της σωστής απάντησης στη session
    $_SESSION['question'] = $num1 . ' + ' . $num2;
    $_SESSION['answer'] = $num1 + $num2;  // Η σωστή απάντηση
    
    // Αποθήκευση του χρόνου που δημιουργήθηκε η ερώτηση για τον έλεγχο χρόνου
    $_SESSION['question_time'] = time();
}

// Λογική αν ο χρήστης υποβάλει απάντηση
$error_message = ''; // Μηνύματα λάθους

if (isset($_POST['answer'])) {
    // Ελέγχουμε αν η απάντηση είναι σωστή
    if ($_POST['answer'] == $_SESSION['answer']) {
        // Αν η απάντηση είναι σωστή, αποθηκεύουμε ότι το captcha λύθηκε και ανακατευθύνουμε στο store.php
        $_SESSION['captcha_verified'] = true;
        unset($_SESSION['question']); // Διαγράφουμε την ερώτηση από τη συνεδρία για την επόμενη επίσκεψη
        unset($_SESSION['answer']);   // Διαγράφουμε την απάντηση από τη συνεδρία για την επόμενη επίσκεψη
        header('Location: store.php');
        exit;
    } else {
        // Αν η απάντηση είναι λάθος, εμφανίζουμε το μήνυμα λάθους
        $error_message = "Incorrect answer. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $storeName; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<div class="verification">
    <h2>Verification Required</h2>
    <p>To access the website, please solve the following question:</p>

    <!-- Φόρμα με το ερώτημα -->
    <form method="post">
        <label for="answer">What is <?php echo $_SESSION['question']; ?>?</label><br>
        <input type="text" id="answer" name="answer" required><br><br>
        <input type="submit" value="Submit">
    </form>

    <?php
    // Αν η απάντηση είναι λανθασμένη, εμφάνιση μηνύματος
    if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
</div>

</body>
</html>

