<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>DB登録</title>
	</head>
	<body>
		<?php

			require_once '_database_conf.php';
			require_once '_h.php';

			$pro_Title=$_POST['title'];
			

			try
			{
				$db = new PDO($dsn, $dbUser, $dbPass);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql='INSERT INTO games(Title) VALUES (:Title)';
				$stmt=$db->prepare($sql);
				$stmt->bindValue(':Title', $pro_Title, PDO::PARAM_STR);
				
				$stmt->execute();

				$db=null;

				print '追加しました。<br />';

			}
			catch(Exception$e)
			{
				echo 'エラーが発生しました。内容: ' . h($e->getMessage());
	 			exit();
			}

		?>
		<a href="edit.php">戻る</a>
	</body>
</html>