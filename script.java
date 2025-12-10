// External JS for Spent Bill Gates Money
let total = 5000000000;
let remaining = total;
let spentItems = [];

function buyItem(name, price) {
  if (remaining >= price) {
    remaining -= price;
    spentItems.push({ name, price });
    updateUI();
    moneyFloat(price);
  }
}

function updateUI() {
  document.getElementById("remaining").innerText = remaining.toLocaleString();
  let list = document.getElementById("spentList");
  list.innerHTML = "";

  spentItems.forEach(item => {
    let div = document.createElement("div");
    div.className = "spent-card";
    div.innerHTML = `<span>${item.name}</span><b>${item.price.toLocaleString()}$</b>`;
    list.appendChild(div);
  });
}

function moneyFloat(amount) {
  let float = document.createElement("div");
  float.className = "money-float";
  float.innerText = `-${amount}$`;
  float.style.left = (Math.random() * 80 + 10) + "vw";
  float.style.top = "70vh";
  document.body.appendChild(float);

  setTimeout(() => float.remove(), 900);
}

window.onload = updateUI;

// Auto-generated 50 items
$items = [];
for ($i = 1; $i <= 50; $i++) {
    $items[] = [
        "name" => "آیتم شماره $i",
        "price" => rand(50, 500000000)
    ];
}



// 50 real items
$items = [
    ["name"=>"آیفون ۱۵ پرو", "price"=>65000],
    ["name"=>"گلکسی S24 اولترا", "price"=>72000],
    ["name"=>"ایرپاد پرو 2", "price"=>9000],
    ["name"=>"هدفون سونی XM5", "price"=>12000],
    ["name"=>"پلی‌استیشن 5", "price"=>25000],
    ["name"=>"ایکس‌باکس سری X", "price"=>22000],
    ["name"=>"نینتندو سوییچ", "price"=>15000],
    ["name"=>"لپ‌تاپ گیمینگ MSI", "price"=>45000],
    ["name"=>"مک‌بوک پرو M3", "price"=>78000],
    ["name"=>"مانیتور 4K سامسونگ", "price"=>18000],
    ["name"=>"کیبورد مکانیکال", "price"=>4000],
    ["name"=>"ماوس گیمینگ", "price"=>3000],
    ["name"=>"صندلی گیمینگ", "price"=>8000],
    ["name"=>"کارت گرافیک RTX 4090", "price"=>90000],
    ["name"=>"پرینتر لیزری", "price"=>5000],
    ["name"=>"اسکوتر برقی", "price"=>16000],
    ["name"=>"دوچرخه کوهستان", "price"=>14000],
    ["name"=>"تلویزیون 75 اینچ", "price"=>60000],
    ["name"=>"پرچم گیمینگ RGB", "price"=>2000],
    ["name"=>"پاوربانک 30000", "price"=>2500],
    ["name"=>"کمپیوتر کامل گیمینگ", "price"=>70000],
    ["name"=>"میز گیمینگ", "price"=>9000],
    ["name"=>"کولر گازی", "price"=>15000],
    ["name"=>"یخچال", "price"=>18000],
    ["name"=>"ماشین لباسشویی", "price"=>13000],
    ["name"=>"مایکروویو", "price"=>4000],
    ["name"=>"ساعت هوشمند", "price"=>7000],
    ["name"=>"دوربین حرفه‌ای", "price"=>35000],
    ["name"=>"پهپاد DJI", "price"=>28000],
    ["name"=>"سرور خانگی", "price"=>30000],
    ["name"=>"کیس RGB", "price"=>6000],
    ["name"=>"موس پد XXL", "price"=>1000],
    ["name"=>"ماشین کنترلی", "price"=>7000],
    ["name"=>"لباس ورزشی", "price"=>2000],
    ["name"=>"کتونی نایک", "price"=>3500],
    ["name"=>"کیف مدرسه", "price"=>1500],
    ["name"=>"کتابخانه", "price"=>5000],
    ["name"=>"گلدان بزرگ", "price"=>800],
    ["name"=>"ساعت دیواری", "price"=>600],
    ["name"=>"آباژور", "price"=>900],
    ["name"=>"پرده جدید", "price"=>1200],
    ["name"=>"قهوه‌ساز", "price"=>3000],
    ["name"=>"چای‌ساز", "price"=>2000],
    ["name"=>"هارد اکسترنال", "price"=>2500],
    ["name"=>"SSD پرسرعت", "price"=>4000],
    ["name"=>"میکروفون استریم", "price"=>3500],
    ["name"=>"وب‌کم HD", "price"=>2000],
    ["name"=>"پرژکتور خانگی", "price"=>6000]
];
