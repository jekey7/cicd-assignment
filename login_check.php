<?php
include "db.php";

$id = $_POST['loginId'];
$pw = $_POST['loginPw'];

$sql = "SELECT * FROM member WHERE user_id = ? AND user_pw = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $pw]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    header("Location: http://127.0.0.1/admin_users.php");
    exit;
}
?>
<script>
    alert("아이디 또는 비밀번호가 올바르지 않습니다.");
    location.replace('./login.html');
</script>
