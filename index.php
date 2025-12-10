<?php
// spent_bill_gates_money.php
// Single-file PHP app: a simple "Spend Bill Gates' Money" style site
// Usage: save as spent_bill_gates_money.php and run: php -S localhost:8000
// Then open http://localhost:8000/spent_bill_gates_money.php

session_start();
header('Content-Type: text/html; charset=utf-8');

// --- Configuration ---
$DEFAULT_TOTAL = 100000000000; // 100 billion by default
$CATEGORIES = [
    ['id'=>'tech','name'=>'تکنولوژی','min'=>100000,'max'=>50000000],
    ['id'=>'charity','name'=>'خیریه','min'=>1000,'max'=>10000000],
    ['id'=>'realestate','name'=>'خرید ملک','min'=>1000000,'max'=>200000000],
    ['id'=>'yacht','name'=>'قایق تفریحی','min'=>5000000,'max'=>300000000],
    ['id'=>'food','name'=>'رستوران','min'=>10,'max'=>5000],
    ['id'=>'weird','name'=>'چیز عجیب','min'=>1,'max'=>1000000],
];

// Initialize session state
if (!isset($_SESSION['total'])) {
    $_SESSION['total'] = $DEFAULT_TOTAL;
    $_SESSION['spent_list'] = [];
}

// AJAX endpoint to spend money
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'spend') {
    $cat_id = $_POST['category'] ?? '';
    // find category
    $cat = null;
    foreach ($CATEGORIES as $c) if ($c['id'] === $cat_id) { $cat = $c; break; }
    if (!$cat) {
        echo json_encode(['ok'=>false,'error'=>'دسته نامعتبر']);
        exit;
    }
    $remaining = floatval($_SESSION['total']);
    if ($remaining <= 0) {
        echo json_encode(['ok'=>false,'error'=>'تمام پول‌ها خرج شده']);
        exit;
    }
    // random spend amount bounded by category min/max but not more than remaining
    $min = $cat['min']; $max = $cat['max'];
    $max = min($max, $remaining);
    if ($max < $min) $min = max(1, intval($max/2));
    $amount = mt_rand($min, $max);
    $_SESSION['total'] = $remaining - $amount;
    $entry = ['category'=>$cat['name'],'amount'=>$amount,'time'=>time()];
    array_unshift($_SESSION['spent_list'], $entry);
    // keep last 50
    $_SESSION['spent_list'] = array_slice($_SESSION['spent_list'],0,50);
    echo json_encode(['ok'=>true,'amount'=>$amount,'total'=>$_SESSION['total'],'entry'=>$entry]);
    exit;
}

// AJAX endpoint to reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    $_SESSION['total'] = $DEFAULT_TOTAL;
    $_SESSION['spent_list'] = [];
    echo json_encode(['ok'=>true,'total'=>$_SESSION['total']]);
    exit;
}

// AJAX endpoint to set custom total
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_total') {
    $t = $_POST['total'] ?? '';
    $t = preg_replace('/[^0-9]/','',$t);
    if ($t === '') { echo json_encode(['ok'=>false]); exit; }
    $_SESSION['total'] = floatval($t);
    $_SESSION['spent_list'] = [];
    echo json_encode(['ok'=>true,'total'=>$_SESSION['total']]);
    exit;
}

// Helper: format currency (USD with commas)
function fmt($n) {
    return '$' . number_format($n, 0, '.', ',');
}

