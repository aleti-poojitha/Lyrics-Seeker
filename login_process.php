<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lyricseeker";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $id;
            $_SESSION["email"] = $email;
            $_SESSION["joined"] = $joined_date;
            $stmt->close();
            $conn->close();
            header("Location: index.html");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: login.html?error=Incorrect password!");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        header("Location: login.html?error=User not found! Please register.");
        exit();
    }
}
?>
