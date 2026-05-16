# 2026년 하계 글로벌 교육 프로그램 특강

## 프로젝트 설명

이 프로젝트는 2026년 하계 글로벌 교육 프로그램 특강에서 진행하는 회원 관리 웹 제작 프로젝트입니다.

1주차: PHP, HTML, CSS, JavaScript로 웹 제작
2주차: Docker와 GitHub Actions를 통한 CI/CD 파이프라인을 구성

## 사용 기술

- PHP
- HTML
- CSS
- JavaScript
- MySQL
- Docker
- Docker Compose
- Prometheus
- Grafana
- GitHub Actions
- Docker Hub
- Trivy

## 프로젝트 구조

```text
.
├── Dockerfile
├── docker-compose.yml
├── mysql/
│   ├── init.sql
│   └── exporter.cnf
├── prometheus/
│   └── prometheus.yml
├── grafana/
│   ├── dashboards/
│   │   └── mysql-overview.json
│   └── provisioning/
├── .github/
│   └── workflows/
│       └── docker-image.yml
├── db.php
├── login.html
├── register.html
├── login_check.php
├── admin_users.php
├── insert_user.php
├── edit_user.php
├── delete_user.php
├── css/
└── js/
```

## 로컬 실행 방법

XAMPP에서 MySQL을 실행한 뒤, 프로젝트 루트에서 PHP 내장 서버를 실행합니다.

```powershell
C:\xampp\php\php.exe -S localhost:8080
```

브라우저에서 접속합니다.

```text
http://localhost:8080/login.html
```

## 데이터베이스 설정

기본 DB 설정은 `db.php`에서 관리합니다.

```php
$host = getenv("DB_HOST") ?: "localhost";
$dbname = getenv("DB_NAME") ?: "my_site";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";
```

로컬 XAMPP 실행 시에는 `my_site` 데이터베이스와 `member` 테이블이 필요합니다.

Docker Compose 실행 시에는 `mysql/init.sql`을 통해 `member` 테이블과 테스트 계정이 자동 생성됩니다.

## Docker 이미지 빌드

```powershell
docker build -t cicd-assignment .
```

## Docker 단일 컨테이너 실행

```powershell
docker run --name cicd-assignment-test -p 8080:8080 cicd-assignment
```

브라우저에서 접속합니다.

```text
http://localhost:8080/login.html
```

컨테이너 정리:

```powershell
docker rm -f cicd-assignment-test
```

단일 컨테이너 실행은 PHP 애플리케이션만 실행합니다. DB 기능까지 테스트하려면 Docker Compose 실행을 사용합니다.

## Docker Compose 실행

PHP 애플리케이션, MySQL 데이터베이스, Prometheus, Grafana를 함께 실행합니다.

```powershell
docker compose up --build
```

웹 애플리케이션:

```text
http://localhost:8080/login.html
```

테스트 계정:

```text
ID: admin
PW: admin123
```

Prometheus:

```text
http://localhost:9090
```

Grafana:

```text
http://localhost:3000
```

Grafana 기본 로그인:

```text
ID: admin
PW: admin
```

Grafana에는 Prometheus datasource와 `MySQL Overview` 대시보드가 자동 등록됩니다.

컨테이너 종료:

```powershell
docker compose down
```

DB 볼륨까지 초기화:

```powershell
docker compose down -v
```

## 모니터링 구성

Docker Compose에는 모니터링용 컨테이너가 포함되어 있습니다.

- `mysql-exporter`: MySQL 상태를 Prometheus 메트릭으로 노출
- `prometheus`: 메트릭 수집
- `grafana`: 대시보드 시각화

Prometheus scrape 설정 파일:

```text
prometheus/prometheus.yml
```

Grafana datasource 설정:

```text
grafana/provisioning/datasources/prometheus.yml
```

Grafana dashboard 설정:

```text
grafana/dashboards/mysql-overview.json
```

MySQL exporter 계정은 `mysql/init.sql`에서 생성됩니다. 기존 Docker volume이 이미 만들어져 있다면 아래 명령으로 DB를 초기화한 뒤 다시 실행해야 exporter 계정이 생성됩니다.

```powershell
docker compose down -v
docker compose up --build
```

## Docker Hub 이미지

Docker Hub repository:

```text
https://hub.docker.com/r/jaekyup/cicd-assignment
```

이미지 pull:

```powershell
docker pull jaekyup/cicd-assignment:latest
```

## CI/CD 파이프라인

이 프로젝트는 GitHub Actions를 사용해 Docker 이미지를 자동으로 빌드하고 Docker Hub에 push합니다.

Workflow는 `main` 브랜치에 push될 때 실행됩니다.

수행 단계:

- Repository checkout
- Docker Hub 로그인
- Docker Buildx 설정
- Docker 이미지 빌드
- Trivy 보안 스캔
- Docker Hub 이미지 push

## Trivy 보안 스캔

이 프로젝트는 GitHub Actions에서 Docker 이미지를 빌드한 뒤 Trivy를 사용해 컨테이너 이미지 취약점을 검사합니다.

Workflow는 Docker Hub에 이미지를 push하기 전에 GitHub Actions runner에서 로컬로 빌드된 이미지를 먼저 스캔합니다. `CRITICAL` 등급 취약점이 발견되면 workflow가 실패하도록 설정했습니다.

```yaml
exit-code: 1
severity: CRITICAL
```

이 설정을 통해 심각도가 높은 취약점이 포함된 이미지는 Docker Hub에 배포되지 않도록 차단합니다.

## GitHub Actions

Workflow 파일:

```text
.github/workflows/docker-image.yml
```

실행 조건:

```text
main 브랜치 push
```

Docker Hub에 생성되는 태그:

```text
jaekyup/cicd-assignment:latest
jaekyup/cicd-assignment:<git-commit-sha>
```

Workflow는 이미지 빌드 후 Trivy 스캔을 수행하고, 스캔을 통과한 경우에만 Docker Hub에 이미지를 push합니다.

GitHub Secrets:

```text
DOCKERHUB_USERNAME
DOCKERHUB_TOKEN
```
