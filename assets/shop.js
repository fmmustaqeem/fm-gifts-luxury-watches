(() => {
  const $ = id => document.getElementById(id);
  const money = n => "PKR " + Number(n).toLocaleString("en-PK");
  const readCart = () => { try { return JSON.parse(localStorage.getItem("fm-cart") || "[]"); } catch { return []; } };
  const updateCount = () => { const el=$("cartCount"); if(el) el.textContent=readCart().reduce((n,x)=>n+Math.max(0,Number(x.qty)||0),0); };
  const root=$("productGrid"); let products=[];
  function empty(title,copy){const box=document.createElement("div");box.className="empty-state";const h=document.createElement("h2");h.textContent=title;const p=document.createElement("p");p.textContent=copy;box.append(h,p);return box;}
  function render(){
    const q=$("searchInput").value.trim().toLowerCase(),cat=$("categoryFilter").value;
    const list=products.filter(p=>p.status==="Active"&&(!cat||p.category===cat)&&(!q||[p.name,p.brand,p.category,p.sku].filter(Boolean).join(" ").toLowerCase().includes(q)));
    root.replaceChildren();
    if(!list.length){root.append(empty(products.length?"No matching products":"Collection coming soon",products.length?"Try a different search or category.":"No verified active products have been published yet. The catalogue will populate when approved inventory is added."));return;}
    for(const p of list){
      const card=document.createElement("article");card.className="product-card";
      const visual=document.createElement("div");visual.className="product-image";
      if(p.image){const img=document.createElement("img");img.src=p.image;img.alt=p.name;img.loading="lazy";visual.append(img);}else visual.textContent="FM";
      const details=document.createElement("div");details.className="product-details";
      const category=document.createElement("span");category.className="product-category";category.textContent=p.category||"Collection";
      const inventoryLabel=document.createElement("span");inventoryLabel.className="demo-label";inventoryLabel.textContent=String(p.sku||"").startsWith("DEMO-")?"DEMO ITEM — NOT VERIFIED STOCK":"LISTING — STOCK TO CONFIRM";
      const name=document.createElement("h3");name.textContent=p.name;
      const price=document.createElement("p");price.className="product-price";price.textContent=Number.isFinite(Number(p.price))?money(p.price):"Price to be confirmed";
      const add=document.createElement("button");add.className="button gold add-button";add.textContent="Add to cart";add.disabled=!p.sku||!Number.isFinite(Number(p.price))||Number(p.price)<0;
      const addToCart=()=>{const cart=readCart(),old=cart.find(x=>x.sku===p.sku);if(old)old.qty=Math.min(99,(Number(old.qty)||1)+1);else cart.push({sku:p.sku,name:p.name,price:Number(p.price),qty:1});localStorage.setItem("fm-cart",JSON.stringify(cart));updateCount();return cart;};
      add.addEventListener("click",()=>{addToCart();add.textContent="Added ✓";setTimeout(()=>add.textContent="Add to cart",1000);});
      const buy=document.createElement("button");buy.className="button outline buy-now-button";buy.textContent="Buy now";buy.disabled=add.disabled;buy.addEventListener("click",()=>{addToCart();window.location.href="store/checkout.html";});
      const actions=document.createElement("div");actions.className="product-actions";actions.append(add,buy);
      details.append(category,inventoryLabel,name,price,actions);card.append(visual,details);root.append(card);
    }
  }
  $("searchInput").addEventListener("input",render);$("categoryFilter").addEventListener("change",render);
  const year=$("year");if(year)year.textContent=new Date().getFullYear();
  const menu=$("menuButton"),nav=$("navigation");if(menu&&nav)menu.addEventListener("click",()=>{const opened=nav.classList.toggle("is-open");menu.setAttribute("aria-expanded",String(opened));});
  fetch("data/catalog.json",{headers:{Accept:"application/json"}}).then(r=>{if(!r.ok)throw Error("catalog unavailable");return r.json();}).then(data=>{products=Array.isArray(data.products)?data.products:[];render();}).catch(()=>root.replaceChildren(empty("Catalogue unavailable","The product catalogue could not be loaded. Please try again later.")));
  updateCount();
})();