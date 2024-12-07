<?php

// Στοιχεία σύνδεσης με τη βάση δεδομένων
$dbuser = "root"; // αντί για "root" αν δεν χρησιμοποιείς root
$dbpw = "mele1821"; // αφήστε κενό αν δεν υπάρχει password
$db = "bitcoin_store";

// Στοιχεία καταστήματος
$storeName = "Patrasbay";
$rootURL = "http://yourrooturl.com/directory"; // παράδειγμα https://mysite.org ή http://yourhomepage.com/store
$yourEmail = "syed.aqeel@blockchainexpertsolutions.com"; // το email στο οποίο θα αποστέλλονται οι ειδοποιήσεις

// Σύνδεση στη βάση δεδομένων
$conn = mysqli_connect("localhost", $dbuser, $dbpw, $db);
if(!$conn){
  die('Connection error check server log');
}

// Αλγόριθμος κρυπτογράφησης (AES-256) και το μυστικό κλειδί (πρέπει να το κρατήσεις μυστικό)
define('ENCRYPTION_KEY', 'to-very-secret-key-1234567890abcdef'); // Μην το μοιράζεσαι με άλλους

// Συνάρτηση κρυπτογράφησης
function encryptData($data) {
    $iv = openssl_random_pseudo_bytes(16); // Δημιουργεί έναν τυχαίο αριθμό για το "αρχικό vector"
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv); // Κρυπτογραφεί τα δεδομένα
    $encryptedData = base64_encode($iv . $encryptedData); // Συνδυάζει το iv και το κρυπτογραφημένο κείμενο και τα κωδικοποιεί σε base64
    return $encryptedData; // Επιστρέφει το κρυπτογραφημένο κείμενο
}

// Συνάρτηση αποκρυπτογράφησης
function decryptData($data) {
    $data = base64_decode($data); // Αποκωδικοποιεί το base64
    $iv = substr($data, 0, 16); // Παίρνει το iv που βρίσκεται στην αρχή
    $data = substr($data, 16); // Παίρνει τα πραγματικά κρυπτογραφημένα δεδομένα
    return openssl_decrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv); // Αποκρυπτογραφεί τα δεδομένα
}

?>

