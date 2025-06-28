<?php
// このページはデバッグ専用です。問題解決後は必ず削除してください。

echo "<h1>環境変数デバッグ情報</h1>";
echo "<p>このページには、Cloud Runコンテナが認識している環境変数が表示されます。</p>";

echo "<h2>getenv() で取得した一覧:</h2>";
echo "<pre style='background-color: #f0f0f0; padding: 15px; border: 1px solid #ccc; white-space: pre-wrap; word-wrap: break-word;'>";
print_r(getenv());
echo "</pre>";

echo "<h2>\$_SERVER スーパーグローバル変数の一覧:</h2>";
echo "<pre style='background-color: #f0f0f0; padding: 15px; border: 1px solid #ccc; white-space: pre-wrap; word-wrap: break-word;'>";
print_r($_SERVER);
echo "</pre>";
?>
