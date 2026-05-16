# CI/CD 과제 수행 로드맵

## 0. 목표

이 과제의 목표는 현재 웹 프로젝트를 Docker 이미지로 패키징하고, GitHub Actions를 통해 Docker Hub에 자동으로 배포되는 CI/CD 흐름을 구성한 뒤 제출 자료를 정리하는 것이다.

최종 제출물은 다음 3가지이다.

- Docker Hub 이미지 링크
- GitHub Actions 실행 로그 또는 캡처
- README.md

선택 보너스 항목은 다음과 같다.

- Docker Compose 기반 다중 컨테이너 구성
- Trivy 보안 스캔 연동
- 모니터링 대시보드 구축

## 1. 전체 진행 순서

```text
프로젝트 현황 파악
→ 로컬 실행 확인
→ Dockerfile 작성
→ .dockerignore 작성
→ Docker 이미지 로컬 빌드
→ Docker 컨테이너 실행 테스트
→ Docker Hub 저장소 생성
→ GitHub 저장소 준비
→ GitHub Secrets 등록
→ GitHub Actions workflow 작성
→ GitHub에 push
→ Actions 실행 결과 확인
→ Docker Hub 이미지 확인
→ README.md 작성
→ 제출 자료 정리
```

## 2. 프로젝트 현황 파악

현재 폴더에는 PHP 웹 프로젝트 파일이 존재한다.

주요 파일 예시는 다음과 같다.

- `login.html`
- `register.html`
- `admin_users.php`
- `db.php`
- `insert_user.php`
- `edit_user.php`
- `delete_user.php`
- `login_check.php`
- `css/`
- `js/`

이 프로젝트는 PHP 파일과 정적 HTML/CSS/JS 파일로 구성되어 있으므로, Docker 이미지에는 PHP 실행 환경이 필요하다.

데이터베이스를 사용하는 경우 `db.php`의 DB 접속 정보도 확인해야 한다. MySQL 같은 DB가 필요하다면 단일 컨테이너만으로는 완전 실행이 어려울 수 있으므로 Docker Compose 구성을 보너스 과제로 함께 고려할 수 있다.

## 3. 로컬 실행 확인

Docker 작업 전에 먼저 프로젝트가 로컬에서 실행되는지 확인한다.

PHP 내장 서버로 실행 가능한 경우 예시는 다음과 같다.

```bash
php -S localhost:8080
```

브라우저에서 다음 주소를 확인한다.

```text
http://localhost:8080/login.html
```

확인할 항목은 다음과 같다.

- 로그인 페이지가 열리는가
- 회원가입 페이지가 열리는가
- CSS와 JS가 정상 적용되는가
- PHP 요청이 정상 동작하는가
- DB 연결이 필요한 기능이 있다면 DB 연결 오류가 없는가

이 단계에서 실패하면 Docker에서도 실패할 가능성이 높으므로 먼저 프로젝트 실행 문제를 해결한다.

## 4. Dockerfile 작성

프로젝트 루트에 `Dockerfile`을 작성한다.

PHP 내장 서버로 실행하는 가장 단순한 예시는 다음과 같다.

```dockerfile
FROM php:8.2-cli

WORKDIR /app

COPY . .

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]
```

이 방식은 과제용으로 단순하고 이해하기 쉽다.

다만 DB 연결에 MySQL 확장이 필요하다면 `mysqli` 또는 `pdo_mysql` 확장을 설치해야 할 수 있다. 예시는 다음과 같다.

```dockerfile
FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app

COPY . .

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]
```

`db.php`에서 어떤 방식으로 DB에 연결하는지 확인한 뒤 필요한 확장을 선택한다.

## 5. .dockerignore 작성

Docker 이미지에 불필요한 파일이 들어가지 않도록 `.dockerignore`를 작성한다.

예시는 다음과 같다.

```text
.git
.github
.env
node_modules
vendor
*.log
README-draft.md
```

현재 프로젝트가 PHP 중심이고 별도 의존성 폴더가 없다면 최소한 `.git`, `.env`, 로그 파일 정도는 제외한다.

주의할 점은 과제 제출에 필요한 파일까지 제외하지 않는 것이다. 일반적으로 `README.md`는 이미지에 포함되어도 큰 문제는 없지만, 실행에 필요하지 않다면 제외해도 된다.

## 6. Docker 이미지 로컬 빌드

Dockerfile 작성 후 로컬에서 이미지를 빌드한다.

```bash
docker build -t cicd-assignment .
```

빌드 성공 여부를 확인한다.

```bash
docker images
```

확인할 항목은 다음과 같다.

