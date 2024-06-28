<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>DB変更</title>
	</head>
	<body>
	<?php
require_once '_database_conf.php';
require_once '_h.php';

$pro_GameID=$_POST['GameID'];
$pro_title=$_POST['title'];
$pro_gaiyou=$_POST['gaiyou'];

try
{
	$db = new PDO($dsn, $dbUser, $dbPass);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql='UPDATE games SET title=:title,gaiyou=:gaiyou WHERE GameID=:GameID';
	$stmt=$db->prepare($sql);
	$stmt->bindValue(':GameID', $pro_GameID, PDO::PARAM_INT);
	$stmt->bindValue(':title', $pro_title, PDO::PARAM_STR);
	$stmt->bindValue(':gaiyou', $pro_gaiyou, PDO::PARAM_STR);
	$stmt->execute();

	$db=null;

	print '修正しました。<br />';

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
