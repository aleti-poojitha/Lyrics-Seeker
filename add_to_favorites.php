<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lyricseeker";
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['song_id'])) {
    $song_id = $_GET['song_id'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $check_query = "SELECT * FROM user_favorites WHERE user_id = ? AND song_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $song_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        $delete_query = "DELETE FROM user_favorites WHERE user_id = ? AND song_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $user_id, $song_id); 
        if ($delete_stmt->execute()) {
            echo "Song removed from favorites.";
        } else {
            echo "Failed to remove song from favorites.";
        }
        $delete_stmt->close();
    } else {
        $insert_query = "INSERT INTO user_favorites (user_id, song_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $user_id, $song_id);
        
        if ($insert_stmt->execute()) {
            echo "Song added to favorites!";
        } else {
            echo "Failed to add song to favorites.";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
