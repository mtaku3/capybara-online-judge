# 開発ガイド

## 開発環境の構築

このプロジェクトは VS Code の devcontainer 機能に対応しています。

1. コントリビューションの際は、まずリポジトリをフォークしてください

   > 参考 : [フォークする方法](https://docs.github.com/ja/get-started/quickstart/fork-a-repo)

2. フォークしたリポジトリをローカルにクローンする

```bash
$ git clone https://github.com/<your-name>/capybara-online-judge.git
```

3. サブモジュールを更新する

```bash
$ git submodule update --init
```

4. .env.example ファイルを参考に .env ファイルを作成する

```
POSTGRES_HOST=localhost
POSTGRES_PORT=5432
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
POSTGRES_DB=dev

REDIS_HOST=localhost
REDIS_PORT=6379

JWT_SECRET=secret
```

5. プログラム実行用のベースラインをビルドする

```bash
$ docker run --rm -v /var/run/docker.sock:/var/run/docker.sock -v .:/workspace -w /workspace php:8-bullseye php tools/BuildImage.php
```

6. VS Code で devcontainer を起動する

## 実行可能な言語の追加

実行可能な言語の追加には下記の変更が必要です。
ここでは`PHP`を追加する例を示します。

1. `src/App/Domain/Common/ValueObject/Language.php` に追記する

```php
enum Language: string
{
    case C = "C (GCC 13.1.0)";
    case CPP = "C++ (GCC 13.1.0)";
    case Python = "Python (3.10.12)";
    case PHP = "PHP (8.2.8)";

    ...

}
```

2. `src/App/Infrastructure/Repository/File/FileRepository.php` にデフォルトのファイル名を追記する

```php
private static function RetrievePreferedFileNameFromLanguage(Language $language): string
{
    switch ($language) {
        case Language::C:
            return "main.c";
            break;
        case Language::CPP:
            return "main.cpp";
            break;
        case Language::Python:
            return "main.py";
            break;
        case Language::PHP:
            return "main.php";
            break;
    }
}
```

3. `docker/postgres/create-tables.sql` の Language タイプを変更する

```sql
CREATE TYPE
    Language AS ENUM('C', 'CPP', 'Python', 'PHP');
```

4. `migrations/` フォルダにマイグレーション用の SQL を作成する

```sql
-- Adding PHP to Languages
ALTER TYPE Language ADD VALUE 'PHP';
```
