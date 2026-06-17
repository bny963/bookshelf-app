# BookShelf 書籍レビューアプリ

## 概要
BookShelfは、読書家が自身の蔵書を管理し、レビューを投稿・共有するための書籍レビューアプリケーションです。
本プロジェクトは、Backendエンジニアを目指す過程で設計から実装までを行い、PHP/Laravelの習得および実戦的な開発スキルの向上を目的としています。

## ER図
<img width="1169" height="741" alt="Image" src="https://github.com/user-attachments/assets/22cae7ff-dc4d-4d8f-a78e-82e9d86fd965" />

## Laravel環境構築 (Sail)

#### 1. リポジトリをクローン
```bash
git clone -b basic https://github.com/bny963/bookshelf-app
```

#### 2. .env.example をコピーして .env を作成します。
```bash
cp .env.example .env
```

#### 3. Google Books API キーを設定します。
書籍情報の自動入力機能（ISBN検索）を使用するには、Google Books API キーが必要です。

1. [Google Cloud Console](https://console.cloud.google.com/) にアクセスし、プロジェクトを作成または選択します。
2. 「APIとサービス」→「ライブラリ」から **Books API** を有効化します。
3. 「APIとサービス」→「認証情報」から API キーを作成します。
4. `.env` の以下の項目に取得した API キーを設定します。

```env
GOOGLE_BOOKS_API_KEY=取得したAPIキー
```

> API キーを設定しない場合でも、ISBN検索機能以外は正常に動作します。

#### 4. composer install
```bash
./vendor/bin/sail composer install
```

#### 5. Laravel Sailを使用してコンテナを起動します。
```bash
./vendor/bin/sail up -d
```

#### 6. マイグレーションを実行します。
```bash
./vendor/bin/sail artisan migrate
```

#### 7. 初期データを投入します。
```bash
./vendor/bin/sail artisan db:seed
```

#### 8. フロントエンドをビルドして監視を開始します。
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```
## テスト環境用データベースのセットアップ

自動テスト（PHPUnit）を実行する前に、テスト専用のデータベースを手動で作成し、適切なアクセス権限を付与する必要があります。以下の手順に従って構築してください。

#### 1. MySQLコンテナへの接続
まず、Laravel Sailで起動しているMySQLコンテナ（rootユーザー）に接続します。

```bash
./vendor/bin/sail mysql -u root -p
```

#### 2. テスト
```bash
./vendor/bin/sail test
```

## 使用技術
言語/フレームワーク: PHP 8.5, Laravel 10

データベース: MySQL 8.0

コンテナ・環境構築: Docker, Laravel Sail

認証・認可: Laravel Fortify, Laravel Policies

テスト: PHPUnit, Pest (テストカバレッジ 80%以上達成)

外部API: Google Books API

フロントエンド連携: Blade (一部 API 連携)

データ管理/バリデーション: FormRequest, Eloquent ORM, Resources

その他ツール: Git/GitHub, Composer (パッケージ管理)

## 作成者
Ruma Kobayashi
 
## APIエンドポイント一覧

| メソッド | パス | 概要 |
| :--- | :--- | :--- |
| GET | /api/books | 書籍一覧の取得 |
| POST | /api/books | 書籍の新規登録 |
| GET | /api/books/{id} | 書籍詳細の取得 |
| PUT | /api/books/{id} | 書籍情報の更新 |
| DELETE | /api/books/{id} | 書籍の削除 |
| GET | /api/isbn-search | Google Books APIを利用した書籍検索 |

## 開発環境URL
アプリケーション: http://localhost
phpMyAdmin: http://localhost:8080/

