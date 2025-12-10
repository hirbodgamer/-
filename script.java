// External JS for Spent Bill Gates Money
let total = 100000000000;
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
