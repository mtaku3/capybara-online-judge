# コントリビューションガイド

ここではコントリビューションの際の一連の流れを説明します。

## Issues

Issue を作る際には下記のことに注意してください。

- 作成前に同様の内容の Issue がないかを必ず確認すること
- 質問やトラブルシューティングのために Issue を作成しないこと
  - Issue は機能追加リクエスト、提案、バグのトラッキングのみに使用されるべき
  - 質問がある場合は Discussions を利用してください

## 実装する前に

機能を追加、もしくはバグを修正したい場合は、**まず初めに Issue を作成してレビューを受けてください**

ここで、作成する PR の目的を忘れずに明示し、他のチームメンバーに不明瞭な部分がないか必ず確かめてください。
これを守られていない PR はレビューするのが非常に困難です。

また、実装を始める際は、自分自身を Issue にアサインしてください。（自分自身でアサインできない場合は、出来る人に頼んでください）

コミットメッセージは [Conventional Commits](https://www.conventionalcommits.org/ja/v1.0.0/) に従ってください。

## PR を作成する前に

PR を作成する前に下記のことを確認してください。

- 可能であれば、タイトルの前に PR の種類を示すプレフィックス（接頭辞）をつけてください

  - `fix` / `refactor` / `feat` / `style` / `doc` / `chore` など
  - また、PR の目的が混同していないか確認してください。

- PR によって解決されるであろう Issue があればリファレンスを記入してください
- もしドキュメントで変更が必要になる点があれば記入してください
- 変更したファイルがフォーマットされているか確認してください

  - devcontainer でセットアップした場合、フォーマッターはファイル保存時に自動的に実行されます

- UI の変更を含む場合は、スクリーンショットを添付してください

ご協力ありがとうございます。 🤗

## 開発

このプロジェクトは VS Code の devcontainer 機能に対応しています。

1. コントリビューションの際は、まずリポジトリをフォークしてください

   > 参考 : [フォークする方法](https://docs.github.com/ja/get-started/quickstart/fork-a-repo)

2. フォークしたリポジトリをローカルにクローンする

```bash
$ git clone https://github.com/<your-name>/capybara-online-judge.git
```

3. サブモジュールを更新する

```bash
$ git submodule init
$ git submodule update
```

4. .env.example ファイルを参考に .env ファイルを作成する

```
COMPOSE_PROJECT_NAME=capybara-online-judge-<your-name>

POSTGRES_HOST=localhost
POSTGRES_PORT=5432
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
POSTGRES_DB=dev

REDIS_HOST=localhost
REDIS_PORT=6379

JWT_SECRET=secret
```

5. VS Code で devcontainer を起動する
