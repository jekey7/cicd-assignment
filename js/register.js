const registerForm = document.querySelector("#registerForm");

registerForm.addEventListener("submit", function (event) {
  const userId = document.querySelector("#userId").value.trim();
  const userName = document.querySelector("#userName").value.trim();
  const userEmail = document.querySelector("#userEmail").value.trim();
  const userPw = document.querySelector("#userPw").value.trim();
  const userPwCheck = document.querySelector("#userPwCheck").value.trim();

  if (userId === "") {
    alert("아이디를 입력하세요.");
    event.preventDefault();
    return;
  }

  if (userName === "") {
    alert("이름을 입력하세요.");
    event.preventDefault();
    return;
  }

  if (userEmail === "") {
    alert("이메일을 입력하세요.");
    event.preventDefault();
    return;
  }

  if (userPw === "") {
    alert("비밀번호를 입력하세요.");
    event.preventDefault();
    return;
  }

  if (userPwCheck === "") {
    alert("비밀번호 확인을 입력하세요.");
    event.preventDefault();
    return;
  }

  if (userPw !== userPwCheck) {
    alert("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
    event.preventDefault();
    return;
  }
});
