(() => {
 const $=id=>document.getElementById(id), money=n=>"PKR "+Number(n).toLocaleString("en-PK");
 const read=()=>{try{return JSON.parse(localStorage.getItem("fm-cart")||"[]");}catch{return[];}};
 const save=a=>{localStorage.setItem("fm-cart",JSON.stringify(a));render();};
 function render(){const cart=read(),root=$("cartRoot");root.replaceChildren();if(!cart.length){const p=document.createElement("p");p.textContent="Your cart is empty.";root.append(p);return;}
 const table=document.createElement("div");table.className="cart-items";let total=0;
 cart.forEach((item,i)=>{const row=document.createElement("div");row.className="cart-row";const title=document.createElement("div");const h=document.createElement("strong");h.textContent=item.name;const sku=document.createElement("small");sku.textContent="SKU: "+item.sku;title.append(h,sku);
 const price=document.createElement("span");price.textContent=money(item.price);
 const qty=document.createElement("input");qty.type="number";qty.min="1";qty.max="99";qty.value=String(Math.max(1,Number(item.qty)||1));qty.setAttribute("aria-label","Quantity for "+item.name);qty.addEventListener("change",()=>{const a=read();a[i].qty=Math.min(99,Math.max(1,parseInt(qty.value,10)||1));save(a);});
 const subtotal=document.createElement("strong");const amount=Number(item.price)*Math.max(1,Number(item.qty)||1);total+=amount;subtotal.textContent=money(amount);
 const remove=document.createElement("button");remove.className="button outline";remove.textContent="Remove";remove.addEventListener("click",()=>{const a=read();a.splice(i,1);save(a);});
 const info=document.createElement("div");info.className="cart-line-total";info.append(subtotal,remove);row.append(title,price,qty,info);table.append(row);});
 const sum=document.createElement("p");sum.className="cart-total";sum.textContent="Subtotal: "+money(total)+" · Delivery confirmed separately";root.append(table,sum);
 }
 render();
})();