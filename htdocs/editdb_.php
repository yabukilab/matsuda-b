<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ゲーム情報修正</title>
	</head>
	<body>
		<?php

			require_once '_database_conf.php';
			require_once '_h.php';

			$pro_GameID=$_GET['proGameID'];
			

			try
			{
				$db = new PDO($dsn, $dbUser, $dbPass);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql='SELECT * FROM games WHERE GameID = :GameID';
				$stmt=$db->prepare($sql);
				$stmt->bindValue(':GameID', $pro_GameID, PDO::PARAM_INT);
				$stmt->execute();

				$dbh=null;

				$rec=$stmt->fetch(PDO::FETCH_ASSOC);

				if($rec==false)
				{
					print'GameIDが正しくありません。';
					print'<a href="edit.php">戻る</a>';
					print '<br />';
					exit();
				}

				
				$pro_title = isset($rec['title']) ? $rec['title'] : '';
				$pro_gaiyou = isset($rec['gaiyou']) ? $rec['gaiyou'] : '';

				$db = null;

			}
			catch(Exception $e)
			{
				echo 'エラーが発生しました。内容: ' . h($e->getMessage());
	 			exit();
			}

		?>

		ゲーム情報修正<br />
		<br />

		<form method="post" action="editdb_done.php">

		変更ID： <?php print $pro_GameID; ?><br />
		<input type="hidden" name="GameID" value="<?php print h($pro_GameID); ?>"><br />

		タイトル名変更<br />
		<input type="text" name="title" style="width:200px" value="<?php print h($pro_title); ?>"><br />
		概要変更<br />
		<input type="text" name="gaiyou" style="width:200px; height:50px;" value="<?php print h($pro_gaiyou); ?>"><br /><br />

		<input type="button" onclick="history.back()" value="戻る">
		<input type="submit" value="ＯＫ">

		</form>

	</body>
</html>