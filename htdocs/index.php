<?php
session_start();

function h($var) {
    if (is_array($var)) {
        return array_map('h', $var);
    } else {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}

// データベース接続設定
$dbServer = isset($_ENV['MYSQL_SERVER'])    ? $_ENV['MYSQL_SERVER']      : '127.0.0.1';
$dbUser   = isset($_SERVER['MYSQL_USER'])   ? $_SERVER['MYSQL_USER']     : 'testuser';
$dbPass   = isset($_SERVER['MYSQL_PASSWORD']) ? $_SERVER['MYSQL_PASSWORD'] : 'pass';
$dbName   = isset($_SERVER['MYSQL_DB'])     ? $_SERVER['MYSQL_DB']       : 'mydb';

$dsn = "mysql:host={$dbServer};dbname={$dbName};charset=utf8";

try {
    $db = new PDO($dsn, $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続エラー: " . h($e->getMessage()));
}

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $loginId = $_POST['loginId'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$loginId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === $password) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $loginError = "ログイン番号またはパスワードが間違っています";
    }
}

// ログアウト処理
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// メニュー一覧を取得
function getMenus($db) {
    $stmt = $db->query("SELECT * FROM menu");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 注文処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (empty($_SESSION['user'])) {
        die("ログインが必要です");
    }

    $userId = $_SESSION['user']['id'];
    $cart = json_decode($_POST['cart'], true);

    // カートが空の場合はエラー
    if (empty($cart)) {
        die("カートが空です");
    }

    // トランザクション開始
    $db->beginTransaction();

    try {
        // 注文番号生成（シンプルな例: 現在の日時とランダムな数値）
        $callNumber = 'A'.str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        // 合計金額計算
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // ordersテーブルに挿入
        $stmt = $db->prepare("INSERT INTO orders (call_number, user_id, total_price, status) VALUES (?, ?, ?, '調理中')");
        $stmt->execute([$callNumber, $userId, $totalPrice]);
        $orderId = $db->lastInsertId();

        // order_detailsテーブルに挿入
        foreach ($cart as $item) {
            $stmt = $db->prepare("INSERT INTO order_details (order_id, menu_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['quantity']]);
        }

        $db->commit();

        // 注文完了画面で表示する情報をセッションに保存
        $_SESSION['last_order'] = [
            'call_number' => $callNumber,
            'items' => $cart,
            'totalPrice' => $totalPrice
        ];

        header("Location: ".$_SERVER['PHP_SELF'].'?screen=orderComplete');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        die("注文処理中にエラーが発生しました: " . h($e->getMessage()));
    }
}

// メニュー管理（追加）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("管理者権限が必要です");
    }

    $name = $_POST['name'];
    $price = (int)$_POST['price'];

    $stmt = $db->prepare("INSERT INTO menu (name, price) VALUES (?, ?)");
    $stmt->execute([$name, $price]);

    header("Location: ".$_SERVER['PHP_SELF'].'?screen=menuManagement');
    exit;
}

// メニュー管理（編集）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_menu'])) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("管理者権限が必要です");
    }

    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $price = (int)$_POST['price'];

    $stmt = $db->prepare("UPDATE menu SET name = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $price, $id]);

    header("Location: ".$_SERVER['PHP_SELF'].'?screen=menuManagement');
    exit;
}

// メニュー管理（削除）
if (isset($_GET['delete_menu'])) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("管理者権限が必要です");
    }

    $id = (int)$_GET['delete_menu'];

    $stmt = $db->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ".$_SERVER['PHP_SELF'].'?screen=menuManagement');
    exit;
}

// 注文状態の更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("管理者権限が必要です");
    }

    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);

    header("Location: ".$_SERVER['PHP_SELF'].'?screen=admin');
    exit;
}

