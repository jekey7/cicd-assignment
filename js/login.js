const loginForm = document.querySelector("#loginForm");

loginForm.addEventListener("submit", function (event) {
  const loginId = document.querySelector("#loginId").value.trim();
  const loginPw = document.querySelector("#loginPw").value.trim();

  if (loginId === "") {
    alert("아이디를 입력하세요.");
    event.preventDefault();
    return;
  }

  if (loginPw === "") {
    alert("비밀번호를 입력하세요.");
    event.preventDefault();
    return;
  }
});
