<?php
// PHPMailerとGoogle Cloud Storageのクラスをインポート
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Core\Exception\GoogleException;

// 常にJSON形式で応答し、文字コードはUTF-8に設定
header('Content-Type: application/json; charset=utf-8');

// 全体的なエラーハンドリング
set_exception_handler(function ($exception) {
    error_log('Unhandled Exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine());
    http_response_code(500);
    echo json_encode([
        'error' => 'サーバーで予期せぬエラーが発生しました。' . $exception->getMessage()
    ]);
    exit;
});

// Composerでインストールしたライブラリを読み込む
require_once __DIR__ . '/vendor/autoload.php';

/**
 * 全店舗のプラン詳細情報を返す関数
 * register.phpのJavaScriptにあるplanDetailsオブジェクトと内容を一致させる。
 * @return array
 */
function getPlanDetails() {
    $longNotes = "
1. 本施設はセルフ利用を基本としております。<br>
2. 会費はクレジットカードからの自動引き落としとなります。<br>
3. 施設内での私物の盗難、紛失について一切の責任を負いかねます。<br>
4. 忘れ物の保管期間は1ヶ月とさせていただきます。<br>
5. 施設内は土足厳禁です。必ず室内用シューズをご持参ください。<br>
6. マシンのご利用後は、備え付けのタオルで清掃をお願いいたします。<br>
7. 大声での会話や長時間のマシンの占有はご遠慮ください。<br>
8. 体調が優れない場合は、施設の利用をお控えください。";

    return [
        'あびこ店' => [
            'クレジットプラン' => ['price' => '月額 8,800円', 'campaign' => '初月無料キャンペーン！<br>さらに事務手数料も0円！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '家族割クレジットプラン' => ['price' => '月額 7,700円', 'campaign' => '家族2人目以降ずっと割引！', 'cancellation' => '代表者の退会と同時に割引は終了します。', 'notes' => $longNotes]
        ],
        '東三国店' => [
            '誰でも割' => ['price' => '月額 7,500円', 'campaign' => '入会金0円！', 'cancellation' => '6ヶ月間の継続利用が条件となります。', 'notes' => $longNotes],
            '年割' => ['price' => '年払い 80,000円', 'campaign' => '1年分一括でお得！', 'cancellation' => '途中解約による返金はいたしかねます。', 'notes' => $longNotes],
            '乗り換え割' => ['price' => '月額 7,000円', 'campaign' => '他社からの乗り換えで半年間割引！', 'cancellation' => '6ヶ月間の継続利用が条件となります。', 'notes' => $longNotes],
            '一括プラン' => ['price' => '一括 150,000円', 'campaign' => '永久会員プラン！', 'cancellation' => 'ご本人様のみ有効です。譲渡・返金はできません。', 'notes' => $longNotes],
            'パーソナルプラン' => ['price' => '月額 20,000円', 'campaign' => '専属トレーナーがサポート！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 6,500円', 'campaign' => 'ペア入会で事務手数料も0円！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ],
        'イオンタウン松原店' => [
            '松原店プランA' => ['price' => '月額 8,000円', 'campaign' => 'オープニングキャンペーン中！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '松原店プランB' => ['price' => '月額 9,000円', 'campaign' => '全店舗利用可能！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '家族割クレジットプラン' => ['price' => '月額 7,000円', 'campaign' => 'ご家族でお得に！', 'cancellation' => '代表者の退会と同時に割引は終了します。', 'notes' => $longNotes]
        ],
        '尼崎店' => [
            '尼崎店プランA' => ['price' => '月額 7,800円', 'campaign' => 'Web入会限定価格！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '尼崎店プランB' => ['price' => '月額 8,800円', 'campaign' => '水素水サーバー無料！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 6,800円', 'campaign' => 'お友達と一緒に入会！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ],
        '古市店' => [
            '古市店プランA' => ['price' => '月額 7,800円', 'campaign' => '初月会費500円！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '古市店プランB' => ['price' => '月額 8,800円', 'campaign' => 'タオルレンタル無料！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 6,800円', 'campaign' => '2人で始めよう！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ],
        '藤井寺店' => [
            '藤井寺店プランA' => ['price' => '月額 7,900円', 'campaign' => 'セキュリティキー発行料0円！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '藤井寺店プランB' => ['price' => '月額 8,900円', 'campaign' => 'プロテインサーバー利用可！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '家族割クレジットプラン' => ['price' => '月額 6,900円', 'campaign' => '家族で健康に！', 'cancellation' => '代表者の退会と同時に割引は終了します。', 'notes' => $longNotes]
        ],
        '東大阪店' => [
            '東大阪店プランA' => ['price' => '月額 8,200円', 'campaign' => '事務手数料半額！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '東大阪店プランB' => ['price' => '月額 9,200円', 'campaign' => 'パーソナル3回無料！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 7,200円', 'campaign' => 'モチベーションアップ！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ],
        '兵庫店' => [
            '兵庫店プランA' => ['price' => '月額 8,100円', 'campaign' => '初月会費0円！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '兵庫店プランB' => ['price' => '月額 9,100円', 'campaign' => 'いつでもアップグレード可能！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 7,100円', 'campaign' => '2人ならもっと楽しい！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ],
        '平野店' => [
            '平野店プランA' => ['price' => '月額 7,700円', 'campaign' => '最初の2ヶ月間割引！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '平野店プランB' => ['price' => '月額 8,700円', 'campaign' => '全店の相互利用OK！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '家族割クレジットプラン' => ['price' => '月額 6,700円', 'campaign' => '家族みんなでフィットネス！', 'cancellation' => '代表者の退会と同時に割引は終了します。', 'notes' => $longNotes]
        ],
        '芦屋店' => [
            '芦屋店プランA' => ['price' => '月額 10,000円', 'campaign' => '高級アメニティ完備！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            '芦屋店プランB' => ['price' => '月額 12,000円', 'campaign' => 'プライベートロッカー付き！', 'cancellation' => '退会希望月の前月10日までにお手続きが必要です。', 'notes' => $longNotes],
            'ペア割プラン' => ['price' => 'お一人様 月額 9,000円', 'campaign' => 'ご夫婦・カップルでどうぞ！', 'cancellation' => 'ペアのどちらかが退会した場合、通常プランに移行します。', 'notes' => $longNotes]
        ]
    ];
}


/**
 * データベース接続を取得する関数
 *
 * @return PDO データベース接続オブジェクト
 * @throws Exception 接続に失敗した場合
 */
function getDbConnection()
{
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');
    $instanceConnectionName = getenv('DB_CONNECTION_NAME');

    if (!$dbName || !$dbUser || !$dbPass || !$instanceConnectionName) {
        throw new Exception("データベースの環境変数が設定されていません。");
    }

    $dsn = "mysql:dbname={$dbName};unix_socket=/cloudsql/{$instanceConnectionName}";

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8, time_zone = 'Asia/Tokyo'",
        ]);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("データベース接続に失敗しました: " . $e->getMessage());
    }
}

/**
 * 【お客様向け】確認メールを送信する関数
 * @param array $formData フォームから送信された全データ
 * @param array $planInfo プランに関する追加情報 (キャンペーン等)
 * @return bool
 */
function sendConfirmationEmail($formData, $planInfo)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP設定
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('_SMTP_USER');
        $mail->Password   = getenv('_SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // 送信元と宛先
        $mail->setFrom(getenv('_SMTP_USER'), 'クイックフィット24');
        $mail->addAddress($formData['email'], $formData['name']);

        // メールの内容
        $mail->isHTML(true);
        $mail->Subject = '【クイックフィット24】お申し込みありがとうございます（ご入会控え）';

        $termsUrl = 'https://drive.google.com/file/d/1UovHKQK71nLN_SWr4cBli8ePgg9OJO6S/view?usp=sharing';

        // メール本文を生成（セキュリティ対策として htmlspecialchars を使用）
        $body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #007bff; color: white; padding: 20px; text-align: center;'><h1 style='margin: 0; font-size: 24px;'>クイックフィット24</h1></div>
                <div style='padding: 20px 30px;'>
                    <h2 style='color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px;'>お申し込みありがとうございます</h2>
                    <p>".htmlspecialchars($formData['name'] ?? '')." 様</p>
                    <p>この度は、クイックフィット24にお申し込みいただき、誠にありがとうございます。<br>以下の内容でお申し込みを受け付けましたので、ご確認ください。</p>
                    
                    <h3 style='margin-top: 30px; color: #333;'>ご契約内容</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>入会店舗</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['store'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>プラン</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['plan'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>料金</th><td style='border: 1px solid #ddd; padding: 12px;'>".($planInfo['price'] ?? '店舗にてご確認ください')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>適用キャンペーン</th><td style='border: 1px solid #ddd; padding: 12px;'>".($planInfo['campaign'] ?? 'なし')."</td></tr>
                    </table>

                    <h3 style='margin-top: 30px; color: #333;'>ご本人様情報</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>お名前</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['name'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>フリガナ</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['furigana'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>性別</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['gender'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>生年月日</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['dob'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>メールアドレス</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['email'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>電話番号</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['tel'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>住所</th><td style='border: 1px solid #ddd; padding: 12px;'>".nl2br(htmlspecialchars($formData['address'] ?? ''))."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>緊急連絡先氏名</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['emergency_contact_name'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>緊急連絡先電話番号</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['emergency_contact_phone'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>知った経緯</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['channel'] ?? '')."</td></tr>
                    </table>";

        if (!empty($formData['pair_name'])) {
            $body .= "
                    <h3 style='margin-top: 30px; color: #333;'>ペア会員様情報</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>お名前</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['pair_name'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>フリガナ</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['pair_furigana'] ?? '')."</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>性別</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['pair_gender'] ?? '')."</td></tr>
                        <tr><th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>生年月日</th><td style='border: 1px solid #ddd; padding: 12px;'>".htmlspecialchars($formData['pair_dob'] ?? '')."</td></tr>
                    </table>";
        }

        $body .= "
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <h3 style='margin-top: 30px; color: #333;'>同意事項</h3>
                    <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; font-size: 14px; margin-bottom: 20px;'>
                        <h4 style='margin-top:0;'>解約について</h4>
                        <p>".($planInfo['cancellation'] ?? '店舗規定に準じます。')."</p>
                        <h4 style='margin-top:15px;'>注意事項</h4>
                        <div>".($planInfo['notes'] ?? '店舗規定に準じます。')."</div>
                    </div>
                    <p style='font-size: 14px;'>ご入会にあたり、以下の会員規約の全文も適用されます。<br>必ずご確認いただきますようお願い申し上げます。</p>
                    <p style='text-align: center; margin-top: 20px;'><a href='{$termsUrl}' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>会員規約(全文)を確認する</a></p>
                </div>
                <div style='background-color: #f4f6f8; padding: 20px; text-align: center; font-size: 12px; color: #666;'><p>クイックフィット24<br>info@quickfit24.jp</p></div>
            </div>";

        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log("Customer mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * 【管理者向け】通知メールを送信する関数
 * @param array $formData フォームから送信された全データ
 * @param array $planInfo プランに関する追加情報 (キャンペーン等)
 * @return bool
 */
function sendAdminNotificationEmail($formData, $planInfo)
{
    $mail = new PHPMailer(true);
    $adminEmail = getenv('_ADMIN_EMAIL');
    if (empty($adminEmail)) {
        error_log("Admin email is not set.");
        return false;
    }
    try {
        // SMTP設定
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('_SMTP_USER');
        $mail->Password   = getenv('_SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // 送信元と宛先
        $mail->setFrom(getenv('_SMTP_USER'), '入会フォーム通知');
        $mail->addAddress($adminEmail, '管理者');

        // メールの内容
        $mail->isHTML(true);
        $mail->Subject = '【新規入会通知】' . ($formData['name'] ?? '不明') . ' 様からのお申し込み';
        
        $body = "
            <div style='font-family: sans-serif; padding: 20px; background-color: #f9f9f9;'>
                <div style='max-width: 600px; margin: auto; background-color: white; border: 1px solid #ddd; padding: 30px;'>
                    <h2 style='color: #d9534f;'>新規入会通知</h2>
                    <p>Webサイトの入会フォームから、新しいお申し込みがありました。データベースへの自動記入、およびGCSへのファイルアップロードは完了しています。</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>

                    <h3 style='margin-bottom: 10px; color: #333; border-left: 4px solid #d9534f; padding-left: 10px;'>契約プラン情報</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>入会店舗</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['store'] ?? '') . "</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>プラン</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['plan'] ?? '') . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>料金</th><td style='padding: 10px; border: 1px solid #ddd;'>" . ($planInfo['price'] ?? 'N/A') . "</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>適用キャンペーン</th><td style='padding: 10px; border: 1px solid #ddd;'>" . ($planInfo['campaign'] ?? 'なし') . "</td></tr>
                    </table>

                    <h3 style='margin-bottom: 10px; color: #333; border-left: 4px solid #d9534f; padding-left: 10px;'>ご本人様情報</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>氏名 (フリガナ)</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['name'] ?? '') . " (" . htmlspecialchars($formData['furigana'] ?? '') . ")</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>性別</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['gender'] ?? '') . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>生年月日</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['dob'] ?? '') . "</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>メールアドレス</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['email'] ?? '') . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>電話番号</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['tel'] ?? '') . "</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>住所</th><td style='padding: 10px; border: 1px solid #ddd;'>" . nl2br(htmlspecialchars($formData['address'] ?? '')) . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>緊急連絡先氏名</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['emergency_contact_name'] ?? '') . "</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>緊急連絡先電話番号</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['emergency_contact_phone'] ?? '') . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>知った経緯</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['channel'] ?? '') . "</td></tr>
                    </table>";

        if (!empty($formData['pair_name'])) {
            $body .= "
                    <h3 style='margin-bottom: 10px; color: #333; border-left: 4px solid #5cb85c; padding-left: 10px;'>ペア会員様情報</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>氏名 (フリガナ)</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['pair_name'] ?? '') . " (" . htmlspecialchars($formData['pair_furigana'] ?? '') . ")</td></tr>
                        <tr><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>性別</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['pair_gender'] ?? '') . "</td></tr>
                        <tr style='background-color: #f8f9fa;'><th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>生年月日</th><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($formData['pair_dob'] ?? '') . "</td></tr>
                    </table>";
        }

        $body .= "
                </div>
            </div>";
        
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        error_log("Admin notification could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * ファイルをGoogle Cloud Storageにアップロードする関数
 *
 * @param array $file $_FILES superglobalの要素 (例: $_FILES['face_photo'])
 * @param string $bucketName バケット名
 * @return string|null アップロードされたファイルの公開URL、または失敗した場合はnull
 * @throws Exception ファイルのアップロードに失敗した場合
 */
function uploadToGCS($file, $bucketName)
{
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        // ファイルがない、またはアップロードエラーがある場合は何もせずnullを返す
        return null;
    }

    try {
        $storage = new StorageClient([
            // Cloud Runのサービスアカウントが自動で使われるため、キーファイル等の指定は不要
        ]);
        $bucket = $storage->bucket($bucketName);

        // ファイル名が衝突しないようにユニークな名前を生成
        // 例: 20250628_175500_667e9b8c12345_original_filename.jpg
        $fileName = date('Ymd_His') . '_' . uniqid() . '_' . basename($file['name']);
        
        // ファイルをストリームとしてアップロード
        $object = $bucket->upload(
            fopen($file['tmp_name'], 'r'),
            ['name' => $fileName]
        );

        // アップロードしたファイルの署名付きURL（一時的なアクセスURL）ではなく、
        // 永続的なオブジェクトのURL（mediaLink）を返すことで、後からでもアクセス可能にする
        // ただし、バケットが非公開の場合、このURLに直接アクセスはできないため、
        // 管理画面で表示する際は署名付きURLを都度生成する方がセキュアです。
        // 今回はシンプルにmediaLinkを保存します。
        return $object->info()['mediaLink'];

    } catch (GoogleException $e) {
        // GCS関連のエラーが発生した場合、それを捕捉して詳細なエラーメッセージを投げる
        throw new Exception("GCSへのファイルアップロードに失敗しました: " . $e->getMessage());
    }
}


// --- メインロジック ---
try {
    $pdo = getDbConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    
    // ★★★ 修正点1: データベースのカラムと完全に一致させる ★★★
    $db_column_map = [
        "id" => "id", "store" => "契約店舗", "member_id" => "会員番号", "name" => "氏名", 
        "furigana" => "フリガナ", "email" => "メールアドレス", "plan" => "プラン", "gender" => "性別", 
        "dob" => "生年月日", "age" => "年齢", "tel" => "TEL No.", "emergency_contact_phone" => "緊急連絡先", 
        "emergency_contact_name" => "緊急連絡先氏名", "address" => "住所", "channel" => "流入経路", 
        "registration_date" => "入会日", "possible_withdrawal_date" => "退会可能日", "withdrawal_date" => "退会日", 
        "final_debit_date" => "最終引落", "pair_member_id" => "ペア会員番号", "pair_name" => "ペア氏名", 
        "pair_furigana" => "ペアふりがな", "pair_gender" => "ペア性別", "pair_dob" => "ペア生年月日", 
        "pair_age" => "ペア年齢", "total_enrollment_period" => "累計在籍", "pin_code" => "PIN", 
        "memo" => "その他", "mutual_use" => "相互利用", "col_a_blank" => "空白",
        "face_photo_url" => "顔写真URL", "id_doc_url" => "身分証URL", "transfer_doc_url" => "乗換書類URL",
        "pair_face_photo_url" => "ペア顔写真URL", "pair_id_doc_url" => "ペア身分証URL",
        "created_at" => "作成日時", "updated_at" => "更新日時" // created_at と updated_at を追加
    ];
    
    // JS側で使うためのキー配列と表示名配列
    $db_columns_ordered = array_keys($db_column_map);
    $response_headers_ordered = array_values($db_column_map);

    switch ($method) {
        case 'GET':
            $sql = "SELECT * FROM members ORDER BY id ASC";
            $stmt = $pdo->query($sql);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $gcsBucketName = getenv('GCS_BUCKET_NAME');
            if ($gcsBucketName) {
                $storage = new StorageClient();
                $bucket = $storage->bucket($gcsBucketName);
                $url_columns = ['face_photo_url', 'id_doc_url', 'transfer_doc_url', 'pair_face_photo_url', 'pair_id_doc_url'];

                foreach ($members as &$member) {
                    foreach ($url_columns as $col) {
                        if (!empty($member[$col])) {
                            try {
                                // ★★★ URLからGCSのオブジェクトパスを正確に抽出 ★★★
                                $path_parts = explode('/o/', $member[$col]);
                                if (count($path_parts) > 1) {
                                    $encoded_path = explode('?', $path_parts[1])[0];
                                    $gcsPath = urldecode($encoded_path);

                                    $object = $bucket->object($gcsPath);
                                    if ($object->exists()) {
                                        $signedUrl = $object->signedUrl(new \DateTime('+15 minutes'));
                                        $member[$col] = $signedUrl;
                                    } else {
                                        $member[$col] = ''; // GCSにオブジェクトが存在しない
                                    }
                                } else {
                                     $member[$col] = ''; // URLの形式が不正
                                }
                            } catch (Exception $e) {
                                error_log("Failed to create signed URL for '{$member[$col]}': " . $e->getMessage());
                                $member[$col] = ''; 
                            }
                        }
                    }
                }
                unset($member);
            }

            echo json_encode([
                'headers' => $db_columns_ordered, 
                'displayHeaders' => $response_headers_ordered,
                'data' => $members
            ]);
            break;


        case 'POST':
            
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            // ファイルアップロードを伴うリクエスト（一般登録 or 管理者作成）
            if (strpos($contentType, 'multipart/form-data') !== false) {

                // --- 1. GCSへのアップロード（共通処理） ---
                $gcsBucketName = getenv('GCS_BUCKET_NAME');
                if (!$gcsBucketName) throw new Exception("GCS_BUCKET_NAME 環境変数が設定されていません。");
                
                $fileUrls = [
                    'face_photo_url' => uploadToGCS($_FILES['face_photo'] ?? null, $gcsBucketName),
                    'id_doc_url' => uploadToGCS($_FILES['id_doc'] ?? null, $gcsBucketName),
                    'transfer_doc_url' => uploadToGCS($_FILES['transfer_doc'] ?? null, $gcsBucketName),
                    'pair_face_photo_url' => uploadToGCS($_FILES['pair_face_photo'] ?? null, $gcsBucketName),
                    'pair_id_doc_url' => uploadToGCS($_FILES['pair_id_doc'] ?? null, $gcsBucketName),
                ];

                // --- 2. DBへの保存処理（カラム名を明示的に指定する安定した方式） ---
                $sql = "INSERT INTO members (
                            store, name, furigana, email, plan, gender, dob, tel, 
                            emergency_contact_phone, emergency_contact_name, address, channel, 
                            registration_date, 
                            pair_member_id, pair_name, pair_furigana, pair_gender, pair_dob,
                            face_photo_url, id_doc_url, transfer_doc_url, pair_face_photo_url, pair_id_doc_url
                        ) VALUES (
                            :store, :name, :furigana, :email, :plan, :gender, :dob, :tel, 
                            :emergency_contact_phone, :emergency_contact_name, :address, :channel, 
                            NOW(), 
                            :pair_member_id, :pair_name, :pair_furigana, :pair_gender, :pair_dob,
                            :face_photo_url, :id_doc_url, :transfer_doc_url, :pair_face_photo_url, :pair_id_doc_url
                        )";
                
                $stmt = $pdo->prepare($sql);
                
                // POSTされてきた値をパラメータとして設定
                $params = [
                    ':store' => $_POST['store'] ?? null,
                    ':name' => $_POST['name'] ?? null,
                    ':furigana' => $_POST['furigana'] ?? null,
                    ':email' => $_POST['email'] ?? null,
                    ':plan' => $_POST['plan'] ?? null,
                    ':gender' => $_POST['gender'] ?? null,
                    ':dob' => empty($_POST['dob']) ? null : $_POST['dob'],
                    ':tel' => $_POST['tel'] ?? null,
                    ':emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? null,
                    ':emergency_contact_name' => $_POST['emergency_contact_name'] ?? null,
                    ':address' => $_POST['address'] ?? null,
                    ':channel' => $_POST['channel'] ?? null,
                    ':pair_member_id' => $_POST['pair_member_id'] ?? null,
                    ':pair_name' => $_POST['pair_name'] ?? null,
                    ':pair_furigana' => $_POST['pair_furigana'] ?? null,
                    ':pair_gender' => $_POST['pair_gender'] ?? null,
                    ':pair_dob' => empty($_POST['pair_dob']) ? null : $_POST['pair_dob'],
                    // GCSから返されたURLをパラメータに設定
                    ':face_photo_url' => $fileUrls['face_photo_url'],
                    ':id_doc_url' => $fileUrls['id_doc_url'],
                    ':transfer_doc_url' => $fileUrls['transfer_doc_url'],
                    ':pair_face_photo_url' => $fileUrls['pair_face_photo_url'],
                    ':pair_id_doc_url' => $fileUrls['pair_id_doc_url']
                ];

                $stmt->execute($params);


                // --- 3. メール送信判定（一般ユーザーからの登録のみ） ---
                if (!isset($_POST['source']) || $_POST['source'] !== 'admin') {
                    $allPlanDetails = getPlanDetails();
                    $store = $_POST['store'] ?? '';
                    $plan = $_POST['plan'] ?? '';
                    $planInfo = $allPlanDetails[$store][$plan] ?? ['price' => 'N/A', 'campaign' => 'N/A', 'cancellation' => 'N/A', 'notes' => 'N/A'];
                    
                    sendConfirmationEmail($_POST, $planInfo);
                    sendAdminNotificationEmail($_POST, $planInfo);
                }

                // --- 4. 成功レスポンスを返す ---
                echo json_encode(['success' => true]);

            } else {
                // JSON形式でのPOSTは現時点では使用しない想定
                throw new Exception("Unsupported Content-Type or data format for POST.");
            }
            break;

        case 'PUT':
            // 管理画面から顧客情報を更新
            $data = json_decode(file_get_contents('php://input'), true);
            $rowId = $data['id'];
            $values = $data['values'];
            
            $updateData = [];
            foreach($db_columns_ordered as $index => $key) {
                if(isset($values[$index])) {
                    $updateData[$key] = $values[$index];
                }
            }

            $setClauses = [];
            $paramsToExecute = [];

            foreach ($updateData as $key => $value) {
                // idは更新対象外
                if ($key !== 'id') {
                    $setClauses[] = "`{$key}` = :{$key}";
                    // 空文字やnullはDBのNULLとして扱う
                    $paramsToExecute[$key] = ($value === '' || is_null($value)) ? null : $value;
                }
            }

            if (empty($setClauses)) {
                throw new Exception("更新するデータがありません。");
            }

            $sql = sprintf("UPDATE members SET %s WHERE id = :id", implode(', ', $setClauses));
            $stmt = $pdo->prepare($sql);

            // WHERE句のidをパラメータに追加
            $paramsToExecute['id'] = $rowId;

            $stmt->execute($paramsToExecute);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'データベースエラーが発生しました。']);
} catch (PHPMailerException $e) {
    http_response_code(500);
    error_log('Mailer Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'メールの送信に失敗しました。']);
} catch (Exception $e) {
    // GCSアップロード失敗など、その他のカスタム例外をここで捕捉
    http_response_code(500);
    error_log('API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
