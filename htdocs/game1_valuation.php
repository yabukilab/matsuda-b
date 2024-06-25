<?php
include 'db.php';

$gameID = 1; // ゲームIDを指定

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
        header("Location: game1.php"); // ここで別のページにリダイレクト
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            border-top: 10px solid #0078d7;
            box-sizing: border-box;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        header {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #ccc;
            background-color: #f5f5f5;
        }
        .circle {
            width: 100px;
            height: 100px;
            background-color: #4a90e2;
            border-radius: 50%;
            margin-right: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .circle i {
            font-size: 3em;
            color: white;
        }
        header h1 {
            font-size: 2em;
            margin: 0 20px;
            flex-grow: 1;
        }
        .stars {
            display: flex;
            align-items: center;
            color: #4a90e2;
        }
        .stars i {
            font-size: 2em;
            margin: 0 5px;
            cursor: pointer;
        }
        .stars .selected {
            color: gold;
        }
        .pen-icon {
            margin-left: 20px;
            cursor: pointer;
        }
        .pen-icon i {
            font-size: 1.5em;
            color: #4a90e2;
        }
        main {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 0 20px;
        }
        .item {
            width: 30%;
            background-color: #4a90e2;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-bottom: 20px;
            position: relative;
        }
        .item h3,
        .item p {
            cursor: pointer;
        }
        .item h3 {
            margin: 10px 0;
        }
        .item p {
            margin: 10px 0 0;
            padding: 10px 0;
            background-color: #316bbf;
            border-radius: 5px;
        }
        .delete-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #fff;
            font-size: 1.2em;
        }
        .theme-switch {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            color: #4a90e2;
            z-index: 1000;
        }
        /* フォームスタイル */
        .rating-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: auto;
        }
        .rating-form .stars i {
            font-size: 2em;
            margin: 0 5px;
            cursor: pointer;
        }
        .rating-form .stars i:hover,
        .rating-form .stars i.selected {
            color: gold;
        }
        .rating-form input[type="submit"] {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #0078d7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .rating-form input[type="submit"]:hover {
            background-color: #005bb5;
        }
    </style>
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
