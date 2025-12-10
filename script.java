const totalEl=document.getElementById('total');
const spentList=document.getElementById('spentList');
const items=document.querySelectorAll('.item');
let total=parseInt(totalEl.innerText.replace(/\$|,/g,''));

items.forEach(item=>{
  item.onclick=()=>{
    const price=parseInt(item.dataset.price);
    if(total>=price){
      total-=price;
      totalEl.innerText='$'+total.toLocaleString();
      const div=document.createElement('div');
      div.className='spent-card';
      div.innerHTML=`<span>${item.innerText}</span><b>$${price.toLocaleString()}</b>`;
      spentList.prepend(div);
      const f=document.createElement('div');
      f.className='money-float';
      f.innerText='-$'+price.toLocaleString();
      f.style.left=Math.random()*80+'vw';
      f.style.top='70vh';
      document.body.appendChild(f);
      setTimeout(()=>f.remove(),900);
    } else alert('پول کافی نیست!');
  }
});
