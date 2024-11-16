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
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT songs.song_id, songs.song_title, songs.lyrics, artists.artist_name 
        FROM user_favorites 
        JOIN songs ON user_favorites.song_id = songs.song_id
        JOIN artists ON songs.artist_id = artists.artist_id
        WHERE user_favorites.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>My Favorites</title>
    <link rel=\"stylesheet\" href=\"style.css\">
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: url(./images/search-bg.jpg) no-repeat center center fixed;
            background-size: cover;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        header nav ul {
            list-style: none;
            display: flex;
            margin-right: 67px;
            padding: 0;
            justify-content: space-between;
        }
        .favorites-container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.6);
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .song-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: rgba(249, 249, 249, 0.6);
        }
        .song-title {
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        .artist-name {
            font-style: italic;
            margin-top: 5px;
        }
        .lyrics-preview {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <div class=\"logo\">
            <img src=\"./images/logo.jpg\" alt=\"Lyrical.com Logo\">
            <b><span>LYRICS SEEKER</span></b>
        </div>
        <nav>
            <ul>
                <li><a href=\"home.html\">Home</a></li>
                <li><a href=\"search.html\">Search</a></li>
                <li><a href=\"login.html\">Profile</a></li>
                <li><a href=\"#\">Logout</a></li>
            </ul>
        </nav>
    </header>
    <br/><br/><br/><br/><br/><br/>";
    echo "<div class=\"favorites-container\">";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $song_title = htmlspecialchars($row['song_title']);
            $artist_name = htmlspecialchars($row['artist_name']);
            $lyrics_preview = substr(htmlspecialchars($row['lyrics']), 0, 200) . "...";
            echo "<div class=\"song-item\">";
            echo "<div class=\"song-title\">$song_title</div>";
            echo "<div class=\"artist-name\">~by $artist_name</div>";
            echo "<div class=\"lyrics-preview\">$lyrics_preview</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No favorite songs found.</p>";
    }
    echo "</div>";
    echo "</body>
    </html>";
    $stmt->close();
    $conn->close();
?>
