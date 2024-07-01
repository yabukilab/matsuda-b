<?php
include 'db.php';

$gameID = 3; // ゲームIDを指定

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

// ウィキ内容を取得
$sql = "SELECT * FROM wiki_content WHERE GameID = :gameID";
$stmt = $db->prepare($sql);
$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
$stmt->execute();
$wiki_contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ウィキ内容を更新
if (isset($_POST['submit_wiki'])) {
    $content = $_POST['content'];
    $section = $_POST['section'];

    $sql = "SELECT ID FROM wiki_content WHERE GameID = :gameID AND Section = :section";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $stmt->bindParam(':section', $section, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        // レコードが存在する場合は更新
        $sql = "UPDATE wiki_content SET Content = :content WHERE GameID = :gameID AND Section = :section";
    } else {
        // レコードが存在しない場合は挿入
        $sql = "INSERT INTO wiki_content (GameID, Section, Content) VALUES (:gameID, :section, :content)";
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
    $stmt->bindParam(':section', $section, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->execute();
}

// コメントを取得
$sql = "SELECT c.*, w.Section FROM comments c JOIN wiki_content w ON c.WikiContentID = w.ID WHERE c.GameID = :gameID ORDER BY c.CreatedAt DESC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<nav class="nav-bar">
            <a href="game<?php echo $gameID; ?>.php" class="nav-button">戻る</a>
            <a href="index.php" class="nav-button">ゲーム一覧</a>
    </nav>
    <div class="container">
        <header>
            <div class="title-section">
                <div class="circle">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h1><?php echo htmlspecialchars($game['Title']); ?></h1>
            </div>
            <div class="rating-section">
                <div class="starsh">
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
                    <span><?php echo htmlspecialchars($avgRating); ?></span>
                </div>
                <button onclick="location.href='./game<?php echo $gameID; ?>_valuation.php'">
                    <div class="pen-icon" id="edit-icon">
                        <i class="fas fa-pen"></i>
                    </div>
                </button>
            </div>
        </header>
        <main>
            <div class="content-wrapper">
                <?php foreach ($wiki_contents as $content): ?>
                <div class="item-comment-pair">
                    <div class="item">
                        <h3><?php echo htmlspecialchars($content['Section']); ?></h3>
                        <p id="content-<?php echo $content['ID']; ?>"><?php echo nl2br(htmlspecialchars($content['Content'])); ?></p>
                        
                        <form id="edit-form-<?php echo $content['ID']; ?>" style="display:none;" method="post">
                            <textarea name="content"><?php echo htmlspecialchars($content['Content']); ?></textarea>
                            <input type="hidden" name="section" value="<?php echo htmlspecialchars($content['Section']); ?>">
                            <input type="submit" name="submit_wiki" value="保存">
                        </form>
                    </div>
                    <div class="comments">
                        <h4>
                            コメント: <?php echo htmlspecialchars($content['Section']); ?>
                            <button class="toggle-comments">▼</button>
                        </h4>
                        <div class="comments-content collapsed">
                            <?php foreach ($comments as $comment): ?>
                                <?php if ($comment['WikiContentID'] == $content['ID']): ?>
                                <div class="comment">
                                    <p><?php echo nl2br(htmlspecialchars($comment['Comment'])); ?></p>
                                    <small><?php echo $comment['CreatedAt']; ?></small>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-comments');
        
        toggleButtons.forEach(button => {
            button.textContent = '▼';
            
            button.addEventListener('click', function() {
                const commentsContent = this.parentElement.nextElementSibling;
                commentsContent.classList.toggle('collapsed');
                this.textContent = commentsContent.classList.contains('collapsed') ? '▼' : '▲';
            });
        });
    });
    </script>
</body>
</html>