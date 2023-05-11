# 開発環境

## devcontainer を使って構築する場合

1. このリポジトリをクローンする

```bash
git clone https://github.com/mtaku3/cuddly-chainsaw.git
```

2. サブモジュールを更新する

```bash
git submodule init
git submodule update
```

3. .env.example ファイルを参考に.env を作成する

```
COMPOSE_PROJECT_NAME=cuddly-chainsaw-b00000 # アカウント名は適宜変更

POSTGRES_HOST=localhost
POSTGRES_PORT=5432
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
POSTGRES_DB=dev

REDIS_HOST=localhost
REDIS_PORT=6379
```

4. VS Code で開いて devcontainer を起動する

## devcontainer を使わずに構築する場合

### 事前にインストールするもの

- PHP 8.2.5
  - pdo
  - pdo_pgsql
  - redis
- Composer 2.5.5
- nodejs v20.0.0
- npm 9.6.4
- python 3.9.2
- pip 23.1.2
- postgresql 14.7-1.pgdg110+1
- redis 7.0.11
- Apache 2.4.56

### 構築手順

1. このリポジトリをクローンする

```bash
git clone https://github.com/mtaku3/cuddly-chainsaw.git
```

2. サブモジュールを更新する

```bash
git submodule init
git submodule update
```

3. composer のパッケージをインストールする

```bash
composer install
```

4. npm のパッケージをインストールする

```bash
npm install -D
```

5. Flowbite の.js ファイルのリンクを dist フォルダに作る

```bash
ln ./node_modules/flowbite/dist/flowbite.min.js ./dist/
```

6. .env.example ファイルを参考に.env を作成する

7. Apache の DocumentRoot を dist フォルダに設定する

8. PostgreSQL, Redis, Apache2 を起動する

# デプロイ

1. このリポジトリをクローンする

```bash
git clone https://github.com/mtaku3/cuddly-chainsaw.git
```

2. サブモジュールを更新する

```bash
git submodule init
git submodule update
```

3. .env.example ファイルを参考に.env を作成する

```
POSTGRES_HOST=localhost
POSTGRES_PORT=5432
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
POSTGRES_DB=dev

REDIS_HOST=localhost
REDIS_PORT=6379
```

4. Docker でコンテナを起動する

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```