- 이미지가 정상 생성되었는가
- Dockerfile 명령어에서 오류가 없는가
- PHP 확장 설치가 실패하지 않았는가
- 불필요한 파일이 이미지에 포함되지 않았는가

## 7. Docker 컨테이너 실행 테스트

빌드한 이미지를 컨테이너로 실행한다.

```bash
docker run --name cicd-assignment-test -p 8080:8080 cicd-assignment
```

브라우저에서 확인한다.

```text
http://localhost:8080/login.html
```

테스트가 끝나면 컨테이너를 정리한다.

```bash
docker stop cicd-assignment-test
docker rm cicd-assignment-test
```

확인할 항목은 다음과 같다.

- 컨테이너가 정상 실행되는가
- 웹 페이지가 정상 표시되는가
- PHP 파일 요청이 동작하는가
- DB 연결 기능이 필요한 경우 DB 연결 방식이 명확한가

DB가 필요한 기능이 실패한다면 다음 중 하나를 선택한다.

- 과제 필수 범위는 웹 앱 컨테이너 빌드와 배포로 제한하고 README에 DB 설정 필요 사항을 명시한다.
- Docker Compose로 PHP 앱과 MySQL을 함께 실행하도록 구성한다.

## 8. Docker Hub 저장소 생성

Docker Hub에 로그인한 뒤 새 repository를 만든다.

예시 이름은 다음과 같다.

```text
cicd-assignment
```

최종 이미지 이름은 보통 다음 형식이 된다.

```text
dockerhub-username/cicd-assignment
```

예시는 다음과 같다.

```text
jekey/cicd-assignment
```

제출할 Docker Hub 링크 예시는 다음과 같다.

```text
https://hub.docker.com/r/dockerhub-username/cicd-assignment
```

## 9. Docker Hub 수동 Push 테스트

GitHub Actions를 만들기 전에 로컬에서 한 번 push를 테스트하면 문제를 빠르게 찾을 수 있다.

Docker Hub에 로그인한다.

```bash
docker login
```

이미지에 Docker Hub용 태그를 붙인다.

```bash
docker tag cicd-assignment dockerhub-username/cicd-assignment:latest
```

Docker Hub에 push한다.

```bash
docker push dockerhub-username/cicd-assignment:latest
```

Docker Hub 페이지에서 이미지가 올라갔는지 확인한다.

이 단계에서 성공하면 Dockerfile과 Docker Hub repository 이름은 기본적으로 정상이다.

## 10. GitHub 저장소 준비

GitHub에 과제용 repository를 준비한다.

아직 Git 저장소가 아니라면 초기화한다.

```bash
git init
git add .
git commit -m "Initial commit"
```

원격 저장소를 연결한다.

```bash
git remote add origin https://github.com/username/repository-name.git
git branch -M main
git push -u origin main
```

이미 Git 저장소라면 다음 명령으로 상태를 확인한다.

```bash
git status
```

확인할 항목은 다음과 같다.

- Dockerfile이 커밋 대상에 포함되어 있는가
- `.dockerignore`가 커밋 대상에 포함되어 있는가
- `.env` 같은 민감 정보가 커밋되지 않았는가
- `.gitignore`가 필요한 파일을 제외하고 있는가

## 11. GitHub Secrets 등록

GitHub repository에서 다음 경로로 이동한다.

```text
Settings → Secrets and variables → Actions → New repository secret
```

다음 Secret을 등록한다.

```text
DOCKERHUB_USERNAME
DOCKERHUB_TOKEN
```

`DOCKERHUB_USERNAME`에는 Docker Hub 사용자명을 넣는다.

`DOCKERHUB_TOKEN`에는 Docker Hub 비밀번호 대신 Access Token을 넣는 것이 좋다.

Docker Hub Access Token 생성 경로는 다음과 같다.

```text
Docker Hub → Account Settings → Personal access tokens
```

주의할 점은 다음과 같다.

- Secret 이름은 workflow 파일에서 사용하는 이름과 정확히 같아야 한다.
- Token 값을 코드에 직접 작성하면 안 된다.
- GitHub Actions 로그에 비밀번호나 토큰이 노출되면 안 된다.

## 12. GitHub Actions Workflow 작성

프로젝트 루트에 다음 폴더와 파일을 만든다.

```text
.github/workflows/docker-image.yml
```

기본 workflow 예시는 다음과 같다.

```yaml
name: Docker Image CI

on:
  push:
    branches:
      - main
  pull_request:
    branches:
      - main

jobs:
  build-and-push:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Log in to Docker Hub
        uses: docker/login-action@v3
        with:
          username: ${{ secrets.DOCKERHUB_USERNAME }}
          password: ${{ secrets.DOCKERHUB_TOKEN }}

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3

      - name: Build and push Docker image
        uses: docker/build-push-action@v6
        with:
          context: .
          push: true
          tags: |
            dockerhub-username/cicd-assignment:latest
            dockerhub-username/cicd-assignment:${{ github.sha }}
```

