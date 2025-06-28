<?php
// セッションを開始
session_start();

// 環境変数から認証情報を取得
$correct_username = getenv('ADMIN_USERNAME');
$raw_hashed_password = getenv('ADMIN_PASSWORD_HASH'); // 元のハッシュ値を取得


// Cloud Buildの仕様で先頭が '$$' になっている場合、正しい '$' 1つの形式に補正する
if (is_string($raw_hashed_password) && str_starts_with($raw_hashed_password, '$$')) {
    $hashed_password = substr($raw_hashed_password, 1);
} else {
    $hashed_password = $raw_hashed_password;
}


// 補正後の値で空かどうかをチェック
if (empty($hashed_password)) {
    die('エラー: パスワードの環境変数が設定されていません。Cloud Runの環境変数を確認してください。');
}

$error_message = '';

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ユーザー名が一致し、かつパスワードが補正後のハッシュと一致するか検証
    if ($username === $correct_username && password_verify($password, $hashed_password)) {
        // 認証成功
        $_SESSION['user_logged_in'] = true;
        header('Location: index.php'); // 管理画面へリダイレクト
        exit;
    } else {
        // 認証失敗
        $error_message = 'ユーザー名またはパスワードが正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - 顧客管理システム</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f6f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 360px; }
        h1 { text-align: center; color: #007bff; margin-top: 0; margin-bottom: 25px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: background-color 0.2s; }
        button:hover { background-color: #0056b3; }
        .error { color: #dc3545; text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>管理画面ログイン</h1>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">ユーザー名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">ログイン</button>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
