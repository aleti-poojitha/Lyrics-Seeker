<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: url(./images/background.jpg) no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        header {
            width: 100%;
            background-color: #f8f8f8;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 1rem;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        header .logo {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }
        header .logo img {
            height: 40px;
            margin-right: 0.5rem;
        }
        header nav ul {
            list-style: none;
            display: flex;
            margin-right: 66px;
            padding: 0;
            justify-content: space-between;
        }
        header nav ul li {
            list-style: none;
            display: inline-block;
            margin: 0 20px;
            position: relative;
        }
        header nav ul li a {
            text-decoration: none;
            color: black;
            font-size: 19px;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.5);
            align-items: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
            width: 200px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="./images/logo.jpg" alt="Lyrical.com Logo">
            <b><span>LYRICS SEEKER</span></b>
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="search.html">Search</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <br/><br/><br/><br/><br/><br/><br/>
        <div class="container">
            <h1>Profile</h1>
            <div class="profile-info">
                <label>Username:</label>
                <span><?php echo $_SESSION["username"];?></span><br/><br/>
                <label>Email:</label>
                <span><?php echo isset($_SESSION["email"]) ? $_SESSION["email"] : 'N/A';?></span><br/><br/>
                <label>Joined:</label>
                <span><?php echo isset($_SESSION["joined"]) ? $_SESSION["joined"] : 'N/A';?></span><br/><br/>
            </div>
        </div>
    </main>
</body>
</html>
