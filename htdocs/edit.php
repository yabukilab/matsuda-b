<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム評価システム（編集ページ）</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
<div class="container">
<header>
    <div class="search-container">
        <input type="text" placeholder="検索..." class="search-box">
        <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
    </div>
    <h1>ゲーム評価システム（編集ページ）</h1>
</header>
<main>
<?php
// db.phpを読み込む
include 'db.php';

try {
    $sql = 'SELECT * FROM games';                 # SQL文
    $prepare = $db->prepare($sql);                  # 準備
    $prepare->execute();                            # 実行
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC); # 結果の取得

    foreach ($result as $row) {
        $gameID = h($row['GameID']);
        $title = h($row['Title']);
        $link = h($row['Link']);

        // ゲームの評価を取得
        $sql = "SELECT AVG(Rating) as avg_rating FROM valuation WHERE GameID = :gameID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
        $stmt->execute();
        $rating = $stmt->fetch(PDO::FETCH_ASSOC);
        $avgRating = round($rating['avg_rating'], 1);

        echo "<div class=\"game\">";
        echo "<div class=\"circle\"><i class=\"fas fa-gamepad\"></i></div>";

        // 星の評価を表示
        echo "<div class=\"star\">";
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
        echo "<span>{$avgRating}</span>";
        echo "</div>";

        echo "<div class=\"title\"><a href=\"{$link}\">{$title}</a></div>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "An error occurred: " . h($e->getMessage());
}
?>
</main>
<button onclick="location.href='index.php'" class="save-button">保存</button>
<button onclick="location.href='add.php'" class="add-button">データベースを追加する</button>
<button onclick="location.href='editdb.php'" class="edit-button">修正</button>
<button onclick="location.href='delete.php'" class="delete-button">削除</button>
</div>

</body>
</html>
