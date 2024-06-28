<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>情報修正</title>
	</head>
	<body>
		<?php

			require_once '_database_conf.php';
			require_once '_h.php';

			if (isset($_GET['Title'])) {
				$title = $_GET['Title'];
			} else {
				$title = null;
			}
			if ($title === null) {
				// Display the input form
				echo '<form action="" method="GET">';
				echo 'ゲームタイトル: <input type="text" name="Title"><br>';
				echo '<input type="submit" value="変更" >';
				echo '</form>';
			} else {

			try
			{
				$db = new PDO($dsn, $dbUser, $dbPass);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql = 'DELETE FROM games WHERE Title = :Title';
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':Title', $title, PDO::PARAM_INT);
					$stmt->execute();

				$dbh=null;

				$rec=$stmt->fetch(PDO::FETCH_ASSOC);

				if($rec==false)
				{
					print'タイトル名が正しくありません。';
					print'<a href="edit.php">戻る</a>';
					print '<br />';
					exit();
				}

				$title = $rec['Title'];
				$pro_price = $rec['price'];

			}
			catch(Exception $e)
			{
				echo 'エラーが発生しました。内容: ' . h($e->getMessage());
	 			exit();
			}
		}


		?>

		ゲーム情報修正<br />
		<br />

		<form method="post" action="editdb_done.php">


		ゲーム名<br />
		<input type="text" name="title" style="width:200px" value="<?php print $title; ?>"><br />
		//価格<br />
		//<input type="text" name="price" style="width:50px" value="<?php print $pro_price; ?>">円<br /><br />

		<input type="button" onclick="history.back()" value="戻る">
		<input type="submit" value="ＯＫ">

		</form>

	</body>
</html>
