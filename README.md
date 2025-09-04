# フィットネスジム 顧客管理システム

このプロジェクトは、フィットネスジムの顧客管理システムです。Google Cloud Runで動作し、新規入会の受付から既存顧客の管理まで一元的に行うことができます。

## 🏗️ アーキテクチャ

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   ユーザー       │    │   管理者         │    │  Google Cloud   │
│                 │    │                 │    │                 │
│ register.php    │    │ index.php       │    │ Cloud Run       │
│ (入会フォーム)  　│    │ (管理画面)        │    │ Cloud SQL       │
│                 │    │ login.php       │    │ Cloud Storage   │
└─────────────────┘    └─────────────────┘    │ Cloud Build     │
          │                        │           └─────────────────┘
          │                        │                     │
          └────────────────────────┼─────────────────────┘
                                   │
                           ┌─────────────────┐
                           │    api.php      │
                           │  (REST API)     │
                           └─────────────────┘
```

## 🚀 主な機能

### 顧客向け機能
- **新規入会フォーム** (`register.php`)
  - 2ステップの入会プロセス
  - リアルタイムプラン情報表示
  - ファイルアップロード（顔写真、身分証等）
  - 自動メール送信（確認メール）

### 管理者向け機能
- **顧客管理システム** (`index.php`)
  - 顧客一覧表示・検索・フィルタリング
  - 顧客情報の詳細表示・編集
  - 新規顧客の手動登録
  - ページネーション対応
  - レスポンシブデザイン

### 共通機能
- **認証システム** (`login.php`, `logout.php`)
- **RESTful API** (`api.php`)
- **ファイル管理** (Google Cloud Storage連携)
- **データベース管理** (Google Cloud SQL連携)

## 🛠️ 技術スタック

### フロントエンド
- **HTML5** / **CSS3** / **JavaScript (ES6+)**
- **レスポンシブデザイン** (モバイル対応)
- **モダンUI/UX** (モーダル、アニメーション)

### バックエンド
- **PHP 8.2**
- **Apache HTTP Server**
- **Composer** (依存関係管理)

### データベース
- **MySQL** (Google Cloud SQL)
- **PDO** (データベース接続)

### インフラ
- **Google Cloud Run** (コンテナホスティング)
- **Google Cloud Storage** (ファイルストレージ)
- **Google Cloud Build** (CI/CD)
- **Docker** (コンテナ化)

### 外部サービス
- **PHPMailer** (メール送信)
- **Gmail SMTP** (メール配信)

## 📋 前提条件

### Google Cloudプロジェクト
- Google Cloud プロジェクトの作成
- 以下のAPIの有効化：
  - Cloud Run API
  - Cloud SQL API
  - Cloud Storage API
  - Cloud Build API

### データベーステーブル
MySQLに以下のテーブルが必要です：

```sql
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store VARCHAR(100),
    member_id VARCHAR(50),
    name VARCHAR(100),
    furigana VARCHAR(100),
    email VARCHAR(255),
    plan VARCHAR(100),
    gender VARCHAR(20),
    dob DATE,
    age INT,
    tel VARCHAR(20),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_name VARCHAR(100),
    address TEXT,
    channel VARCHAR(100),
    registration_date DATE,
    possible_withdrawal_date DATE,
    withdrawal_date DATE,
    final_debit_date DATE,
    pair_member_id VARCHAR(50),
    pair_name VARCHAR(100),
    pair_furigana VARCHAR(100),
    pair_gender VARCHAR(20),
    pair_dob DATE,
    pair_age INT,
    total_enrollment_period VARCHAR(50),
    pin_code VARCHAR(10),
    memo TEXT,
    mutual_use VARCHAR(50),
    col_a_blank VARCHAR(50),
    face_photo_url TEXT,
    id_doc_url TEXT,
    transfer_doc_url TEXT,
    pair_face_photo_url TEXT,
    pair_id_doc_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## ⚙️ 環境変数設定