반드시 수정해야 하는 부분은 다음이다.

```text
dockerhub-username/cicd-assignment
```

자신의 Docker Hub 사용자명과 repository 이름으로 바꾼다.

예시는 다음과 같다.

```text
jekey/cicd-assignment
```

## 13. GitHub Actions 실행

workflow 파일을 커밋하고 push한다.

```bash
git add Dockerfile .dockerignore .github/workflows/docker-image.yml README.md
git commit -m "Add Docker CI workflow"
git push
```

GitHub repository의 Actions 탭에서 workflow 실행을 확인한다.

확인할 항목은 다음과 같다.

- Workflow가 자동으로 실행되는가
- Checkout 단계가 성공하는가
- Docker Hub 로그인 단계가 성공하는가
- Docker build 단계가 성공하는가
- Docker push 단계가 성공하는가
- Docker Hub에 `latest` 태그와 Git SHA 태그가 생성되는가

## 14. GitHub Actions 로그 저장

과제 제출용으로 Actions 실행 결과를 저장한다.

가능한 제출 방식은 다음과 같다.

- Actions 성공 화면 캡처
- build-and-push job 상세 로그 캡처
- 로그 파일 다운로드
- 주요 로그를 텍스트 파일로 저장

캡처에는 다음 정보가 보이면 좋다.

- workflow 이름
- 실행 시각
- branch 이름
- 성공 표시
- Docker build/push 단계

실패 로그를 제출해도 된다는 조건이 있다면 실패 원인과 수정 내용을 README에 간단히 적는 것도 가능하다. 하지만 가능한 한 성공 로그를 제출하는 것이 좋다.

## 15. Docker Hub 이미지 확인

Docker Hub repository 페이지에서 이미지가 정상 push되었는지 확인한다.

확인할 항목은 다음과 같다.

- repository가 공개 또는 제출자가 확인 가능한 상태인가
- `latest` 태그가 있는가
- Git SHA 기반 태그가 있는가
- 최근 push 시간이 GitHub Actions 실행 시간과 맞는가

제출용 링크 형식은 다음과 같다.

```text
https://hub.docker.com/r/dockerhub-username/cicd-assignment
```

## 16. README.md 작성

README에는 최소한 다음 내용을 포함한다.

```markdown
# 프로젝트 이름

## 프로젝트 설명

이 프로젝트는 PHP 기반 웹 프로젝트이며, Docker와 GitHub Actions를 사용해 CI/CD 파이프라인을 구성한 과제입니다.

## 사용 기술

- PHP
- HTML
- CSS
- JavaScript
- Docker
- GitHub Actions
- Docker Hub

## 로컬 실행 방법

```bash
php -S localhost:8080
```

브라우저에서 접속:

```text
http://localhost:8080/login.html
```

## Docker 빌드 방법

```bash
docker build -t cicd-assignment .
```

## Docker 실행 방법

```bash
docker run -p 8080:8080 cicd-assignment
```

브라우저에서 접속:

```text
http://localhost:8080/login.html
```

## Docker Hub 이미지

```text
https://hub.docker.com/r/dockerhub-username/cicd-assignment
```

## CI/CD 설명

main 브랜치에 push 또는 pull request가 발생하면 GitHub Actions가 실행됩니다.
Workflow는 Docker 이미지를 빌드한 뒤 Docker Hub에 push합니다.

## GitHub Actions

- Workflow 파일: `.github/workflows/docker-image.yml`
- 실행 조건: main 브랜치 push, pull request
- 수행 작업: Checkout, Docker Hub 로그인, Docker Buildx 설정, Docker 이미지 빌드 및 push
```

실제 README 작성 시에는 Docker Hub 사용자명과 repository 이름을 반드시 실제 값으로 바꾼다.

## 17. 보너스 1: Docker Compose 구성

DB가 필요한 프로젝트라면 Docker Compose를 구성하는 것이 좋다.

예시 구성은 다음과 같다.

```yaml
services:
  app:
    build: .
    ports:
      - "8080:8080"
    depends_on:
      - db
    environment:
      DB_HOST: db
      DB_NAME: app_db
      DB_USER: app_user
      DB_PASS: app_password

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: app_db
      MYSQL_USER: app_user
      MYSQL_PASSWORD: app_password
      MYSQL_ROOT_PASSWORD: root_password
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

실행 명령은 다음과 같다.

```bash
docker compose up -d
```

종료 명령은 다음과 같다.

```bash
docker compose down
```

이 구성을 사용하려면 `db.php`가 환경변수를 읽도록 수정하는 것이 좋다.

## 18. 보너스 2: Trivy 보안 스캔

GitHub Actions에 Trivy 스캔 단계를 추가할 수 있다.

예시 단계는 다음과 같다.

```yaml
- name: Run Trivy vulnerability scanner
  uses: aquasecurity/trivy-action@master
  with:
    image-ref: dockerhub-username/cicd-assignment:latest
    format: table
    exit-code: 0
