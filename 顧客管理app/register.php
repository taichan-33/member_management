<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規入会フォーム</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f6f8; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 20px 0; }
        .form-container { background-color: white; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; box-sizing: border-box; }
        h1, h2 { text-align: center; color: #007bff; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        button { width: 100%; padding: 12px; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: background-color 0.2s; margin-top: 10px; }
        #next-btn, #register-btn { background-color: #28a745; }
        #next-btn:hover, #register-btn:hover { background-color: #218838; }
        #back-btn { background-color: #6c757d; }
        #back-btn:hover { background-color: #5a6268; }
        button:disabled { background-color: #adb5bd; cursor: not-allowed; }
        
        #form-step-2 .summary-box { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin: 20px 0; }
        #form-step-2 .summary-box p { margin: 12px 0; font-size: 16px; }
        #form-step-2 .summary-box strong { color: #007bff; display: block; margin-bottom: 5px; }
        #form-step-2 .notes-box { background-color: #fff; border: 1px solid #ced4da; border-radius: 4px; padding: 10px; max-height: 120px; overflow-y: auto; font-size: 14px; line-height: 1.5; color: #495057; }
        #form-step-2 #agree-label { display: flex; align-items: center; justify-content: center; font-weight: normal; }
        #form-step-2 #agree-checkbox { width: auto; margin-right: 10px; }

        .upload-section { border-top: 1px solid #dee2e6; margin-top: 25px; padding-top: 20px; }
        .upload-section h4 { margin-top: 0; color: #333; }
        .upload-group { margin-bottom: 15px; }
        .upload-group label { font-weight: bold; display: block; margin-bottom: 5px; font-size: 14px; }
        .upload-group h5 { margin-top: 20px; margin-bottom: 10px; font-size: 15px; color: #007bff; }
        input[type="file"] { border: 1px solid #dee2e6; padding: 8px; border-radius: 5px; width: 100%; box-sizing: border-box; font-size: 14px; }
        
        #form-step-3 { text-align: center; padding: 40px 20px; }
        #form-step-3 .success-icon { width: 80px; height: 80px; margin: 0 auto 20px auto; }
        #form-step-3 .success-icon .checkmark { stroke: #28a745; stroke-width: 4; stroke-dasharray: 48; stroke-dashoffset: 48; animation: draw 0.6s ease-out forwards; }
        @keyframes draw { to { stroke-dashoffset: 0; } }
        #form-step-3 h1 { color: #28a745; font-size: 24px; }
        #form-step-3 p { font-size: 16px; color: #666; }
        
        .message-box { text-align: center; padding: 15px; border-radius: 5px; margin-top: 20px; display: none; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .pair-info-section { border-left: 3px solid #007bff; margin-top: 25px; padding-left: 15px; display: none; }
    </style>
</head>
<body>

<div class="form-container">
    <form id="register-form" novalidate enctype="multipart/form-data">
        
        <div id="form-step-1">
            <h1>新規入会フォーム</h1>
            <div class="form-group">
                <label for="store">入会店舗</label>
                <select id="store" name="store" required>
                    <option value="">店舗を選択してください</option>
                    <option value="あびこ店">あびこ店</option>
                    <option value="東三国店">東三国店</option>
                    <option value="イオンタウン松原店">イオンタウン松原店</option>
                    <option value="尼崎店">尼崎店</option>
                    <option value="古市店">古市店</option>
                    <option value="藤井寺店">藤井寺店</option>
                    <option value="東大阪店">東大阪店</option>
                    <option value="兵庫店">兵庫店</option>
                    <option value="平野店">平野店</option>
                    <option value="芦屋店">芦屋店</option>
                </select>
            </div>
            <div class="form-group">
                <label for="plan">プラン</label>
                <select id="plan" name="plan" required>
                    <option value="">先に店舗を選択してください</option>
                </select>
            </div>
            <hr>
            <h4>ご本人様情報</h4>
            <div class="form-group"><label for="name">氏名</label><input type="text" id="name" name="name" required></div>
            <div class="form-group"><label for="furigana">フリガナ</label><input type="text" id="furigana" name="furigana" required></div>
            <div class="form-group"><label for="gender">性別</label><select id="gender" name="gender" required><option value="">選択してください</option><option value="男性">男性</option><option value="女性">女性</option><option value="その他">その他</option></select></div>
            <div class="form-group"><label for="dob">生年月日</label><input type="date" id="dob" name="dob" required></div>
            <div class="form-group"><label for="email">メールアドレス</label><input type="email" id="email" name="email" required></div>
            <div class="form-group"><label for="tel">電話番号</label><input type="tel" id="tel" name="tel" required></div>
            <div class="form-group"><label for="address">住所</label><textarea id="address" name="address" required></textarea></div>
            <div class="form-group"><label for="emergency_contact_name">緊急連絡先氏名</label><input type="text" id="emergency_contact_name" name="emergency_contact_name" required></div>
            <div class="form-group"><label for="emergency_contact_phone">緊急連絡先電話番号</label><input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" required></div>
            <div class="form-group"><label for="channel">当ジムを何で知りましたか？</label><select id="channel" name="channel" required><option value="">選択してください</option><option value="Web検索">Web検索</option><option value="紹介">紹介</option><option value="チラシ">チラシ</option><option value="SNS">SNS</option><option value="その他">その他</option></select></div>
            <div id="pair-info-section" class="pair-info-section">
                <h3>ペア会員様情報</h3>
                <div class="form-group" style="display: none;"><label for="pair_member_id">ペア会員様 会員番号（任意）</label><input type="text" id="pair_member_id" name="pair_member_id"></div>
                <div class="form-group"><label for="pair_name">ペア会員様 氏名</label><input type="text" id="pair_name" name="pair_name"></div>
                <div class="form-group"><label for="pair_furigana">ペア会員様 フリガナ</label><input type="text" id="pair_furigana" name="pair_furigana"></div>
                <div class="form-group"><label for="pair_gender">ペア会員様 性別</label><select id="pair_gender" name="pair_gender"><option value="">選択してください</option><option value="男性">男性</option><option value="女性">女性</option><option value="その他">その他</option></select></div>
                <div class="form-group"><label for="pair_dob">ペア会員様 生年月日</label><input type="date" id="pair_dob" name="pair_dob"></div>
            </div>
            <button type="button" id="next-btn">次へ</button>
        </div>

        <div id="form-step-2" style="display: none;">
            <h2>お申し込み内容の確認と書類提出</h2>
            <div class="summary-box">
                <p><strong>入会店舗:</strong> <span id="confirm-store"></span></p>
                <p><strong>プラン:</strong> <span id="confirm-plan"></span></p>
                <p><strong>料金:</strong> <span id="confirm-price"></span></p>
                <p><strong>適用キャンペーン:</strong><br><span id="confirm-campaign"></span></p>
                <p><strong>解約について:</strong><br><span id="confirm-cancellation"></span></p>
                <div>
                    <strong>注意事項:</strong>
                    <div id="confirm-notes" class="notes-box"></div>
                </div>
            </div>

            <div class="upload-section">
                <h4>必要書類のアップロード</h4>
                <div class="upload-group">
                    <label for="face_photo">お顔の写真</label>
                    <input type="file" id="face_photo" name="face_photo" accept="image/*" required>
                </div>
                <div class="upload-group">
                    <label for="id_doc">身分証明書</label>
                    <input type="file" id="id_doc" name="id_doc" accept="image/*" required>
                </div>
                
                <div id="transfer-doc-section" style="display:none;">
                    <div class="upload-group">
                        <label for="transfer_doc">他ジムの入会・退会書類 (乗り換え割の方)</label>
                        <input type="file" id="transfer_doc" name="transfer_doc" accept="image/*,application/pdf">
                    </div>
                </div>

                <div id="pair-doc-section" style="display:none;">
                    <h5>ペア会員様の書類</h5>
                    <div class="upload-group">
                        <label for="pair_face_photo">お顔の写真（ペア）</label>
                        <input type="file" id="pair_face_photo" name="pair_face_photo" accept="image/*">
                    </div>
                    <div class="upload-group">
                        <label for="pair_id_doc">身分証明書（ペア）</label>
                        <input type="file" id="pair_id_doc" name="pair_id_doc" accept="image/*">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label id="agree-label">
                    <input type="checkbox" id="agree-checkbox"> 上記の全ての事項に同意します。
                </label>
            </div>
            <div id="message-box" class="message-box"></div>
            <button type="button" id="register-btn" disabled>この内容で入会する</button>
            <button type="button" id="back-btn">入力画面に戻る</button>
        </div>

        <div id="form-step-3" style="display: none;">
            <div class="success-icon">
                <svg viewBox="0 0 52 52"><circle cx="26" cy="26" r="25" fill="none" stroke="#eee" stroke-width="4"/><path class="checkmark" fill="none" d="M14,27 L22,35 L38,18"/></svg>
            </div>
            <h1>お申し込み完了</h1>
            <p>ご入会ありがとうございます。<br>手続きが完了しました。<br>確認メールを送信しましたので、ご確認ください。</p>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const step1 = document.getElementById('form-step-1');
    const step2 = document.getElementById('form-step-2');
    const step3 = document.getElementById('form-step-3');
    const nextBtn = document.getElementById('next-btn');
    const backBtn = document.getElementById('back-btn');
    const registerBtn = document.getElementById('register-btn');
    const agreeCheckbox = document.getElementById('agree-checkbox');
    const messageBox = document.getElementById('message-box');
    const storeSelect = document.getElementById('store');
    const planSelect = document.getElementById('plan');
    const pairInfoSection = document.getElementById('pair-info-section');
    
    // テンプレートリテラル(``)を使い、見たままの改行で注意事項を記述
    const longNotes = `
1. 本施設はセルフ利用を基本としております。
2. 会費はクレジットカードからの自動引き落としとなります。
3. 施設内での私物の盗難、紛失について一切の責任を負いかねます。
4. 忘れ物の保管期間は1ヶ月とさせていただきます。
5. 施設内は土足厳禁です。必ず室内用シューズをご持参ください。
6. マシンのご利用後は、備え付けのタオルで清掃をお願いいたします。
7. 大声での会話や長時間のマシンの占有はご遠慮ください。
8. 体調が優れない場合は、施設の利用をお控えください。
    `.trim().replace(/\n/g, '<br>'); // 先頭と末尾の空白を削除し、改行を<br>タグに変換

    const planDetails = {
        'あびこ店': {
            'クレジットプラン': { price: '月額 8,800円', campaign: '初月無料キャンペーン！<br>さらに事務手数料も0円！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '家族割クレジットプラン': { price: '月額 7,700円', campaign: '家族2人目以降ずっと割引！', cancellation: '代表者の退会と同時に割引は終了します。', notes: longNotes }
        },
        '東三国店': {
            '誰でも割': { price: '月額 7,500円', campaign: '入会金0円！', cancellation: '6ヶ月間の継続利用が条件となります。', notes: longNotes },
            '年割': { price: '年払い 80,000円', campaign: '1年分一括でお得！', cancellation: '途中解約による返金はいたしかねます。', notes: longNotes },
            '乗り換え割': { price: '月額 7,000円', campaign: '他社からの乗り換えで半年間割引！', cancellation: '6ヶ月間の継続利用が条件となります。', notes: longNotes },
            '一括プラン': { price: '一括 150,000円', campaign: '永久会員プラン！', cancellation: 'ご本人様のみ有効です。譲渡・返金はできません。', notes: longNotes },
            'パーソナルプラン': { price: '月額 20,000円', campaign: '専属トレーナーがサポート！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 6,500円', campaign: 'ペア入会で事務手数料も0円！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        },
        'イオンタウン松原店': {
            '松原店プランA': { price: '月額 8,000円', campaign: 'オープニングキャンペーン中！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '松原店プランB': { price: '月額 9,000円', campaign: '全店舗利用可能！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '家族割クレジットプラン': { price: '月額 7,000円', campaign: 'ご家族でお得に！', cancellation: '代表者の退会と同時に割引は終了します。', notes: longNotes }
        },
        '尼崎店': {
            '尼崎店プランA': { price: '月額 7,800円', campaign: 'Web入会限定価格！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '尼崎店プランB': { price: '月額 8,800円', campaign: '水素水サーバー無料！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 6,800円', campaign: 'お友達と一緒に入会！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        },
        '古市店': {
            '古市店プランA': { price: '月額 7,800円', campaign: '初月会費500円！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '古市店プランB': { price: '月額 8,800円', campaign: 'タオルレンタル無料！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 6,800円', campaign: '2人で始めよう！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        },
        '藤井寺店': {
            '藤井寺店プランA': { price: '月額 7,900円', campaign: 'セキュリティキー発行料0円！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '藤井寺店プランB': { price: '月額 8,900円', campaign: 'プロテインサーバー利用可！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '家族割クレジットプラン': { price: '月額 6,900円', campaign: '家族で健康に！', cancellation: '代表者の退会と同時に割引は終了します。', notes: longNotes }
        },
        '東大阪店': {
            '東大阪店プランA': { price: '月額 8,200円', campaign: '事務手数料半額！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '東大阪店プランB': { price: '月額 9,200円', campaign: 'パーソナル3回無料！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 7,200円', campaign: 'モチベーションアップ！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        },
        '兵庫店': {
            '兵庫店プランA': { price: '月額 8,100円', campaign: '初月会費0円！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '兵庫店プランB': { price: '月額 9,100円', campaign: 'いつでもアップグレード可能！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 7,100円', campaign: '2人ならもっと楽しい！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        },
        '平野店': {
            '平野店プランA': { price: '月額 7,700円', campaign: '最初の2ヶ月間割引！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '平野店プランB': { price: '月額 8,700円', campaign: '全店の相互利用OK！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '家族割クレジットプラン': { price: '月額 6,700円', campaign: '家族みんなでフィットネス！', cancellation: '代表者の退会と同時に割引は終了します。', notes: longNotes }
        },
        '芦屋店': {
            '芦屋店プランA': { price: '月額 10,000円', campaign: '高級アメニティ完備！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            '芦屋店プランB': { price: '月額 12,000円', campaign: 'プライベートロッカー付き！', cancellation: '退会希望月の前月10日までにお手続きが必要です。', notes: longNotes },
            'ペア割プラン': { price: 'お一人様 月額 9,000円', campaign: 'ご夫婦・カップルでどうぞ！', cancellation: 'ペアのどちらかが退会した場合、通常プランに移行します。', notes: longNotes }
        }
    };
    // ★★★ ここまで修正箇所 ★★★
    const pairPlanNames = ['家族割クレジットプラン', 'ペア割プラン'];
    const plansByStore = {
        'あびこ店': ['クレジットプラン', '家族割クレジットプラン'],
        '東三国店': ['誰でも割', '年割', '乗り換え割', '一括プラン', 'パーソナルプラン', 'ペア割プラン'],
        'イオンタウン松原店': ['松原店プランA', '松原店プランB', '家族割クレジットプラン'],
        '尼崎店': ['尼崎店プランA', '尼崎店プランB', 'ペア割プラン'],
        '古市店': ['古市店プランA', '古市店プランB', 'ペア割プラン'],
        '藤井寺店': ['藤井寺店プランA', '藤井寺店プランB', '家族割クレジットプラン'],
        '東大阪店': ['東大阪店プランA', '東大阪店プランB', 'ペア割プラン'],
        '兵庫店': ['兵庫店プランA', '兵庫店プランB', 'ペア割プラン'],
        '平野店': ['平野店プランA', '平野店プランB', '家族割クレジットプラン'],
        '芦屋店': ['芦屋店プランA', '芦屋店プランB', 'ペア割プラン']
    };

    storeSelect.addEventListener('change', function() {
        const selectedStore = this.value;
        planSelect.innerHTML = '<option value="">プランを選択してください</option>';
        if (selectedStore && plansByStore[selectedStore]) {
            plansByStore[selectedStore].forEach(plan => {
                const option = document.createElement('option');
                option.value = plan;
                option.textContent = plan;
                planSelect.appendChild(option);
            });
        }
        planSelect.dispatchEvent(new Event('change'));
    });
    
    planSelect.addEventListener('change', function() {
        const isPairPlan = pairPlanNames.includes(this.value);
        pairInfoSection.style.display = isPairPlan ? 'block' : 'none';
        pairInfoSection.querySelectorAll('input, select').forEach(input => {
             if (input.name !== 'pair_member_id') input.required = isPairPlan;
        });
    });

    nextBtn.addEventListener('click', function() {
        // ステップ1の入力項目のみをチェックする
        const step1Inputs = step1.querySelectorAll('input, select, textarea');
        let isStep1Valid = true;
        step1Inputs.forEach(input => {
            if (!input.checkValidity()) {
                isStep1Valid = false;
            }
        });
        
        if (!isStep1Valid) {
            form.reportValidity();
            return;
        }
        const store = storeSelect.value;
        const plan = planSelect.value;
        
        document.getElementById('confirm-store').textContent = store;
        document.getElementById('confirm-plan').textContent = plan;
        
        const details = planDetails[store] && planDetails[store][plan];
        const defaultText = '該当情報なし';

        if (details) {
            document.getElementById('confirm-price').innerHTML = details.price || defaultText;
            document.getElementById('confirm-campaign').innerHTML = details.campaign || defaultText;
            document.getElementById('confirm-cancellation').innerHTML = details.cancellation || defaultText;
            document.getElementById('confirm-notes').innerHTML = details.notes || defaultText;
        } else {
            document.getElementById('confirm-price').textContent = defaultText;
            document.getElementById('confirm-campaign').textContent = defaultText;
            document.getElementById('confirm-cancellation').textContent = defaultText;
            document.getElementById('confirm-notes').textContent = defaultText;
        }
        
        const transferDocSection = document.getElementById('transfer-doc-section');
        const pairDocSection = document.getElementById('pair-doc-section');
        const transferDocInput = document.getElementById('transfer_doc');
        const pairFacePhotoInput = document.getElementById('pair_face_photo');
        const pairIdDocInput = document.getElementById('pair_id_doc');

        transferDocSection.style.display = 'none';
        transferDocInput.required = false;
        pairDocSection.style.display = 'none';
        pairFacePhotoInput.required = false;
        pairIdDocInput.required = false;

        if (plan === '乗換割') {
            transferDocSection.style.display = 'block';
            transferDocInput.required = true;
        }

        if (pairPlanNames.includes(plan)) {
            pairDocSection.style.display = 'block';
            pairFacePhotoInput.required = true;
            pairIdDocInput.required = true;
        }
        
        step1.style.display = 'none';
        step2.style.display = 'block';
    });

    backBtn.addEventListener('click', function() {
        step2.style.display = 'none';
        step1.style.display = 'block';
        messageBox.style.display = 'none';
    });
    
    agreeCheckbox.addEventListener('change', function() {
        registerBtn.disabled = !this.checked;
    });

    registerBtn.addEventListener('click', async function() {
        // ★★★ ここで初めてフォーム全体のチェックを行う ★★★
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        this.disabled = true;
        this.textContent = '送信中...';
        messageBox.style.display = 'none';

        const formData = new FormData(form);
        formData.append('action', 'register');

        try {
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                 const errorText = await response.text();
                 throw new Error(`サーバーエラー: ${response.status} ${errorText}`);
            }
            const result = await response.json();
            if (result.success) {
                step2.style.display = 'none';
                step3.style.display = 'block';
            } else {
                throw new Error(result.error || '不明なエラーが発生しました。');
            }
        } catch (error) {
            console.error('Submission error:', error);
            messageBox.textContent = `エラーが発生しました: ${error.message}`;
            messageBox.className = 'message-box error-message';
            messageBox.style.display = 'block';
            this.disabled = false;
            this.textContent = 'この内容で入会する';
        }
    });
});
</script>

</body>
</html>