Cloud Runで以下の環境変数を設定してください：

```bash
# データベース接続
DB_NAME=your_database_name
DB_USER=your_db_user
DB_PASS=your_db_password
DB_CONNECTION_NAME=your_cloud_sql_connection_name

# メール設定
_SMTP_USER=your_gmail_address@gmail.com
_SMTP_PASSWORD=your_gmail_app_password
_ADMIN_EMAIL=admin@example.com

# 認証設定
ADMIN_USERNAME=admin_user_name
ADMIN_PASSWORD_HASH=$2y$10$your_bcrypt_hashed_password

# ストレージ設定
GCS_BUCKET_NAME=your_gcs_bucket_name
```

### パスワードハッシュの生成方法

```php
<?php
echo password_hash('your_plain_password', PASSWORD_DEFAULT);
?>
```

## 🚀 デプロイ手順

### 1. リポジトリのクローン
```bash
git clone <repository-url>
cd customer-management-app
```

### 2. Google Cloud設定
```bash
# Google Cloud CLIの認証
gcloud auth login
gcloud config set project YOUR_PROJECT_ID

# Cloud Buildの設定
gcloud builds submit --config cloudbuild.yaml \
  --substitutions=_REGION=asia-northeast1,_REPO_NAME=your-repo,_SERVICE_NAME=customer-management,_DB_NAME=your_db,_DB_USER=your_user,_DB_PASS=your_pass,_DB_CONNECTION_NAME=your_connection,_SMTP_USER=your_email,_SMTP_PASSWORD=your_password,_ADMIN_EMAIL=admin@example.com,_ADMIN_USERNAME=admin,_ADMIN_PASSWORD_HASH=your_hash,_GCS_BUCKET_NAME=your_bucket
```

### 3. Cloud SQL設定
```bash
# Cloud SQLインスタンスの作成
gcloud sql instances create your-instance-name \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region=asia-northeast1

# データベースの作成
gcloud sql databases create your_database_name \
  --instance=your-instance-name

# ユーザーの作成
gcloud sql users create your_user \
  --instance=your-instance-name \
  --password=your_password
```

### 4. Cloud Storageバケット作成
```bash
gsutil mb gs://your-bucket-name
gsutil iam ch serviceAccount:your-service-account@your-project.iam.gserviceaccount.com:objectAdmin gs://your-bucket-name
```

## 📁 ファイル構成

```
顧客管理app/
├── Dockerfile                 # Dockerコンテナ設定
├── apache-config.conf         # Apache仮想ホスト設定
├── entrypoint.sh             # コンテナエントリーポイント
├── composer.json             # PHP依存関係定義
├── api.php                   # RESTful APIエンドポイント
├── index.php                 # 管理画面（メイン）
├── login.php                 # ログイン画面
├── logout.php                # ログアウト処理
├── register.php              # 新規入会フォーム
├── debug.php                 # デバッグ用（本番では削除）
└── cloudbuild.yaml           # Cloud Build設定
```

## 🎯 使用方法

### 新規入会フォーム (`register.php`)

1. **ステップ1: 基本情報入力**
   - 店舗選択
   - プラン選択（店舗に応じて動的更新）
   - 本人情報入力
   - ペア情報入力（ペアプランの場合）

2. **ステップ2: 確認と書類提出**
   - 入力内容の確認
   - プラン詳細・キャンペーン情報表示
   - 必要書類のアップロード
   - 同意確認

3. **ステップ3: 完了**
   - 確認メール自動送信
   - 管理者通知メール送信

### 管理画面 (`index.php`)

1. **ログイン**
   - 環境変数で設定した認証情報を使用

2. **顧客一覧**
   - 検索・フィルタリング機能
   - ソート機能（名前、会員番号、入会日等）
   - ページネーション

