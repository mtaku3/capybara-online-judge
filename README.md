# capybara-online-judge

システム設計学の授業で開発したオンラインジャッジメントシステムです。
[AtCoder](https://atcoder.jp/) を参考に開発しました。

## コントリビュートする

コントリビュートする前に[CONTRIBUTING.md](CONTRIBUTING.md) を読んで、Issue または PR を作成してください。

## ライセンス

このプロジェクトは MIT License で開発されています。詳しくは[LICENSE](LICENSE)を参照してください。

## デプロイ

以下に Docker を使ったデプロイ方法を示します。

1. リポジトリをクローンする
2. サブモジュールを更新する

```bash
$ git submodule init
$ git submodule update
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
```

4. Docker でコンテナを起動する

```bash
$ docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```
