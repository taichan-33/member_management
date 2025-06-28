# ベースイメージとして公式のPHP + Apacheイメージを使用
FROM php:8.2-apache

# ★★★ 修正点1: PDOとMySQL用ドライバをインストール ★★★
# アプリケーションがデータベース接続に必要とするPHP拡張機能をインストール
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# デフォルトのApache設定を、我々が作成した許可設定の多いファイルで上書きする
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# 作業ディレクトリを設定
WORKDIR /var/www/html

# ★★★ 修正点2: ビルドキャッシュ効率化のため、composer.jsonを先にコピー ★★★
# 依存関係の定義ファイルのみを先にコピーすることで、
# これらのファイルに変更がない限り、次の`composer install`のステップはキャッシュが利用される
COPY composer.json composer.lock* ./

# ★★★ 修正点3: 本番環境向けのオプションを追加 ★★★
# --no-dev: 開発用の依存関係はインストールしない
# --optimize-autoloader: パフォーマンス向上のためオートローダーを最適化
RUN composer install --no-interaction --no-dev --optimize-autoloader

# ★★★ 修正点4: アプリケーションのソースコード全体をコピー ★★★
# 依存関係のインストール後にソースコードをコピーする
COPY . .

# ディレクトリの所有者をApacheの実行ユーザー(www-data)に変更
RUN chown -R www-data:www-data /var/www/html

# entrypointスクリプトをコピーし、実行権限を付与
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# コンテナ起動時にentrypoint.shを実行するよう設定
ENTRYPOINT ["entrypoint.sh"]
