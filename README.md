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

#### 3. Laravel Sailを使用してコンテナを起動します。
```bash
./vendor/bin/sail up -d
```

#### 4. マイグレーションを実行します。
```bash
./vendor/bin/sail artisan migrate
```

#### 5. 初期データを投入します。
```bash
./vendor/bin/sail artisan db:seed
```

#### 6. フロントエンドをビルドして監視を開始します。
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

## 使用技術
言語/フレームワーク: PHP 8.2, Laravel 10

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

