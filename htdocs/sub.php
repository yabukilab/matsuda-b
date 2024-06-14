<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム評価詳細ページ</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ゲーム評価詳細ページ</h1>
        </header>
        <main>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "game";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $game_id = $_GET['id'];

                // Use prepared statement to prevent SQL injection
                $stmt = $conn->prepare("SELECT title, image_url, valution FROM games WHERE id = ?");
                $stmt->bind_param("i", $game_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="game-detail">';
                        echo '<div class="circle"><img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["title"]) . '"></div>';
                        echo '<div class="detail-title">' . htmlspecialchars($row["title"]) . '</div>';
                        echo '<div class="star">';
                        for ($i = 0; $i < 5; $i++) {
                            echo $i < $row["valution"] ? '★' : '☆';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "No results found.";
                }
                $stmt->close();
            } else {
                echo "Invalid game ID.";
            }

            $conn->close();
            ?>

            <div class="boards">
                <div class="board">
                    <div class="board-title">項目</div>
                    <div class="board-content">掲示板</div>
                </div>
                <div class="board">
                    <div class="board-title">項目</div>
                    <div class="board-content">掲示板</div>
                </div>
                <div class="board">
                    <div class="board-title">項目</div>
                    <div class="board-content">掲示板</div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
