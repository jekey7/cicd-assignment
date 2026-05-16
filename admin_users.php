<?php
include "db.php";

$sql = "SELECT * FROM member";
$stmt = $pdo->prepare($sql);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>회원관리</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <main class="admin-page">
    <section class="admin-card">
      <div class="admin-header">
        <div>
          <h1>회원관리</h1>
          <p class="desc">사이트에 가입한 회원 목록을 확인하는 관리자 페이지입니다.</p>
        </div>
        <a class="small-link" href="login.html">로그인 페이지</a>
      </div>

      <table class="member-table">
        <thead>
          <tr>
            <th>번호</th>
            <th>아이디</th>
            <th>이름</th>
            <th>이메일</th>
            <th>가입일</th>
            <th>관리</th>
          </tr>
        </thead>

        <tbody>
          <?php 
		  $num = 1;
		  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
              <td><?php echo $num; ?></td>
              <td><?php echo $row['user_id']; ?></td>
              <td><?php echo $row['user_name']; ?></td>
              <td><?php echo $row['user_email']; ?></td>
              <td><?php echo $row['user_reg_datetime']; ?></td>
              <td>
                <a href="edit_user.php?userId=<?php echo $row['user_id']; ?>" class="table-btn edit">수정</a>

                <form action="delete_user.php" method="post" style="display:inline;">
                  <input type="hidden" name="userId" value="<?php echo $row['user_id']; ?>">
                  <button class="table-btn delete" type="submit">삭제</button>
                </form>
              </td>
            </tr>
          <?php 
		  $num++;
		  } ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>