<?php
session_start();

if (isset($_SESSION['register_error'])) {
    echo $_SESSION['register_error'];
    unset($_SESSION['register_error']); // Clear the session variable after displaying
} else {
    echo ''; // Return empty string if no error message is set
}
?>