?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>خرج کردن پول بیل گیتس — نمونه PHP</title>
<style>
    :root{--accent:#2b8;--bg:#0f1724;--card:#0b1220;color-scheme:dark}
    body{font-family:Tahoma, Arial; margin:0; background:linear-gradient(180deg,#071022 0%, #081827 100%); color:#e6eef6}
    .container{max-width:980px;margin:24px auto;padding:20px}
    header{display:flex;align-items:center;justify-content:space-between}
    h1{margin:0;font-size:20px}
    .card{background:rgba(255,255,255,0.03);border-radius:12px;padding:16px;box-shadow:0 6px 18px rgba(2,6,23,0.6)}
    .big{font-size:28px;font-weight:700}
    .grid{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-top:16px}
    .cats{display:flex;flex-wrap:wrap;gap:8px}
    .cat{background:rgba(255,255,255,0.02);padding:10px 12px;border-radius:8px;cursor:pointer; user-select:none}
    .cat:hover{transform:translateY(-3px)}
    button{background:var(--accent);border:0;padding:10px 14px;border-radius:8px;color:#002018;cursor:pointer;font-weight:700}
    .small{font-size:13px;color:#a9c0d9}
    ul{padding-left:0;list-style:none;margin:8px 0 0 0;max-height:380px;overflow:auto}
    li{padding:8px 0;border-bottom:1px dashed rgba(255,255,255,0.03)}
    .spent-amt{font-weight:700}
    .controls{display:flex;gap:8px;align-items:center}
    input[type=number]{padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    footer{margin-top:18px;color:#9fb0c8;font-size:12px}
    @media(max-width:880px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">
    <header>
        <h1>خرج کردن پول بیل گیتس — نمونه PHP</h1>
        <div class="small">جلسه شما با php session ذخیره می‌شود</div>
    </header>

    <div class="grid">
        <main class="card">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div class="small">موجودی فعلی</div>
                    <div class="big" id="total"><?php echo fmt($_SESSION['total']); ?></div>
                </div>
                <div style="text-align:left">
                    <div class="small">تنظیم مقدار اولیه</div>
                    <div class="controls">
                        <input id="setTotalInput" type="number" placeholder="مثال: 100000000000">
                        <button id="setTotalBtn">تنظیم</button>
                        <button id="resetBtn" style="background:#ff6b6b;color:white">بازنشانی</button>
                    </div>
                </div>
            </div>

            <hr style="margin:12px 0;border:none;border-top:1px solid rgba(255,255,255,0.03)">
            <div class="small">دسته‌ها — روی یک دسته کلیک کنید تا مبلغی خرج شود</div>
            <div class="cats" id="cats">
                <?php foreach($CATEGORIES as $c): ?>
                    <div class="cat" data-id="<?php echo $c['id']; ?>">
                        <div style="font-weight:700"><?php echo htmlspecialchars($c['name']); ?></div>
                        <div class="small">حدود <?php echo fmt($c['min']); ?> — <?php echo fmt($c['max']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr style="margin:12px 0;border:none;border-top:1px solid rgba(255,255,255,0.03)">
            <div class="small">آخرین خرج‌ها</div>
            <ul id="spentList">
                <?php foreach($_SESSION['spent_list'] as $e): ?>
                    <li>
                        <div style="display:flex;justify-content:space-between">
                            <div class="small"><?php echo htmlspecialchars($e['category']); ?> — <?php echo date('Y-m-d H:i',$e['time']); ?></div>
                            <div class="spent-amt"><?php echo fmt($e['amount']); ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        </main>

        <aside class="card">
            <div class="small">ابزارک‌ها</div>
            <div style="margin-top:10px">
                <button id="autoSpendBtn">خرج خودکار (10 بار)</button>
            </div>
            <div style="margin-top:12px" class="small">صادرات / اشتراک</div>
            <div style="margin-top:8px">
                <button id="exportBtn">دریافت JSON خرج‌ها</button>
            </div>
            <div style="margin-top:12px" class="small">نکات</div>
            <ul>
                <li class="small">جلسه‌ها در cookie/session ذخیره می‌شوند.</li>
                <li class="small">می‌توانید دسته‌ها و مقادیر پیش‌فرض را در فایل PHP تغییر دهید.</li>
            </ul>
        </aside>
    </div>

    <footer class="small">نمونه ساده — برای گسترش، افزودن تصاویر، انیمیشن و اشتراک‌گذاری لینک وضعیت پیشنهاد می‌شود.</footer>
</div>

<script>
function qs(sel){return document.querySelector(sel)}
function qsa(sel){return document.querySelectorAll(sel)}

function postForm(data){
    return fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(data)})
    .then(r=>r.json());
}

qsa('.cat').forEach(function(el){
    el.addEventListener('click',function(){
        const id = el.dataset.id;
        el.style.transform='scale(0.98)';
        setTimeout(()=>el.style.transform='',120);
        postForm({action:'spend',category:id}).then(handleSpendResponse);
    });
});

function handleSpendResponse(res){
    if(!res.ok){ alert(res.error || 'خطا'); return; }
    // animate total and prepend entry
    const totalEl = qs('#total');
    totalEl.textContent = formatMoney(res.total);
    const ul = qs('#spentList');
    const li = document.createElement('li');
    li.innerHTML = `<div style="display:flex;justify-content:space-between"><div class="small">${escapeHtml(res.entry.category)} — ${new Date(res.entry.time*1000).toLocaleString()}</div><div class="spent-amt">${formatMoney(res.entry.amount)}</div></div>`;
    ul.insertBefore(li, ul.firstChild);
}

qs('#resetBtn').addEventListener('click',function(){
    if(!confirm('آیا مطمئن هستید می‌خواهید بازنشانی شود؟')) return;
    postForm({action:'reset'}).then(r=>{ if(r.ok) qs('#total').textContent=formatMoney(r.total); qs('#spentList').innerHTML=''; });
});

qs('#setTotalBtn').addEventListener('click',function(){
    const v = qs('#setTotalInput').value.trim(); if(!v) return alert('یک عدد وارد کنید');
    postForm({action:'set_total', total:v}).then(r=>{ if(r.ok){ qs('#total').textContent=formatMoney(r.total); qs('#spentList').innerHTML=''; } else alert('خطا'); });
});

qs('#autoSpendBtn').addEventListener('click',async function(){
    for(let i=0;i<10;i++){
        // choose random category
        const cats = Array.from(qsa('.cat'));
        const pick = cats[Math.floor(Math.random()*cats.length)];
        await postForm({action:'spend',category:pick.dataset.id}).then(handleSpendResponse);
        await new Promise(r=>setTimeout(r,220));
    }
});

qs('#exportBtn').addEventListener('click',function(){
    // request current session dump by building from DOM
    const items = Array.from(qsa('#spentList li')).map(li=>{
        return {html:li.innerHTML};
    });
    const payload = {total:qs('#total').textContent, items:items};
    const blob = new Blob([JSON.stringify(payload, null, 2)],{type:'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href=url; a.download='spent_session.json'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
});

function formatMoney(n){
    // n might be number or string like "$1,234"
    if(typeof n==='string'){
        // try extract digits
        const d = n.replace(/[^0-9.-]/g,'');
        n = Number(d);
    }
    if(isNaN(n)) return n;
    return '$' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
}

function escapeHtml(s){return s.replace(/[&<>\"]/,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]||c}) }
</script>
</body>
</html>
