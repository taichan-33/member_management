#!/bin/sh
set -e

# Apacheがリクエストを待ち受けるポートを変更
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf

# Apacheがどのポートで来た顧客を接客するかの設定も変更
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Apacheを起動
apache2-foreground
