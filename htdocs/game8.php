<?php
include 'db.php';

$gameID = 8; // ゲームIDを指定

// ゲームの詳細を取得
$sql = "SELECT * FROM games WHERE GameID = :gameID";
$stmt = $db->prepare($sql);
$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
$stmt->execute();
$game = $stmt->fetch(PDO::FETCH_ASSOC);

// ゲームの評価を取得
$sql = "SELECT AVG(Rating) as avg_rating FROM valuation WHERE GameID = :gameID";
$stmt = $db->prepare($sql);
$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
$stmt->execute();
$rating = $stmt->fetch(PDO::FETCH_ASSOC);
$avgRating = round($rating['avg_rating'], 1);

//評価を送信
if(isset($_POST['submit'])) {
    $rating = $_POST['rating'];
    $gameID = $_POST['gameID'];

    $sql = "INSERT INTO valuation (GameID, Rating) VALUES (:gameID, :rating)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "評価が送信されました";
    } else {
        echo "エラー: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>サブページ</title>
    <link rel="stylesheet" href="styles_sub.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
</head>
<body>
    <div class="container">
        <header>
            <div class="circle">
            <i class="fas fa-gamepad"></i>
            </div>
            <h1><?php echo h($game['Title']); ?></h1>
            <div class="stars">
                <?php
                $fullStars = floor($avgRating);
                $halfStar = ($avgRating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fas fa-star"></i>';
                }
                if ($halfStar) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="far fa-star"></i>';
                }
                ?>
                <span><?php echo h($avgRating); ?></span>
            </div>
            <button onclick="location.href='./game8_valuation.php'">
            <div class="pen-icon" id="edit-icon">
                <i class="fas fa-pen"></i>
            </div>
            </button>
            
        </header>
        <main>
            <div class="item">
                <h3>項目</h3>
                <p>表示板</p>
            </div>
            <div class="item">
                <h3>項目</h3>
                <p>表示板</p>
            </div>
            <div class="item">
                <h3>項目</h3>
                <p>表示板</p>
            </div>
        </main>
    </div>
</body>
</html>