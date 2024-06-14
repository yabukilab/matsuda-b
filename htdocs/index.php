<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム評価システム</title>
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
            <h1>ゲーム評価システム</h1>
</header>
<main>
<?php
// db.phpを読み込む
require 'db.php';                               # 接続
try {
$sql = 'SELECT * FROM games';                 # SQL文
$prepare = $db->prepare($sql);                  # 準備
$prepare->execute();                            # 実行
$result = $prepare->fetchAll(PDO::FETCH_ASSOC); # 結果の取得

foreach ($result as $row) {
  $id       = h($row['GameID']);
  $title    = h($row['Title']);
  $Rating     = h($row['Rating']);// ここに星の評価が入るように修正する
  $link      =h($row['Link']);
  // 星の評価を計算するロジックを追加する
  echo "<div class=\"game\">";
  echo "<div class=\"circle\"><i class=\"fas fa-gamepad\"></i></div>";
  echo "<div class=\"star\">{$Rating}</div>";
  echo "<div class=\"title\"><a href=\"{$link}\">{$title}</a></div>";
  echo "</div>";
}
} catch (PDOException $e) {
echo "An error occurred: " . h($e->getMessage());
}
?>
</main>
</div>

</body>
</html>
