(() => {
 const $=id=>document.getElementById(id),money=n=>"PKR "+Number(n).toLocaleString("en-PK");
 const readCart=()=>{try{return JSON.parse(localStorage.getItem("fm-cart")||"[]");}catch{return[];}};
 const renderSummary=()=>{
  const root=$("checkoutSummary");if(!root)return;const cart=readCart();root.replaceChildren();
  if(!cart.length){const p=document.createElement("p");p.textContent="Your cart is empty.";root.append(p);return;}
  let total=0;cart.forEach(item=>{const row=document.createElement("p");row.textContent=item.name+" × "+Math.max(1,Number(item.qty)||1)+" — "+money(Number(item.price)*Math.max(1,Number(item.qty)||1));root.append(row);total+=Number(item.price)*Math.max(1,Number(item.qty)||1);});
  const sum=document.createElement("strong");sum.textContent="Subtotal: "+money(total);root.append(sum);
 };
 renderSummary();
 const form=$("checkoutForm");if(form)form.addEventListener("submit",e=>{e.preventDefault();$("checkoutMessage").textContent="Order submission is disabled. No order was sent or saved; secure backend integration and testing are required first.";});
 const rail=$("checkoutSlides");let products=[],offset=0;
 const addItem=p=>{const cart=readCart(),old=cart.find(x=>x.sku===p.sku);if(old)old.qty=Math.min(99,(Number(old.qty)||1)+1);else cart.push({sku:p.sku,name:p.name,price:Number(p.price),qty:1});localStorage.setItem("fm-cart",JSON.stringify(cart));renderSummary();};
 const renderSlides=()=>{
  if(!rail)return;rail.replaceChildren();
  const available=products.filter(p=>p.status==="Active"&&p.sku&&Number.isFinite(Number(p.price))&&Number(p.price)>=0);
  if(!available.length){const p=document.createElement("p");p.textContent="No products available to show right now.";rail.append(p);return;}
  const visible=Array.from({length:Math.min(8,available.length)},(_,i)=>available[(offset+i)%available.length]);
  visible.forEach(p=>{
   const card=document.createElement("article");card.className="checkout-slide-card";
   const visual=document.createElement("div");visual.className="checkout-slide-image";
   if(p.image){const img=document.createElement("img");img.src=p.image;img.alt=p.name;img.loading="lazy";visual.append(img);}else visual.textContent="FM";
   const title=document.createElement("h3");title.textContent=p.name;
   const category=document.createElement("small");category.textContent=p.category||"Collection";
   const price=document.createElement("strong");price.textContent=money(p.price);
   const button=document.createElement("button");button.className="button gold";button.textContent="Add to cart";button.addEventListener("click",()=>{addItem(p);button.textContent="Added ✓";setTimeout(()=>button.textContent="Add to cart",1000);});
   card.append(visual,category,title,price,button);rail.append(card);
  });
 };
 document.querySelectorAll("[data-checkout-slide]").forEach(button=>button.addEventListener("click",()=>{offset=(offset+Number(button.dataset.checkoutSlide)+Math.max(1,products.length))%Math.max(1,products.length);renderSlides();}));
 fetch("../data/catalog.json",{headers:{Accept:"application/json"}}).then(r=>{if(!r.ok)throw Error("catalog unavailable");return r.json();}).then(data=>{products=Array.isArray(data.products)?data.products:[];renderSlides();}).catch(()=>{if(rail)rail.textContent="Product suggestions are temporarily unavailable.";});
})();