```

과제에서는 보안 스캔을 수행했다는 증거로 Actions 로그에 Trivy 실행 결과가 남으면 좋다.

처음에는 `exit-code: 0`으로 설정해 취약점이 있어도 workflow가 실패하지 않게 할 수 있다. 더 엄격하게 운영하려면 이후 `exit-code: 1`로 변경한다.

## 19. 보너스 3: 모니터링 대시보드

모니터링 대시보드는 필수 과제보다 작업량이 크므로 마지막에 여유가 있을 때 진행한다.

간단한 방향은 다음과 같다.

- Docker Compose에 Prometheus 추가
- Grafana 추가
- 컨테이너 상태 또는 웹 서버 상태를 시각화

다만 현재 과제의 핵심은 Docker Hub 이미지, GitHub Actions 로그, README이므로 보너스보다 필수 항목 완성을 우선한다.

## 20. 자주 발생하는 문제와 해결 기준

### Docker build 실패

확인할 항목:

- Dockerfile 파일명이 정확한가
- Dockerfile이 프로젝트 루트에 있는가
- `COPY . .` 단계에서 불필요한 파일이 너무 많이 포함되지 않는가
- PHP 확장 설치 명령이 맞는가

### Docker run 후 페이지 접속 실패

확인할 항목:

- `EXPOSE 8080`이 설정되어 있는가
- `docker run -p 8080:8080`으로 포트 매핑했는가
- PHP 서버가 `localhost`가 아니라 `0.0.0.0`으로 실행되는가
- 접속 경로가 `/login.html`인지 확인했는가

### GitHub Actions가 실행되지 않음

확인할 항목:

- workflow 파일 위치가 `.github/workflows/` 아래인가
- 파일 확장자가 `.yml` 또는 `.yaml`인가
- main 브랜치에 push했는가
- YAML 문법 오류가 없는가

### Docker Hub 로그인 실패

확인할 항목:

- `DOCKERHUB_USERNAME` Secret 이름이 정확한가
- `DOCKERHUB_TOKEN` Secret 이름이 정확한가
- Docker Hub Access Token이 유효한가
- workflow에서 `${{ secrets.DOCKERHUB_TOKEN }}` 문법을 정확히 사용했는가

### Docker push 실패

확인할 항목:

- Docker Hub repository 이름이 정확한가
- 태그의 사용자명이 Docker Hub 사용자명과 일치하는가
- Docker Hub repository에 push 권한이 있는가
- repository가 이미 생성되어 있는가

## 21. 제출 전 최종 체크리스트

필수 항목:

- [ ] 프로젝트가 로컬에서 실행된다.
- [ ] Dockerfile이 작성되어 있다.
- [ ] `.dockerignore`가 작성되어 있다.
- [ ] Docker 이미지가 로컬에서 빌드된다.
- [ ] Docker 컨테이너가 로컬에서 실행된다.
- [ ] Docker Hub repository가 생성되어 있다.
- [ ] Docker Hub에 이미지가 push되어 있다.
- [ ] GitHub Secrets가 등록되어 있다.
- [ ] GitHub Actions workflow가 작성되어 있다.
- [ ] GitHub Actions가 성공 실행되었다.
- [ ] Actions 로그 또는 캡처를 확보했다.
- [ ] README.md가 작성되어 있다.
- [ ] README.md에 Docker Hub 링크가 포함되어 있다.
- [ ] README.md에 Docker 실행 방법이 포함되어 있다.

보너스 항목:

- [ ] Docker Compose 구성이 있다.
- [ ] Trivy 보안 스캔이 Actions에 포함되어 있다.
- [ ] 모니터링 대시보드 구성이 있다.

## 22. 권장 우선순위

시간이 부족하다면 다음 순서로 처리한다.

1. Dockerfile 작성
2. 로컬 Docker 빌드 및 실행 성공
3. Docker Hub 수동 push 성공
4. GitHub Actions 자동 push 성공
5. README.md 작성
6. Actions 로그 캡처
7. Docker Compose 보너스
8. Trivy 보너스
9. 모니터링 보너스

필수 과제만 확실히 제출해도 기본 요구사항은 충족된다. 보너스는 필수 항목이 모두 성공한 뒤 진행한다.
