<?php
	include "db.php";

	$userId = $_POST['userId'];
	$user_pw = $_POST['userPw'];
	$user_name = $_POST['userName'];
	$user_email = $_POST['userEmail'];
	
	$sql = "
	INSERT INTO `member` 
	(`user_id`, `user_pw`, `user_name`, `user_email`, `user_reg_datetime`) VALUES 
	('{$userId}', '{$user_pw}', '{$user_name}', '{$user_email}', NOW());
	";
	$pdo->exec($sql);
?>
<script>
	alert("아이디가 추가되었습니다");
	location.replace('./login.html'); 
</script>