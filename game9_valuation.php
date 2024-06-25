<?php
include 'db.php';

$gameID = 9; // ゲームIDを指定

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
if (isset($_POST['submit'])) {
    $rating = $_POST['rating'];
    $gameID = $_POST['gameID'];

    $sql = "INSERT INTO valuation (GameID, Rating) VALUES (:gameID, :rating)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('評価が送信されました');</script>";
        header("Location: game9.php"); // ここで別のページにリダイレクト
        exit(); // header関数の後にexitを呼び出す
    } else {
        echo "<script>alert('エラー: " . $stmt->errorInfo()[2] . "');</script>";
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
            <h1><?php echo htmlspecialchars($game['Title']); ?></h1>
            <form class="rating-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="stars">
                    <input type="radio" id="rating1" name="rating" value="1" required style="display:none;">
                    <label for="rating1"><i class="fas fa-star" data-value="1"></i></label>
                    <input type="radio" id="rating2" name="rating" value="2" style="display:none;">
                    <label for="rating2"><i class="fas fa-star" data-value="2"></i></label>
                    <input type="radio" id="rating3" name="rating" value="3" style="display:none;">
                    <label for="rating3"><i class="fas fa-star" data-value="3"></i></label>
                    <input type="radio" id="rating4" name="rating" value="4" style="display:none;">
                    <label for="rating4"><i class="fas fa-star" data-value="4"></i></label>
                    <input type="radio" id="rating5" name="rating" value="5" style="display:none;">
                    <label for="rating5"><i class="fas fa-star" data-value="5"></i></label>
                </div>
                <input type="hidden" name="gameID" value="<?php echo $gameID; ?>">
                
                <input type="submit" name="submit" value="評価する">
                
            </form>
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

    <script>
        document.querySelectorAll('.stars i').forEach(star => {
            star.addEventListener('click', function() {
                let rating = this.getAttribute('data-value');
                document.getElementById('rating' + rating).checked = true;
                document.querySelectorAll('.stars i').forEach(s => {
                    s.classList.remove('selected');
                    if (s.getAttribute('data-value') <= rating) {
                        s.classList.add('selected');
                    }
                });
            });
        });
    </script>
</body>
</html>
