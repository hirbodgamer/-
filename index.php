<?php
// Enhanced "Spend Bill Gates Money" — PHP + Modern JS/CSS
session_start();
header('Content-Type: text/html; charset=utf-8');

$DEFAULT_TOTAL = 100000000000;
$CATEGORIES = [
    ['id'=>'tech','name'=>'تکنولوژی','min'=>100000,'max'=>50000000,'icon'=>'💻'],
    ['id'=>'charity','name'=>'خیریه','min'=>1000,'max'=>10000000,'icon'=>'🤝'],
    ['id'=>'realestate','name'=>'خرید ملک','min'=>1000000,'max'=>200000000,'icon'=>'🏠'],
    ['id'=>'yacht','name'=>'قایق تفریحی','min'=>5000000,'max'=>300000000,'icon'=>'🛥️'],
    ['id'=>'food','name'=>'رستوران','min'=>10,'max'=>5000,'icon'=>'🍔'],
    ['id'=>'weird','name'=>'چیز عجیب','min'=>1,'max'=>1000000,'icon'=>'🧸'],
    ['id'=>'plane','name'=>'هواپیما شخصی','min'=>10000000,'max'=>300000000,'icon'=>'✈️'],
    ['id'=>'island','name'=>'جزیره خصوصی','min'=>20000000,'max'=>500000000,'icon'=>'🏝️'],
    ['id'=>'crypto','name'=>'سرمایه‌گذاری کریپتو','min'=>1000,'max'=>20000000,'icon'=>'🪙'],
    ['id'=>'gold','name'=>'خرید طلا و جواهر','min'=>5000,'max'=>10000000,'icon'=>'💎'],
];

if (!isset($_SESSION['total'])) {
    $_SESSION['total'] = $DEFAULT_TOTAL;
    $_SESSION['spent_list'] = [];
}

// AJAX handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'spend') {
        $cat_id = $_POST['category'] ?? '';
        $cat = null;
        foreach ($CATEGORIES as $c) if ($c['id'] === $cat_id) $cat = $c;
        if (!$cat) exit(json_encode(['ok'=>false]));

        $remaining = $_SESSION['total'];
        $min = $cat['min'];
        $max = min($cat['max'], $remaining);
        if ($max < $min) $min = max(1, intval($max/2));

        $amount = mt_rand($min, $max);
        $_SESSION['total'] -= $amount;
        $entry = ['category'=>$cat['name'],'icon'=>$cat['icon'],'amount'=>$amount,'time'=>time()];
        array_unshift($_SESSION['spent_list'], $entry);

        exit(json_encode(['ok'=>true,'amount'=>$amount,'total'=>$_SESSION['total'],'entry'=>$entry]));
    }

    if ($action==='reset'){
        $_SESSION['total']=$DEFAULT_TOTAL;
        $_SESSION['spent_list']=[];
        exit(json_encode(['ok'=>true,'total'=>$_SESSION['total']]));
    }

    if ($action==='set_total'){
        $t = preg_replace('/[^0-9]/','', $_POST['total']);
        $_SESSION['total'] = intval($t);
        $_SESSION['spent_list']=[];
        exit(json_encode(['ok'=>true,'total'=>$_SESSION['total']]));
    }
}

function fmt($n){ return '$'.number_format($n); }
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>خرج کردن پول بیل گیتس — نسخه قشنگ</title>
<style>
:root{
  --bg:#0d1117; --card:#161b22; --accent:#43c3ff; --text:#e6eef6;
}
body{
  background:var(--bg); color:var(--text);
  font-family:"Vazirmatn", sans-serif; margin:0;
}
.container{max-width:1100px; margin:auto; padding:20px;}
header h1{
  font-size:26px; font-weight:800; color:#fff;
  text-shadow:0 0 12px rgba(67,195,255,0.4);
}
.card{
  background:var(--card); border-radius:15px; padding:20px;
  box-shadow:0 0 20px rgba(0,0,0,0.4);
  backdrop-filter:blur(8px);
}
.big{font-size:32px; font-weight:900; color:var(--accent);}
.grid{display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-top:20px;}
.cat{
  background:#1c222b; padding:14px; border-radius:14px;
  transition:0.2s; cursor:pointer; width:155px; text-align:center;
  border:1px solid transparent;
}
.cat:hover{
  transform:translateY(-6px) scale(1.03);
  border-color:var(--accent);
  box-shadow:0 0 20px rgba(67,195,255,0.25);
}
.cat-icon{font-size:26px;}
button{
  background:var(--accent); border:0; padding:10px 16px;
  border-radius:10px; font-weight:700; cursor:pointer;
}
#spentList{
  max-height:400px; overflow-y:auto;
}
.spent-card{
  background:#13171f; padding:10px 12px; border-radius:10px;
  margin-bottom:8px; display:flex; justify-content:space-between;
  animation:fadeIn 0.25s ease;
}
@keyframes fadeIn{from{opacity:0; transform:translateY(5px);}to{opacity:1;}}
.money-float{
  position:fixed; font-size:22px; pointer-events:none;
  animation:floatUp 0.9s ease forwards;
}
@keyframes floatUp{
  from{opacity:1; transform:translateY(0);} to{opacity:0; transform:translateY(-80px);} }
