# capybara-online-judge

システム設計学の授業で開発したオンラインジャッジメントシステムです。
[AtCoder](https://atcoder.jp/) を参考に開発しました。

## コントリビュートする

コントリビュートする前に[CONTRIBUTING.md](docs/CONTRIBUTING.md) を読んで、Issue または PR を作成してください。

## ライセンス

このプロジェクトは MIT License で開発されています。詳しくは[LICENSE](LICENSE)を参照してください。

## デプロイ

以下に Docker を使ったデプロイ方法を示します。

1. リポジトリをクローンする
2. サブモジュールを更新する

```bash
$ git submodule update --init
```

3. `.env.example` ファイルを参考に `.env` ファイルを作成する

```
POSTGRES_HOST=postgres
POSTGRES_PORT=5432
POSTGRES_USER=<Your postgres's username goes here>
POSTGRES_PASSWORD=<Your postgres's password goes here>
POSTGRES_DB=<Your postgres's database name goes here>

REDIS_HOST=redis
REDIS_PORT=6379

JWT_SECRET=<Your JWT secret goes here>
```

4. プログラム実行用のベースラインをビルドする

```bash
$ docker run --rm -v /var/run/docker.sock:/var/run/docker.sock -v .:/workspace -w /workspace mcr.microsoft.com/devcontainers/php:8-bullseye /bin/sh -c "composer install && php tools/BuildImage.php"
```

5. Docker でコンテナを起動する

```bash
$ docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

> コマンド末尾の引数に `--scale judger=<Number of judger you want to run>` を加えるとプログラム実行システムのレプリカを増やせます

6. ユーザーの新規登録を行い、管理者権限を付与する

```bash
$ docker exec <Postgres container name or id> psql -U <Your postgres's username> <Your postgres's database name> -c 'UPDATE "Users" SET "IsAdmin" = true WHERE "Username" = \'<Your username>\';'
```

> 管理者権限を付与するにはデータベースのレコードを更新する必要があります
