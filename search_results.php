<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lyricseeker";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search_query = "";
$search_results = "";

// Check if search query is provided via GET method
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $search_query = $_GET['query'];

    // Prepare SQL query with placeholders to prevent SQL injection
    $sql = "SELECT songs.song_id, songs.song_title, songs.lyrics, artists.artist_name 
            FROM songs 
            INNER JOIN artists ON songs.artist_id = artists.artist_id
            WHERE songs.song_title LIKE ? 
            OR artists.artist_name LIKE ? 
            OR songs.lyrics LIKE ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute query
    $param = "%$search_query%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if results are found
    if ($result->num_rows > 0) {
        // Build HTML for search results
        $search_results .= buildSearchResultsHTML($result, $search_query);
    } else {
        // No results found message
        $search_results .= buildNoResultsHTML($search_query);
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();

function buildSearchResultsHTML($result, $search_query) {
    $html = "<!DOCTYPE html>
            <html lang=\"en\">
            <head>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                <title>Search Results</title>
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
                    .search-bar {
                        width: 50%;
                        margin: 140px auto 20px; /* Adjusted top margin to accommodate fixed header */
                        background-color: rgba(249, 249, 249, 0.696);
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 15px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    #search-input {
                        width: 70%;
                        padding: 10px;
                        font-size: 16px;
                        border: none;
                        border-radius: 10px;
                        background-color: transparent;
                        font-family: Georgia, 'Times New Roman', Times, serif;
                    }
                    #search-button {
                        width: 28%;
                        padding: 10px;
                        font-size: 16px;
                        background-color: #4CAF50;
                        color: #fff;
                        border: none;
                        border-radius: 10px;
                        cursor: pointer;
                        font-family: Georgia, 'Times New Roman', Times, serif;
                    }
                    #search-button:hover {
                        background-color: #3e8e41;
                    }
                    .container {
                        width: 600px;
                        margin: 20px auto;
                        text-align: center;
                        border: none;
                        border-radius: 20px;
                        background-color: rgba(255, 255, 255, 0.8);
                    }
                    .section {
                        margin-bottom: 20px;
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 20px;
                        background-color: transparent;
                    }
                    .song-title {
                        cursor: pointer;
                        font-size: 18px;
                        font-weight: bold;
                        text-align: center;
                        margin-top: -5px;
                        margin-bottom: 0px;
                    }
                   .lyrics {
                        font-size: 16px;
                        line-height: 1.6;
                        text-align: left; /* Adjust alignment of lyrics */
                    }
                   .artist-name {
                        font-size: 14px;
                        font-style: italic;
                        text-align: right;
                        margin-top: 2px;
                    }
                   .favorites-placeholder {
                        margin-bottom: 5px;   
                    }
                   .favorite-icon {
                        width: 15px;
                        height: auto;
                        cursor: pointer;
                        margin-right: 500px;
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
                            <li><a href=\"index.html\">Home</a></li>
                            <li><a href=\"search.html\">Search</a></li>
                            <li><a href=\"login.html\">Profile</a></li>
                            <li><a href=\"#\">Logout</a></li>
                        </ul>
                    </nav>
                </header>
                <form action=\"search_results.php\" method=\"GET\" class=\"search-bar\">
                    <input type=\"search\" id=\"search-input\" name=\"query\" placeholder=\"Search for song lyrics...\" required value=\"$search_query\"><br/>
                    <button type=\"submit\" id=\"search-button\">Search</button>
                </form>";

    if ($result->num_rows > 0) {
        $html.= "<div class=\"container\">";
        while ($row = $result->fetch_assoc()) {
            $song_id = $row['song_id'];
            $song_title = htmlspecialchars($row['song_title']);
            $lyrics_preview = nl2br(htmlspecialchars($row['lyrics']));
            $artist_name = htmlspecialchars($row['artist_name']);

            $html.= "<div class=\"section\">";
            $html.= "<img src=\"./images/favorite.jpg\" alt=\"Add to Favorites\" onclick=\"addToFavorites($song_id)\" class=\"favorite-icon\">";
            $html.= "<div class=\"song-details\">";
            $html.= "<div class=\"song-title\" onclick=\"showLyrics($song_id)\">$song_title</div>";
            $html.= "<div class=\"artist-name\">~by $artist_name</div>";
            $html.= "</div>";
            $html.= "<div class=\"lyrics\" id=\"lyrics_$song_id\" style=\"display: none;\">$lyrics_preview</div>";
            $html.= "</div>";
        }
        $html.= "</div>";
    } else {
        $html.= buildNoResultsHTML($search_query);
    }
    $html.= "<script>
                function showLyrics(songId) {
                    var lyricsDiv = document.getElementById('lyrics_' + songId);
                    var allLyrics = document.querySelectorAll('.lyrics');
                    
                    // Hide all lyrics sections
                    allLyrics.forEach(function(lyrics) {
                        lyrics.style.display = 'none';
                    });
                    
                    // Show the clicked lyrics section
                    if (lyricsDiv.style.display === 'none') {
                        lyricsDiv.style.display = 'block';
                    } else {
                        lyricsDiv.style.display = 'none';
                    }
                }  
                function addToFavorites(songId) {
                    fetch('add_to_favorites.php?song_id=' + songId)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to add to favorites');
                        }
                        alert('Song added to favorites!');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to add to favorites');
                    });
                }
            </script>";
    $html.= "</body></html>";
    return $html;
}

function buildNoResultsHTML($search_query) {
    $html = "<!DOCTYPE html>
            <html lang=\"en\">
            <head>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                <title>No Results Found</title>
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
                   .search-bar {
                        width: 50%;
                        margin: 140px auto 20px; /* Adjusted top margin to accommodate fixed header */
                        background-color: rgba(249, 249, 249, 0.696);
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 15px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    #search-input {
                        width: 70%;
                        padding: 10px;
                        font-size: 16px;
                        border: none;
                        border-radius: 10px;
                        background-color: transparent;
                        font-family: Georgia, 'Times New Roman', Times, serif;
                    }
                    #search-button {
                        width: 28%;
                        padding: 10px;
                        font-size: 16px;
                        background-color: #4CAF50;
                        color: #fff;
                        border: none;
                        border-radius: 10px;
                        cursor: pointer;
                        font-family: Georgia, 'Times New Roman', Times, serif;
                    }
                    #search-button:hover {
                        background-color: #3e8e41;
                    }
                   .suggestions {
                        margin-top: 20px;
                        padding: 10px;
                        background-color: rgba(249, 249, 249, 0.696);
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                   .error-message {
                        color: #ff0000;
                        margin-top: 10px;
                        text-align: center;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 50px;
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
                            <li><a href=\"index.html\">Home</a></li>
                            <li><a href=\"search.html\">Search</a></li>
                            <li><a href=\"login.html\">Profile</a></li>
                            <li><a href=\"#\">Logout</a></li>
                        </ul>
                    </nav>
                </header>
                <form action=\"search_results.php\" method=\"GET\" class=\"search-bar\">
                    <input type=\"search\" id=\"search-input\" name=\"query\" placeholder=\"Search for song lyrics...\" required value=\"$search_query\"><br/>
                    <button type=\"submit\" id=\"search-button\">Search</button>
                </form>
                <div class=\"error-message\" id=\"error-message\">
                    <p>No results found for \"$search_query\". Please try a different search term.</p>
                </div>
            </body>
            </html>";

    return $html;
}

// Output search results or no results message
echo $search_results;
?>