3. **顧客詳細・編集**
   - 詳細情報の表示
   - インライン編集機能
   - 身分証等画像の表示

4. **新規顧客登録**
   - 管理者による手動登録
   - ファイルアップロード対応

## 🏪 店舗・プラン情報

### 対応店舗
- あびこ店
- 東三国店
- イオンタウン松原店
- 尼崎店
- 古市店
- 藤井寺店
- 東大阪店
- 兵庫店
- 平野店
- 芦屋店

### プラン種別
各店舗で異なるプランを提供：
- クレジットプラン
- 家族割クレジットプラン
- ペア割プラン
- 誰でも割
- 年割
- 乗り換え割
- 一括プラン
- パーソナルプラン
- など

## 🔒 セキュリティ機能

- **認証システム**: セッションベースの管理者認証
- **パスワードハッシュ化**: bcryptによる安全なパスワード保存
- **SQL インジェクション対策**: PDOプリペアドステートメント使用
- **XSS対策**: htmlspecialchars()による出力エスケープ
- **ファイルアップロード制限**: 許可された形式のみ受け入れ
- **CSRF対策**: 適切なフォーム処理

## 📱 レスポンシブデザイン

- **モバイルファースト**: スマートフォンでの利用を考慮
- **タブレット対応**: 中間サイズでも快適な操作
- **デスクトップ最適化**: 大画面での効率的な作業

## 🚨 トラブルシューティング

### よくある問題

1. **データベース接続エラー**
   ```
   解決策: DB_CONNECTION_NAME環境変数を確認
   形式: project:region:instance-name
   ```

2. **ファイルアップロードエラー**
   ```
   解決策: GCSバケットのアクセス権限を確認
   サービスアカウントにStorage Object Adminロールが必要
   ```

3. **メール送信エラー**
   ```
   解決策: Gmailアプリパスワードの生成
   2段階認証有効化後にアプリパスワードを生成
   ```

4. **環境変数が読み込まれない**
   ```
   解決策: debug.phpで環境変数を確認
   Cloud Runサービスの環境変数設定を再確認
   ```

### ログの確認方法

```bash
# Cloud Runのログを確認
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=customer-management" --limit=50

# リアルタイムログの監視
gcloud logging tail "resource.type=cloud_run_revision AND resource.labels.service_name=customer-management"
```

## 🔄 データバックアップ

### 定期バックアップの設定

```bash
# Cloud SQLの自動バックアップ有効化
gcloud sql instances patch your-instance-name \
  --backup-start-time=02:00

# 手動バックアップの実行
gcloud sql backups create \
  --instance=your-instance-name \
  --description="Manual backup $(date)"
```

## 📊 監視・メトリクス

### Cloud Runメトリクス
- リクエスト数
- レスポンス時間
- エラー率
- CPU・メモリ使用率

### Cloud SQLメトリクス
- 接続数
- クエリ実行時間
- ストレージ使用量

## 🔧 開発・カスタマイズ

### ローカル開発環境

```bash
# Composerの依存関係をインストール
composer install

# ローカル開発サーバー（PHP内蔵サーバー）
php -S localhost:8000

# Dockerでの開発
docker build -t customer-management .
docker run -p 8080:8080 customer-management
```

### 新店舗・プランの追加

1. `register.php`の`plansByStore`オブジェクトに追加
2. `api.php`の`getPlanDetails()`関数に詳細情報を追加
3. `index.php`の該当配列を更新

### カスタムフィールドの追加

1. データベーステーブルにカラム追加
2. `api.php`の`$db_column_map`配列を更新
3. フロントエンド（`index.php`, `register.php`）のフォーム要素を追加

## 📝 ライセンス

このプロジェクトは、商用利用についても自由にお使いください。

## 🤝 サポート

技術的な問題や機能要望については、開発者までお問い合わせください。

---

**Version**: 1.0.0  
**Last Updated**: 2025年6月  
**Maintained by**: taichan-33
