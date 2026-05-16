<?php
	include "db.php";

	$userId = $_POST['userId'];
	$sql = "
	DELETE FROM `member` 
	WHERE `member`.`user_id` = '{$userId}';
	";
	$pdo->exec($sql);
?>
<script>
	alert("아이디가 삭제되었습니다");
	location.replace('./admin_users.php');
</script>