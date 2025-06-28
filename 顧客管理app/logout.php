<?php
// セッションを開始
session_start();

// セッション変数を全て解除
$_SESSION = [];

// セッションを破壊
session_destroy();

// ログインページへリダイレクト
header('Location: login.php');
exit;
?>