// 注文履歴の取得（管理者用）
function getAdminOrderHistory($db) {
    $stmt = $db->query("
        SELECT o.id, o.call_number, o.total_price, o.order_time, o.status, u.name AS user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_time DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 注文詳細の取得
function getOrderDetails($db, $orderId) {
    $stmt = $db->prepare("
        SELECT m.name, od.quantity, m.price
        FROM order_details od
        JOIN menu m ON od.menu_id = m.id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 本日の注文統計
function getTodayStats($db) {
    $stats = [
        'order_count' => 0,
        'total_revenue' => 0
    ];
    
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) AS order_count,
            SUM(total_price) AS total_revenue
        FROM orders 
        WHERE DATE(order_time) = CURDATE()
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $stats['order_count'] = $result['order_count'] ?: 0;
        $stats['total_revenue'] = $result['total_revenue'] ?: 0;
    }
    
    return $stats;
}

// 画面状態の管理
$screen = 'login';
if (isset($_GET['screen'])) {
    $screen = $_GET['screen'];
} elseif (!empty($_SESSION['user'])) {
    $screen = 'top';
}

// メニューデータを取得
$menus = getMenus($db);

// 本日の統計データを取得
$todayStats = getTodayStats($db);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>津田沼校舎食堂 モバイルオーダーシステム</title>
    <style>
        /* リセットと共通設定 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            min-height: 400px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .screen {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .screen.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1, h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
        }

        h2 {
            font-size: 20px;
            color: #555;
        }

        h3 {
            font-size: 18px;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #555;
            border: 2px solid #e1e5e9;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .menu-item {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .menu-item:hover {
            transform: translateY(-2px);
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .menu-name {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
        }

        .menu-price {
            font-size: 18px;
            color: #28a745;
            font-weight: 600;
        }

        .user-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: #555;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            color: #667eea;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
        }

        .back-btn:hover {
            color: #5a6fd8;
        }

        .call-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9ff;
            border-radius: 15px;
            border: 3px solid #667eea;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .order-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .order-table .btn {
            width: auto;
            padding: 6px 12px;
            margin: 0;
            font-size: 12px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .menu-management-item {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-management-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .menu-info {
            flex: 1;
        }

        .menu-info .menu-name {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
        }

        .menu-info .menu-price {
            font-size: 18px;
            color: #28a745;
            font-weight: 600;
        }

        .menu-actions {
            display: flex;
            gap: 10px;
        }

        .menu-actions .btn {
            width: auto;
            padding: 8px 16px;
            margin: 0;
            font-size: 14px;
        }

        .btn-edit {
            background: #ffc107;
            color: #212529;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }
        
        .status-cooking {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            font-weight: bold;
            cursor: pointer;
        }
        
        .remove-btn {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ログイン画面 -->
        <div id="login" class="screen <?= $screen === 'login' ? 'active' : '' ?>">
            <h1>津田沼校舎食堂 モバイルオーダーシステム</h1>
            <p style="text-align: center; margin-bottom: 30px; color: #666;">ログイン番号とパスワードを入力してください</p>

            <?php if (isset($loginError)): ?>
                <div class="error"><?= h($loginError) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label for="loginId">ログイン番号</label>
                    <input type="text" id="loginId" name="loginId" placeholder="ログイン番号を入力" value="student001">
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" placeholder="パスワードを入力" value="password">
                </div>

                <button type="submit" class="btn btn-primary">ログイン</button>
            </form>

            <div style="margin-top: 20px; font-size: 12px; color: #666; text-align: center;">
                <strong>テスト用アカウント:</strong><br>
                学生: student001 / password<br>
                管理者: admin / admin123
            </div>
        </div>

        <?php if (!empty($_SESSION['user'])): ?>
            <!-- TOP画面 -->
            <div id="top" class="screen <?= $screen === 'top' ? 'active' : '' ?>">
                <h2>メインメニュー</h2>
                <div class="user-info">
                    ようこそ、<?= h($_SESSION['user']['name']) ?>さん
                </div>
                <button class="btn btn-primary" onclick="location.href='?screen=purchase'">🍽️ 注文する</button>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <button class="btn btn-secondary" onclick="location.href='?screen=admin'">⚙️ 管理者画面</button>
                <?php endif; ?>
                <button class="btn btn-secondary" onclick="location.href='?logout=1'">📤 ログアウト</button>
            </div>

            <!-- 購入画面 -->
            <div id="purchase" class="screen <?= $screen === 'purchase' ? 'active' : '' ?>">
                <button class="back-btn" onclick="location.href='?screen=top'">← 戻る</button>
                <div class="user-info">
                    ログイン中: <?= h($_SESSION['user']['name']) ?>
                </div>
                <h2>本日のメニュー</h2>

                <div id="purchaseError" class="error" style="display: none;"></div>

                <div class="menu-grid" id="menuGrid">
                    <?php foreach ($menus as $menu): ?>
                        <div class="menu-item" id="menu-<?= h($menu['id']) ?>">
                            <div class="menu-name"><?= h($menu['name']) ?></div>
                            <div class="menu-price">¥<?= h($menu['price']) ?></div>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity(<?= h($menu['id']) ?>, -1)">−</button>
                                <span class="quantity-display" id="quantity-<?= h($menu['id']) ?>">0</span>
                                <button class="quantity-btn" onclick="updateQuantity(<?= h($menu['id']) ?>, 1)">＋</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- カート表示 -->
                <div id="cartSection" class="cart-section" style="display: none;">
                    <div class="cart-header">
                        <span>🛒 カート</span>
                        <button class="btn-danger" onclick="clearCart()" style="padding: 5px 10px; font-size: 12px;">全て削除</button>
                    </div>
                    <div id="cartItems"></div>
                    <div class="total-section">
                        <div class="total-amount">合計: ¥<span id="totalAmount">0</span></div>
                    </div>
                </div>

                <form id="orderForm" method="POST" action="">
                    <input type="hidden" name="confirm_order" value="1">
                    <input type="hidden" name="cart" id="cartInput">
                    <button type="button" class="btn btn-success" onclick="confirmOrder()" id="orderBtn" disabled>注文確定</button>
                </form>
            </div>

            <!-- 注文完了画面 -->
            <?php if ($screen === 'orderComplete' && isset($_SESSION['last_order'])): ?>
                <div id="orderComplete" class="screen active">
                    <div class="success">
                        <h2>🎉 注文が完了しました！</h2>
                        <p>お呼び出し番号</p>
                        <div class="call-number" id="callNumber"><?= h($_SESSION['last_order']['call_number']) ?></div>
                        <p>番号が呼ばれるまでお待ちください</p>
                        <div id="orderSummary" style="margin-top: 20px; font-size: 14px; color: #666;">
                            <strong>注文内容:</strong><br>
                            <?php foreach ($_SESSION['last_order']['items'] as $item): ?>
                                <?= h($item['name']) ?> × <?= h($item['quantity']) ?><br>
                            <?php endforeach; ?>
                            <br><strong>合計: ¥<?= number_format($_SESSION['last_order']['totalPrice']) ?></strong>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="location.href='?screen=top'">メインメニューに戻る</button>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <!-- 管理者画面 -->
                <div id="admin" class="screen <?= $screen === 'admin' ? 'active' : '' ?>">
                    <button class="back-btn" onclick="location.href='?screen=top'">← 戻る</button>
                    <h2>管理者画面</h2>

                    <div class="admin-stats">
                        <div class="stat-card">
                            <div class="stat-number" id="totalOrders">
                                <?= h($todayStats['order_count']) ?>
                            </div>
                            <div class="stat-label">本日の注文数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="totalRevenue">
                                ¥<?= number_format($todayStats['total_revenue']) ?>
                            </div>
                            <div class="stat-label">本日の売上</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0;">
                        <button class="btn btn-primary" onclick="location.href='?screen=menuManagement'">📝 メニュー編集</button>
                        <button class="btn btn-secondary" onclick="toggleOrderHistory()">📊 注文履歴</button>
                    </div>

                    <div id="orderHistorySection" style="display: none;">
                        <h3 style="margin: 20px 0 10px 0; color: #555;">注文履歴</h3>
                        <table class="order-table" id="orderHistoryTable">
                            <thead>
                                <tr>
                                    <th>呼出番号</th>
                                    <th>注文者</th>
                                    <th>商品</th>
                                    <th>金額</th>
                                    <th>時刻</th>
                                    <th>状態</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody id="orderHistoryBody">
                                <?php
                                    $orders = getAdminOrderHistory($db);
                                    if (empty($orders)) {
                                        echo '<tr><td colspan="7" style="text-align: center; color: #666;">注文履歴がありません</td></tr>';
                                    } else {
                                        foreach ($orders as $order) {
                                            $details = getOrderDetails($db, $order['id']);
                                            echo '<tr>';
                                            echo '<td>'.h($order['call_number']).'</td>';
                                            echo '<td>'.h($order['user_name']).'</td>';
                                            echo '<td>';
                                            foreach ($details as $detail) {
                                                echo h($detail['name']).' × '.h($detail['quantity']).'<br>';
                                            }
                                            echo '</td>';
                                            echo '<td>¥'.number_format($order['total_price']).'</td>';
                                            echo '<td>'.h($order['order_time']).'</td>';
                                            
                                            // 状態表示（色付き）
                                            echo '<td>';
                                            if ($order['status'] === '調理中') {
                                                echo '<span class="status-cooking">調理中</span>';
                                            } else {
                                                echo '<span class="status-completed">完了</span>';
                                            }
                                            echo '</td>';
                                            
                                            // 状態更新フォーム
                                            echo '<td>';
                                            echo '<form method="POST" style="display:flex;gap:5px;">';
                                            echo '<input type="hidden" name="update_status" value="1">';
                                            echo '<input type="hidden" name="order_id" value="'.h($order['id']).'">';
                                            echo '<select name="status" onchange="this.form.submit()">';
                                            echo '<option value="調理中" '.($order['status'] === '調理中' ? 'selected' : '').'>調理中</option>';
                                            echo '<option value="完了" '.($order['status'] === '完了' ? 'selected' : '').'>完了</option>';
                                            echo '</select>';
                                            echo '</form>';
                                            echo '</td>';
                                            
                                            echo '</tr>';
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- メニュー管理画面 -->
                <div id="menuManagement" class="screen <?= $screen === 'menuManagement' ? 'active' : '' ?>">
                    <button class="back-btn" onclick="location.href='?screen=admin'">← 戻る</button>
                    <h2>メニュー管理</h2>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="success">操作が完了しました</div>
                    <?php endif; ?>

                    <!-- 新しいメニュー追加 -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px 0; color: #555;">新しいメニューを追加</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="newMenuName">メニュー名</label>
                                <input type="text" id="newMenuName" name="name" placeholder="例：工大スペシャル" required>
                            </div>
                            <div class="form-group">
                                <label for="newMenuPrice">価格（円）</label>
                                <input type="number" id="newMenuPrice" name="price" placeholder="例：500" min="0" required>
                            </div>
                            <button type="submit" name="add_menu" class="btn btn-success">➕ メニューを追加</button>
                        </form>
                    </div>

                    <!-- 既存メニュー一覧 -->
                    <h3 style="margin: 20px 0 15px 0; color: #555;">現在のメニュー</h3>
                    <div id="menuList">
                        <?php if (empty($menus)): ?>
                            <p style="text-align: center; color: #666; padding: 20px;">メニューがありません</p>
                        <?php else: ?>
                            <?php foreach ($menus as $menu): ?>
                                <div class="menu-management-item">
                                    <div class="menu-info">
                                        <div class="menu-name"><?= h($menu['name']) ?></div>
                                        <div class="menu-price">¥<?= h($menu['price']) ?></div>
                                    </div>
                                    <div class="menu-actions">
                                        <button class="btn btn-edit" onclick="location.href='?screen=menuEdit&id=<?= h($menu['id']) ?>'">✏️ 編集</button>
                                        <button class="btn btn-delete" onclick="if(confirm('本当に削除しますか？')) location.href='?delete_menu=<?= h($menu['id']) ?>'">🗑️ 削除</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- メニュー編集画面 -->
                <?php if ($screen === 'menuEdit' && isset($_GET['id'])): ?>
                    <?php
                        $id = (int)$_GET['id'];
                        $stmt = $db->prepare("SELECT * FROM menu WHERE id = ?");
                        $stmt->execute([$id]);
                        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <?php if ($menu): ?>
                        <div id="menuEdit" class="screen active">
                            <button class="back-btn" onclick="location.href='?screen=menuManagement'">← 戻る</button>
                            <h2>メニュー編集</h2>

                            <form method="POST" action="">
                                <input type="hidden" name="edit_menu" value="1">
                                <input type="hidden" name="id" value="<?= h($menu['id']) ?>">
                                <div class="form-group">
                                    <label for="editMenuName">メニュー名</label>
                                    <input type="text" id="editMenuName" name="name" value="<?= h($menu['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="editMenuPrice">価格（円）</label>
                                    <input type="number" id="editMenuPrice" name="price" value="<?= h($menu['price']) ?>" min="0" required>
                                </div>
                                <button type="submit" class="btn btn-primary">💾 保存</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // グローバル変数
        let cart = {}; // { menuId: {id, name, price, quantity} }

        // 数量更新処理
        function updateQuantity(menuId, change) {
            const currentQuantity = cart[menuId] ? cart[menuId].quantity : 0;
            const newQuantity = Math.max(0, currentQuantity + change);

            if (newQuantity === 0) {
                if (cart[menuId]) {
                    delete cart[menuId];
                }
            } else {
                // メニュー情報を取得
                const menuItem = document.querySelector(`#menu-${menuId}`);
                const menuName = menuItem.querySelector('.menu-name').textContent;
                const menuPrice = parseInt(menuItem.querySelector('.menu-price').textContent.replace('¥', ''));

                cart[menuId] = {
                    id: menuId,
                    name: menuName,
                    price: menuPrice,
                    quantity: newQuantity
                };
            }

            // 数量表示を更新
            const quantityDisplay = document.getElementById(`quantity-${menuId}`);
            if (quantityDisplay) {
                quantityDisplay.textContent = newQuantity;
            }

            updateCartDisplay();
        }

        // カート表示更新
        function updateCartDisplay() {
            const cartSection = document.getElementById('cartSection');
            const cartItems = document.getElementById('cartItems');
            const totalAmount = document.getElementById('totalAmount');
            const orderBtn = document.getElementById('orderBtn');
            const cartInput = document.getElementById('cartInput');

            const cartEntries = Object.values(cart);
            
            if (cartEntries.length === 0) {
                cartSection.style.display = 'none';
                orderBtn.disabled = true;
                return;
            }

            cartSection.style.display = 'block';
            
            let total = 0;
            cartItems.innerHTML = cartEntries.map(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                return `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">¥${item.price} × ${item.quantity} = ¥${subtotal}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
                            <span class="quantity-display">${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">＋</button>
                            <button class="remove-btn" onclick="removeFromCart(${item.id})">削除</button>
                        </div>
                    </div>
                `;
            }).join('');

            totalAmount.textContent = total.toLocaleString();
            orderBtn.disabled = false;
            cartInput.value = JSON.stringify(cartEntries);
        }

        // カートから商品削除
        function removeFromCart(menuId) {
            delete cart[menuId];
            // 数量表示をリセット
            const quantityDisplay = document.getElementById(`quantity-${menuId}`);
            if (quantityDisplay) {
                quantityDisplay.textContent = '0';
            }
            updateCartDisplay();
        }

        // カート全削除
        function clearCart() {
            if (Object.keys(cart).length === 0) return;
            
            if (confirm('カートの中身をすべて削除しますか？')) {
                // すべての数量表示をリセット
                Object.keys(cart).forEach(menuId => {
                    const quantityDisplay = document.getElementById(`quantity-${menuId}`);
                    if (quantityDisplay) {
                        quantityDisplay.textContent = '0';
                    }
                });
                
                cart = {};
                updateCartDisplay();
            }
        }

        // 注文確定処理
        function confirmOrder() {
            if (Object.keys(cart).length === 0) {
                showError('purchaseError', 'カートが空です。商品を選択してください');
                return;
            }

            document.getElementById('orderForm').submit();
        }

        // 注文履歴表示切り替え
        function toggleOrderHistory() {
            const section = document.getElementById('orderHistorySection');
            if (section.style.display === 'none') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }

        // エラー表示
        function showError(elementId, message) {
            const errorDiv = document.getElementById(elementId);
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        // 初期化
        document.addEventListener('DOMContentLoaded', function () {
            // 管理者画面で注文履歴をデフォルトで表示
            <?php if ($screen === 'admin'): ?>
                document.getElementById('orderHistorySection').style.display = 'block';
            <?php endif; ?>
        });
    </script>
</body>
</html>