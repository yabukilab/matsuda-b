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
		//$=$_POST[''];
		//$=$_POST[''];

		try{
			if($pro_Title=='')
			{
				print'タイトルが入力されていません。<br />';	
		}
		$db = new PDO($dsn, $dbUser, $dbPass);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql='UPDATE FROM games VALUES :Title';
				$stmt=$db->prepare($sql);
				$stmt->bindValue(':Title', $pro_Title, PDO::PARAM_STR);
				
				$stmt->execute();

				$db=null;

				print '追加しました。<br />';

		}
		catch(PDOException$e)
		{
			echo 'エラーが発生しました。内容: ' . h($e->getMessage());
			exit();
		}

		?>
		<a href="edit.php">戻る</a>
