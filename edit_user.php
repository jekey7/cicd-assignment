<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = $_POST['userId'];
    $userName  = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userPw    = $_POST['userPw'];

    $sql = "UPDATE member SET user_pw = ?, user_name = ?, user_email = ? WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userPw, $userName, $userEmail, $userId]);
?>
<script>
    alert("회원 정보가 수정되었습니다.");
    location.replace('./admin_users.php');
</script>
<?php
    exit;
}

$userId = $_GET['userId'];
$sql = "SELECT * FROM member WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>회원수정</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <main class="page">
    <section class="card">
      <h1>회원수정</h1>
      <p class="desc">수정할 회원 정보를 입력하세요.</p>

      <form action="edit_user.php" method="post">
        <div class="form-group">
          <label for="userId">아이디</label>
          <input type="text" id="userId" name="userId" value="<?php echo $row['user_id']; ?>" readonly>
        </div>

        <div class="form-group">
          <label for="userName">이름</label>
          <input type="text" id="userName" name="userName" value="<?php echo $row['user_name']; ?>">
        </div>

        <div class="form-group">
          <label for="userEmail">이메일</label>
          <input type="email" id="userEmail" name="userEmail" value="<?php echo $row['user_email']; ?>">
        </div>

        <div class="form-group">
          <label for="userPw">비밀번호</label>
          <input type="password" id="userPw" name="userPw" placeholder="새 비밀번호 입력">
        </div>

        <button class="btn" type="submit">수정하기</button>
      </form>

      <p class="link-text">
        <a href="admin_users.php">회원 목록으로</a>
      </p>
    </section>
  </main>
</body>
</html>
