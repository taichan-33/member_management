<?php
// セッションを開始
session_start();

// ログイン状態を確認し、ログインしていなければlogin.phpへリダイレクト
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>顧客管理システム</title>
    <style>
        :root { --main-bg-color: #f4f6f8; --primary-color: #007bff; --text-color: #333; --border-color: #dee2e6; --danger-color: #dc3545; --secondary-color: #6c757d; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; background-color: var(--main-bg-color); color: var(--text-color); }
        
        /* ★★★ サイドバー関連CSS ★★★ */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1100;
            padding: 20px;
            box-sizing: border-box;
        }
        #sidebar.open {
            transform: translateX(0);
        }
        #sidebar h2 {
            color: var(--primary-color);
            text-align: left;
            margin-top: 10px;
            margin-bottom: 30px;
            font-size: 22px;
        }
        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 15px 10px;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        #sidebar ul li a:hover {
            background-color: #f0f0f0;
        }
        #sidebar ul li a svg {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            fill: var(--secondary-color);
        }
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1050;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        #overlay.active {
            display: block;
            opacity: 1;
        }
        #menu-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1200;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            width: 24px;
            height: 24px;
        }
        #menu-toggle span {
            display: block;
            width: 100%;
            height: 3px;
            background: var(--text-color);
            border-radius: 3px;
            transition: all 0.3s ease-in-out;
        }
        #menu-toggle.open span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        #menu-toggle.open span:nth-child(2) {
            opacity: 0;
        }
        #menu-toggle.open span:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 15px; }
        .header { display: flex; align-items: center; padding-bottom: 15px; flex-wrap: nowrap; gap: 15px; }
        .header h1 { margin: 0; font-size: 24px; flex-grow: 1; text-align: center; }
        #new-member-btn { padding: 8px 12px; border-radius: 5px; border: 1px solid var(--primary-color); background-color: var(--primary-color); color: white; cursor: pointer; font-size: 14px;}
        .controls { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; }
        .controls input, .controls select { padding: 8px 12px; border-radius: 5px; border: 1px solid var(--border-color); font-size: 14px; background-color: white; }
        .controls input { flex-grow: 1; min-width: 250px; }
        .view-filter-group { display: flex; flex-wrap: wrap; gap: 15px; padding: 8px 0; font-size: 14px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); margin-bottom: 15px; }
        .view-filter-group label { cursor: pointer; display: flex; align-items: center; gap: 5px; }
        #member-list { list-style: none; padding: 0; margin: 0; min-height: 200px; }
        #member-list li { background-color: white; border: 1px solid var(--border-color); border-radius: 5px; margin-bottom: 10px; padding: 15px; cursor: pointer; transition: box-shadow 0.2s; display: flex; align-items: center; gap: 15px; }
        #member-list li:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        /* ★★★ 会員一覧でアイコンとして顔を表示させるデザイン ★★★ */
        .member-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); }
        .member-info-container { flex-grow: 1; }
        
        
        .member-item-header { font-weight: bold; font-size: 16px; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
        .member-item-details { font-size: 14px; color: #666; display: flex; gap: 15px; }
        .status-tag { padding: 3px 8px; font-size: 12px; font-weight: bold; border-radius: 4px; color: white; }
        .tag-withdrawn { background-color: var(--danger-color); }
        .modal { display: none; position: fixed; z-index: 1200; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; border-radius: 8px; max-width: 600px; width: 90%; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px; }
        .modal-title { font-size: 20px; margin: 0; }
        .close-btn { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        #member-form { max-height: 70vh; overflow-y: auto; padding-right: 15px; }
        #member-form .form-group { margin-bottom: 15px; }
        #member-form label { display: block; margin-bottom: 5px; font-weight: bold; }
        #member-form input, #member-form select, #member-form textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid var(--border-color); border-radius: 4px; }
        #member-form input:disabled, #member-form select:disabled, #member-form textarea:disabled { background-color: #e9ecef; color: #6c757d; border-color: #ced4da; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 15px; border-top: 1px solid var(--border-color); margin-top: 15px; }
        .modal-footer button { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        #save-btn { background-color: var(--primary-color); color: white; }
        #edit-btn { background-color: var(--secondary-color); color: white; }

        /* ★★★ 詳細画面で身分証などを表示させるデザイン ★★★ */
        .toggle-button { background-color: var(--secondary-color); color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; margin-top: 15px; width: 100%; text-align: left; font-size: 14px; }
        .details-container { display: none; border-left: 3px solid var(--primary-color); margin-top: 15px; padding-left: 15px; }
        .image-viewer { display: flex; flex-wrap: wrap; gap: 15px; padding-top: 10px; }
        .image-viewer .image-item { width: calc(50% - 10px); }
        .image-viewer .image-item label { font-weight: bold; font-size: 12px; color: #555; }
        .image-viewer .image-item img { max-width: 100%; border-radius: 4px; border: 1px solid var(--border-color); margin-top: 5px; }
       
        
        .pair-info-toggle { background-color: var(--secondary-color); color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; margin-top: 15px; width: 100%; text-align: left; font-size: 14px; }
        .pagination-controls { display: flex; justify-content: center; align-items: center; gap: 10px; padding: 20px 0; }
        .pagination-controls button { padding: 8px 12px; font-size: 14px; border: 1px solid var(--border-color); background-color: white; cursor: pointer; border-radius: 5px; }
        .pagination-controls button:disabled { background-color: #e9ecef; cursor: not-allowed; color: #6c757d; }
        .pagination-controls .page-info { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<nav id="sidebar">
    <h2>管理メニュー</h2>
    <ul>
        <li>
            <a href="logout.php">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z"/></svg>
                ログアウト
            </a>
        </li>
    </ul>
</nav>
<div id="overlay"></div>

<div class="container">
    <div class="header">
        <button id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h1>顧客管理システム</h1>
        <button id="new-member-btn">＋新規登録</button>
    </div>
    <div class="controls">
        <input type="text" id="search-box" placeholder="会員番号、名前、フリガナ、電話番号で検索...">
        <select id="store-filter"></select>
        <select id="plan-filter"><option value="">全プラン</option></select>
        <select id="sort-order">
            <option value="id_asc">デフォルト（登録順）</option>
            <option value="member_id_asc">会員番号の若い順</option>
            <option value="member_id_desc">会員番号の大きい順</option>
            <option value="name_asc">名前のあいうえお順</option>
            <option value="name_desc">名前のあいうえお順（降順）</option>
            <option value="reg_date_desc">入会日の新しい順</option>
            <option value="reg_date_asc">入会日の古い順</option>
        </select>
    </div>
    <div id="view-filter" class="view-filter-group">
        <label><input type="radio" name="view" value="all" checked> 全員</label>
        <label><input type="radio" name="view" value="active_only"> 在籍者のみ</label>
        <label><input type="radio" name="view" value="new_only"> 新規受付のみ</label>
        <label><input type="radio" name="view" value="withdrawn_only"> 退会者のみ</label>
    </div>
    <ul id="member-list"></ul>
    <div id="pagination-controls" class="pagination-controls"></div>
</div>

<div id="member-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h2 id="modal-title" class="modal-title">顧客情報</h2><span class="close-btn">&times;</span></div>
        <form id="member-form" enctype="multipart/form-data"></form>
         <div class="modal-footer">
            <button id="edit-btn">編集</button>
            <button id="save-btn">保存</button>
         </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // グローバル変数
    const apiEndpoint = 'api.php';
    let allMembers = [];
    let headers = [];
    let displayHeaders = []; 
    let currentPage = 1;
    const itemsPerPage = 10;
    
    // DOM要素の取得
    const memberListEl = document.getElementById('member-list');
    const paginationControlsEl = document.getElementById('pagination-controls');
    const searchBox = document.getElementById('search-box');
    const storeFilter = document.getElementById('store-filter');
    const planFilter = document.getElementById('plan-filter');
    const sortOrderSelect = document.getElementById('sort-order');
    const viewFilterRadios = document.querySelectorAll('#view-filter input[name="view"]');
    
    const modal = document.getElementById('member-modal');
    const modalTitle = document.getElementById('modal-title');
    const form = document.getElementById('member-form');
    const saveBtn = document.getElementById('save-btn');
    const editBtn = document.getElementById('edit-btn');
    const newMemberBtn = document.getElementById('new-member-btn');
    const closeBtn = document.querySelector('.close-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menu-toggle');

    // 定数データ
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

    /**
     * APIから全顧客データを取得して画面を初期化する
     */
    async function fetchData() {
        try {
            const response = await fetch(apiEndpoint);
            if (!response.ok) { throw new Error(`サーバーエラー: ${response.statusText}`); }
            const text = await response.text();
            try {
                const apiData = JSON.parse(text);
                if(apiData.error) { throw new Error(apiData.error); }
                
                headers = apiData.headers || []; 
                displayHeaders = apiData.displayHeaders || [];
                allMembers = apiData.data || []; 
                
                currentPage = 1;
                populateStoreFilter();
                populatePlanFilter();
                renderList();
            } catch (jsonError) {
                console.error("JSON Parse Error:", jsonError);
                console.error("Received Text:", text.substring(0, 500));
                throw new Error(`サーバーからの応答が不正です (JSONパースエラー)。`);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            memberListEl.innerHTML = `<li>データの読み込みに失敗しました。詳細: ${error.message}</li>`;
        }
    }

    /**
     * フィルターとソートに基づき、顧客一覧を描画する
     */
    function renderList() {
        const searchTerm = searchBox.value.toLowerCase();
        const selectedStore = storeFilter.value;
        const selectedPlan = planFilter.value;
        const selectedView = document.querySelector('#view-filter input[name="view"]:checked').value;
        const sortOrder = sortOrderSelect.value;

        const filtered = allMembers.filter(member => {
            const matchesSearch = searchTerm === '' || 
                                  (member.member_id && String(member.member_id).toLowerCase().includes(searchTerm)) || 
                                  (member.name && member.name.toLowerCase().includes(searchTerm)) || 
                                  (member.furigana && member.furigana.toLowerCase().includes(searchTerm)) || 
                                  (member.tel && member.tel.includes(searchTerm));
            const matchesStore = selectedStore === '' || member.store === selectedStore;
            const matchesPlan = selectedPlan === '' || member.plan === selectedPlan;
            
            const isWithdrawn = (member.withdrawal_date || '').trim() !== '';
            const hasMemberId = (member.member_id || '').trim() !== '';
            let matchesViewFilter = false;
            switch (selectedView) {
                case 'active_only': matchesViewFilter = hasMemberId && !isWithdrawn; break;
                case 'new_only': matchesViewFilter = !hasMemberId && !isWithdrawn; break; 
                case 'withdrawn_only': matchesViewFilter = isWithdrawn; break;
                default: matchesViewFilter = true; break;
            }
            return matchesSearch && matchesStore && matchesPlan && matchesViewFilter;
        });

        const sorted = filtered.sort((a, b) => {
            switch (sortOrder) {
                case 'member_id_asc': return (parseInt(a.member_id, 10) || Infinity) - (parseInt(b.member_id, 10) || Infinity);
                case 'member_id_desc': return (parseInt(b.member_id, 10) || -Infinity) - (parseInt(a.member_id, 10) || -Infinity);
                case 'name_asc': return (a.furigana || '').localeCompare(b.furigana || '', 'ja');
                case 'name_desc': return (b.furigana || '').localeCompare(a.furigana || '', 'ja');
                case 'reg_date_desc': return (b.registration_date || '').localeCompare(a.registration_date || '');
                case 'reg_date_asc': return (a.registration_date || '').localeCompare(b.registration_date || '');
                default: return (parseInt(a.id, 10) || 0) - (parseInt(b.id, 10) || 0);
            }
        });

        const totalItems = sorted.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedItems = sorted.slice(start, end);

        memberListEl.innerHTML = paginatedItems.map(member => {
            const isWithdrawn = (member.withdrawal_date || '').trim() !== '';
            const withdrawnTag = isWithdrawn ? `<span class="status-tag tag-withdrawn">退会済み</span>` : '';
            
            // ★★★ 署名付きURLを使うので、画像が表示されるようになる ★★★
            const avatar = member.face_photo_url 
            ? `<img src="${member.face_photo_url}" alt="顔写真" class="member-avatar" loading="lazy">`
            : `<div class="member-avatar"></div>`; 

            return `
                <li data-id="${member.id}">
                    ${avatar}
                    <div class="member-info-container">
                        <div class="member-item-header">
                            <span>[${member.member_id || '番号なし'}] ${member.name || '名前未設定'}</span>
                            ${withdrawnTag}
                        </div>
                        <div class="member-item-details">
                            <span>店舗: ${member.store || '未設定'}</span>
                            <span>プラン: ${member.plan || '未設定'}</span>
                        </div>
                    </div>
                </li>
            `;
        }).join('') || '<li>該当する顧客が見つかりません。</li>';
        renderPagination(totalItems, totalPages);
    }

    /**
     * モーダル内のフォームを顧客データに応じて生成する
     * @param {object} data - 顧客データオブジェクト。新規の場合は空オブジェクト。
     */
    function createFormFields(data = {}) {
        form.innerHTML = '';
        const isNew = !data.id;

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = 'form-member-id';
        hiddenInput.name = 'id';
        hiddenInput.value = data.id || '';
        form.appendChild(hiddenInput);
        
        if(isNew) {
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'register'; // POST先で処理を識別するために使用
            form.appendChild(actionInput);
        }

        const pairInfoContainer = document.createElement('div');
        pairInfoContainer.id = 'pair-info-container';
        pairInfoContainer.className = 'details-container';
        
        // ★★★★★ ここが全面的に書き換わった部分 ★★★★★
        if (displayHeaders && Array.isArray(displayHeaders) && headers && Array.isArray(headers)) {
            displayHeaders.forEach((headerText, i) => {
                const key = headers[i];
                
                if (!key || key === 'id' || key === 'col_a_blank' || key.endsWith('_url') || key.endsWith('_at')) {
                    return;
                }
                
                const value = data[key] || '';
                const formGroup = document.createElement('div');
                formGroup.className = 'form-group';
                
                const label = document.createElement('label');
                label.textContent = headerText;
                label.htmlFor = `field-${key}`;
                formGroup.appendChild(label);
                
                let inputElement;

                switch(key) {
                    case 'store':
                        inputElement = document.createElement('select');
                        let storeOptions = '<option value="">店舗を選択</option>';
                        Object.keys(plansByStore).forEach(storeName => {
                            storeOptions += `<option value="${storeName}" ${storeName === value ? 'selected' : ''}>${storeName}</option>`;
                        });
                        inputElement.innerHTML = storeOptions;
                        inputElement.addEventListener('change', (e) => updatePlanOptions(e.target.value));
                        break;
                    case 'plan':
                        inputElement = document.createElement('select');
                        inputElement.innerHTML = '<option value="">先に店舗を選択</option>';
                        inputElement.addEventListener('change', (e) => togglePairSectionVisibility(e.target.value));
                        break;
                    case 'gender':
                    case 'pair_gender':
                        inputElement = document.createElement('select');
                        inputElement.innerHTML = `<option value="">性別を選択</option><option value="男性" ${value === '男性' ? 'selected' : ''}>男性</option><option value="女性" ${value === '女性' ? 'selected' : ''}>女性</option><option value="その他" ${value === 'その他' ? 'selected' : ''}>その他</option>`;
                        break;
                    case 'dob':
                    case 'pair_dob':
                        inputElement = document.createElement('input');
                        inputElement.type = 'date';
                        inputElement.value = value;
                        const targetAgeKey = (key === 'dob') ? 'age' : 'pair_age';
                        inputElement.addEventListener('change', (e) => calculateAge(e.target.value, targetAgeKey));
                        break;
                    case 'age':
                    case 'pair_age':
                        inputElement = document.createElement('input');
                        inputElement.readOnly = true;
                        inputElement.style.backgroundColor = '#e9ecef';
                        inputElement.value = value;
                        break;
                    case 'channel':
                        inputElement = document.createElement('select');
                        inputElement.innerHTML = `<option value="">経路を選択</option><option value="Web検索" ${value === 'Web検索' ? 'selected' : ''}>Web検索</option><option value="紹介" ${value === '紹介' ? 'selected' : ''}>紹介</option><option value="チラシ" ${value === 'チラシ' ? 'selected' : ''}>チラシ</option><option value="SNS" ${value === 'SNS' ? 'selected' : ''}>SNS</option><option value="その他" ${value === 'その他' ? 'selected' : ''}>その他</option>`;
                        break;
                    case 'address':
                    case 'memo':
                        inputElement = document.createElement('textarea');
                        inputElement.rows = 3;
                        inputElement.value = value;
                        break;
                    default:
                        inputElement = document.createElement('input');
                        inputElement.type = 'text';
                        inputElement.value = value;
                        break;
                }

                inputElement.id = `field-${key}`;
                inputElement.name = key;
                formGroup.appendChild(inputElement);

                if (key.startsWith('pair_')) {
                    pairInfoContainer.appendChild(formGroup);
                } else {
                    form.appendChild(formGroup);
                }
            });
        }
        
        const pairToggleBtn = document.createElement('button');
        pairToggleBtn.type = 'button';
        pairToggleBtn.className = 'toggle-button';
        pairToggleBtn.textContent = '▼ ペア情報を表示 / 編集';
        pairToggleBtn.addEventListener('click', () => { 
            pairInfoContainer.style.display = pairInfoContainer.style.display === 'block' ? 'none' : 'block'; 
        });
        form.appendChild(pairToggleBtn);
        form.appendChild(pairInfoContainer);
        
        if (!isNew) {
            const docViewerContainer = document.createElement('div');
            docViewerContainer.id = 'doc-viewer-container';
            docViewerContainer.className = 'details-container';
            const imageViewer = document.createElement('div');
            imageViewer.className = 'image-viewer';
            const imageFields = [
                { key: 'face_photo_url', label: '顔写真' }, { key: 'id_doc_url', label: '身分証明書' },
                { key: 'transfer_doc_url', label: '乗換割書類' }, { key: 'pair_face_photo_url', label: 'ペア顔写真' },
                { key: 'pair_id_doc_url', label: 'ペア身分証明書' }
            ];
            let hasImage = false;
            imageFields.forEach(field => {
                if (data[field.key]) {
                    hasImage = true;
                    const item = document.createElement('div');
                    item.className = 'image-item';
                    item.innerHTML = `<label>${field.label}</label><a href="${data[field.key]}" target="_blank"><img src="${data[field.key]}" alt="${field.label}" loading="lazy"></a>`;
                    imageViewer.appendChild(item);
                }
            });
            if (hasImage) {
                const docToggleBtn = document.createElement('button');
                docToggleBtn.type = 'button';
                docToggleBtn.className = 'toggle-button';
                docToggleBtn.textContent = '▼ 身分証などを表示';
                docToggleBtn.addEventListener('click', () => { 
                    docViewerContainer.style.display = docViewerContainer.style.display === 'block' ? 'none' : 'block'; 
                });
                form.appendChild(docToggleBtn);
                docViewerContainer.appendChild(imageViewer);
                form.appendChild(docViewerContainer);
            }
        }
        
        if (isNew) {
            const fileSection = document.createElement('div');
            fileSection.className = 'file-upload-section';
            fileSection.innerHTML = '<h3>必要書類のアップロード</h3>';
            const fileFields = [
                { name: 'face_photo', label: '顔写真', required: true }, { name: 'id_doc', label: '身分証明書', required: true },
                { name: 'transfer_doc', label: '他ジムの書類 (乗り換え割の場合)' }, { name: 'pair_face_photo', label: 'ペア顔写真 (ペアプランの場合)' },
                { name: 'pair_id_doc', label: 'ペア身分証明書 (ペアプランの場合)' }
            ];
            fileFields.forEach(field => {
                const group = document.createElement('div');
                group.className = 'form-group';
                const label = document.createElement('label');
                label.htmlFor = `file-${field.name}`;
                label.textContent = field.label + (field.required ? ' (必須)' : '');
                const input = document.createElement('input');
                input.type = 'file';
                input.id = `file-${field.name}`;
                input.name = field.name;
                input.accept = "image/*,application/pdf";
                if(field.required) input.required = true;
                group.appendChild(label);
                group.appendChild(input);
                fileSection.appendChild(group);
            });
            form.appendChild(fileSection);
        }

        // ★★★ 修正点 ★★★
        updatePlanOptions(data.store || '', data.plan || '');
        if (data.dob) calculateAge(data.dob, 'age');
        if (data.pair_dob) calculateAge(data.pair_dob, 'pair_age');
    }
    
    /**
     * フォームの保存処理（新規・更新を自動で判定）
     */
    async function handleSave() {
        const rowId = document.getElementById('form-member-id').value;
        const isNew = !rowId;
        
        let method, body, headersConfig;

        if (isNew) {
            // 新規登録：FormDataを使用してファイルを含めて送信
            method = 'POST';
            body = new FormData(form);
            
            // 「このリクエストは管理者画面から追加した分にフラグをつける
            body.append('source', 'admin'); 
            
            headersConfig = {}; // FormDataを使う場合、Content-Typeはブラウザが自動設定
        } else {
            // 更新：JSON形式でテキストデータのみ送信
            method = 'PUT';
            const formDataObj = {};
            new FormData(form).forEach((value, key) => { formDataObj[key] = value; });
            body = JSON.stringify(formDataObj);
            headersConfig = { 'Content-Type': 'application/json' };
        }

        try {
            saveBtn.disabled = true;
            saveBtn.textContent = '保存中...';
            
            const response = await fetch(apiEndpoint, { method, headers: headersConfig, body });
            const result = await response.json();
            
            if (!response.ok || result.error) {
                 throw new Error(result.error || `サーバーエラー (${response.status})`);
            }
            
            closeModal();
            fetchData();
        } catch (error) {
            console.error('Error saving data:', error);
            alert(`保存に失敗しました。\n${error.message}`);
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = '保存';
        }
    }
    // --- ここから下はユーティリティ関数とイベントリスナー ---
    
    function populateStoreFilter() {
        const stores = [...new Set(allMembers.map(m => m.store).filter(Boolean))].sort((a,b) => a.localeCompare(b, 'ja'));
        storeFilter.innerHTML = '<option value="">全店舗</option>' + stores.map(s => `<option value="${s}">${s}</option>`).join('');
    }
    
    function populatePlanFilter(selectedStore = '') {
        let plans;
        if (selectedStore && plansByStore[selectedStore]) {
            plans = plansByStore[selectedStore];
        } else {
            plans = [...new Set(allMembers.map(m => m.plan).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'ja'));
        }
        planFilter.innerHTML = '<option value="">全プラン</option>' + plans.map(p => `<option value="${p}">${p}</option>`).join('');
    }

    function renderPagination(totalItems, totalPages) {
        if (totalItems === 0) {
            paginationControlsEl.innerHTML = '';
            return;
        }
        paginationControlsEl.innerHTML = `
            <button id="first-page-btn" ${currentPage === 1 ? 'disabled' : ''}>最初へ</button>
            <button id="prev-page-btn" ${currentPage === 1 ? 'disabled' : ''}>前へ</button>
            <span class="page-info">${currentPage} / ${totalPages} ページ</span>
            <button id="next-page-btn" ${currentPage === totalPages ? 'disabled' : ''}>次へ</button>
            <button id="last-page-btn" ${currentPage === totalPages ? 'disabled' : ''}>最後へ</button>
        `;
        document.getElementById('first-page-btn').addEventListener('click', () => { currentPage = 1; renderList(); });
        document.getElementById('prev-page-btn').addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderList(); } });
        document.getElementById('next-page-btn').addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; renderList(); } });
        document.getElementById('last-page-btn').addEventListener('click', () => { currentPage = totalPages; renderList(); });
    }

    function openModal(title, memberData = null, isEditMode = false) {
        modalTitle.textContent = title;
        createFormFields(memberData || {}); 
        modal.style.display = 'block';
        toggleFormEditState(isEditMode || !memberData); 
    }

    function closeModal() { modal.style.display = 'none'; }

    function toggleFormEditState(isEditable) {
        const formElements = form.querySelectorAll('input:not([type="file"]), select, textarea');
        formElements.forEach(el => {
            if (el.type !== 'hidden') {
                el.disabled = !isEditable;
            }
        });

        editBtn.style.display = isEditable ? 'none' : 'block';
        saveBtn.style.display = isEditable ? 'block' : 'none';
        
        const fileSection = form.querySelector('.file-upload-section');
        if (fileSection) {
            fileSection.style.display = isEditable ? 'block' : 'none';
        }
        
        // ★★★★★ 修正点4: トグルボタンは常に有効にする ★★★★★
        form.querySelectorAll('.toggle-button').forEach(btn => {
            btn.style.pointerEvents = 'auto';
            btn.style.opacity = '1';
        });
    }

    function updatePlanOptions(storeName, currentPlan = '') {
        const planSelect = form.querySelector('[name="plan"]');
        if (!planSelect) return;

        planSelect.innerHTML = '<option value="">プランを選択</option>';
        if (storeName && plansByStore[storeName]) {
            plansByStore[storeName].forEach(planName => {
                const option = document.createElement('option');
                option.value = planName;
                option.textContent = planName;
                if (planName === currentPlan) { option.selected = true; }
                planSelect.appendChild(option);
            });
        }
        togglePairSectionVisibility(planSelect.value);
    }
    
    function togglePairSectionVisibility(planName) {
        const pairContainer = document.getElementById('pair-info-container');
        const pairToggle = Array.from(form.querySelectorAll('.toggle-button')).find(btn => btn.textContent.includes('ペア情報'));
        if (!pairContainer || !pairToggle) return;
        
        const show = pairPlanNames.includes(planName);
        pairToggle.style.display = show ? 'block' : 'none';
        if (!show) { pairContainer.style.display = 'none'; }
    }

    function calculateAge(dob, targetKey) {
        const ageInput = form.querySelector(`[name="${targetKey}"]`);
        if (!ageInput || !dob) { if(ageInput) ageInput.value = ''; return; }
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
        ageInput.value = age >= 0 ? age : '';
    }
    
    // イベントリスナーのセットアップ
    const toggleSidebar = () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        menuToggle.classList.toggle('open');
    };
    menuToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    const addFilterListeners = () => {
        const resetAndRender = () => { currentPage = 1; renderList(); };
        searchBox.addEventListener('input', resetAndRender);
        storeFilter.addEventListener('change', () => { populatePlanFilter(storeFilter.value); resetAndRender(); });
        planFilter.addEventListener('change', resetAndRender);
        sortOrderSelect.addEventListener('change', resetAndRender);
        viewFilterRadios.forEach(radio => radio.addEventListener('change', resetAndRender));
    };
    
    memberListEl.addEventListener('click', e => {
        const li = e.target.closest('li[data-id]');
        if (li) {
            const member = allMembers.find(m => m.id == li.dataset.id);
            if (member) openModal('顧客情報の詳細', member, false);
        }
    });

    newMemberBtn.addEventListener('click', () => openModal('新規顧客の登録', null, true));
    editBtn.addEventListener('click', () => toggleFormEditState(true));
    saveBtn.addEventListener('click', handleSave);
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // 初期化
    addFilterListeners();
    fetchData(); 
});
</script>
</body>
</html>
