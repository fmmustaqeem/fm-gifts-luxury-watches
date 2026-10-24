(() => {
 const $=id=>document.getElementById(id),money=n=>"PKR "+Number(n).toLocaleString("en-PK");
 let cart=[];try{cart=JSON.parse(localStorage.getItem("fm-cart")||"[]");}catch{}
 const root=$("checkoutSummary");root.replaceChildren();
 if(!cart.length){const p=document.createElement("p");p.textContent="Your cart is empty.";root.append(p);}else{
  let total=0;cart.forEach(item=>{const row=document.createElement("p");row.textContent=item.name+" × "+Math.max(1,Number(item.qty)||1)+" — "+money(Number(item.price)*Math.max(1,Number(item.qty)||1));root.append(row);total+=Number(item.price)*Math.max(1,Number(item.qty)||1);});
  const sum=document.createElement("strong");sum.textContent="Subtotal: "+money(total);root.append(sum);
 }
 const form=$("checkoutForm");form.addEventListener("submit",e=>{e.preventDefault();$("checkoutMessage").textContent="Order submission is disabled. No order was sent or saved; secure backend integration and testing are required first.";});
})();