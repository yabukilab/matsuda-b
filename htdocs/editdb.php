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

try
{
	$db = new PDO($dsn, $dbUser, $dbPass);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql='SELECT * FROM games';
	$stmt=$db->prepare($sql);
	$stmt->execute();

	$db=null;

	
	

	print '<br />';
	print '<form method="get" action="editdb_.php">';
	print 'ゲーム情報修正：GameID';
	print '<input type="text" name="proGameID" style="width:20px">';
	print '<input type="submit" value="決定">';
	print '</form>';
}
catch (PDOException $e)
{
	echo 'エラーが発生しました。内容: ' . h($e->getMessage());
	 exit();
}


?>
</body>
</html>