</style>
</head>
<body>
<div class="container">
<header><h1>💸 خرج کردن پول بیل گیتس — نسخه ارتقاء یافته</h1></header>

<div class="grid">
<main class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div>موجودی فعلی:</div>
            <div class="big" id="total"><?php echo fmt($_SESSION['total']); ?></div>
        </div>
        <div>
            <input id="setTotalInput" type="number" placeholder="تنظیم موجودی" style="padding:8px; border-radius:8px;">
            <button id="setTotalBtn">OK</button>
            <button id="resetBtn" style="background:#ff5e5e;">ریست</button>
        </div>
    </div>

    <h3>دسته‌ها</h3>
    <div style="display:flex; flex-wrap:wrap; gap:12px;">
        <?php foreach($CATEGORIES as $c): ?>
        <div class="cat" data-id="<?php echo $c['id']; ?>">
            <div class="cat-icon"><?php echo $c['icon']; ?></div>
            <div><?php echo $c['name']; ?></div>
            <div style="font-size:12px; opacity:0.7;">
                <?php echo fmt($c['min']); ?> تا <?php echo fmt($c['max']); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h3>آخرین خرج‌ها</h3>
    <div id="spentList">
        <?php foreach($_SESSION['spent_list'] as $e): ?>
            <div class="spent-card">
                <div><?php echo $e['icon'].' '.$e['category']; ?></div>
                <div><?php echo fmt($e['amount']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<aside class="card">
    <button id="autoSpendBtn">خرج خودکار ×10</button>
</aside>

</div></div>

<script>
function qs(s){return document.querySelector(s)}
function post(data){return fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(data)}).then(r=>r.json())}
function fmt(n){return '$'+Number(n).toLocaleString()}

// Spend
for(const c of document.querySelectorAll('.cat')){
  c.onclick=()=>{
    const rect=c.getBoundingClientRect();
    post({action:'spend',category:c.dataset.id}).then(res=>{
      if(!res.ok) return;

      // float animation
      const f=document.createElement('div');
      f.className='money-float';
      f.style.left=(rect.left+rect.width/2)+'px';
      f.style.top=(rect.top)+'px';
      f.textContent='-'+fmt(res.amount);
      document.body.appendChild(f);
      setTimeout(()=>f.remove(),900);

      qs('#total').textContent=fmt(res.total);
      const s=qs('#spentList');
      const el=document.createElement('div');
      el.className='spent-card';
      el.innerHTML=`<div>${res.entry.icon} ${res.entry.category}</div><div>${fmt(res.entry.amount)}</div>`;
      s.prepend(el);
    })
  }
}

qs('#resetBtn').onclick=()=>{post({action:'reset'}).then(r=>{qs('#total').textContent=fmt(r.total); qs('#spentList').innerHTML=''})}
qs('#setTotalBtn').onclick=()=>{post({action:'set_total',total:qs('#setTotalInput').value}).then(r=>{qs('#total').textContent=fmt(r.total); qs('#spentList').innerHTML=''})}
qs('#autoSpendBtn').onclick=async()=>{
  for(let i=0;i<10;i++){
    const cats=[...document.querySelectorAll('.cat')];
    cats[Math.floor(Math.random()*cats.length)].click();
    await new Promise(r=>setTimeout(r,250));
  }
}
</script>
</body>
</html>
