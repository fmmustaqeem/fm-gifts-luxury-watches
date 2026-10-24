const menuButton=document.getElementById("menuButton");
const navigation=document.getElementById("navigation");
menuButton.addEventListener("click",()=>{const open=navigation.classList.toggle("open");menuButton.setAttribute("aria-expanded",String(open));});
navigation.addEventListener("click",event=>{if(event.target.closest("a")){navigation.classList.remove("open");menuButton.setAttribute("aria-expanded","false");}});
document.getElementById("year").textContent=new Date().getFullYear();
const categoryFilter=document.getElementById("categoryFilter");
const searchInput=document.getElementById("searchInput");
const productGrid=document.getElementById("productGrid");
let catalogue=[];
function makeEmptyState(title,message){
  const box=document.createElement("div");box.className="empty-state";
  const mark=document.createElement("span");mark.className="empty-mark";mark.textContent="FM";
  const heading=document.createElement("h3");heading.textContent=title;
  const copy=document.createElement("p");copy.textContent=message;
  box.append(mark,heading,copy);return box;
}
function renderProducts(){
  const query=searchInput.value.trim().toLocaleLowerCase();
  const category=categoryFilter.value;
  const products=catalogue.filter(product=>(!category||product.category===category)&&(!query||[product.name,product.brand,product.category,product.subcategory].filter(Boolean).join(" ").toLocaleLowerCase().includes(query)));
  productGrid.replaceChildren();
  if(!products.length){
    productGrid.append(makeEmptyState(catalogue.length?"No products match your search.":"Our collection is being prepared.",catalogue.length?"Try another keyword or category.":"Verified product names, photos, prices and stock will appear here when the inventory is connected. No sample prices or invented products are published."));
    return;
  }
  for(const product of products){
    const card=document.createElement("article");card.className="product-card";
    const visual=document.createElement("div");visual.className="product-image";
    if(product.image){const image=document.createElement("img");image.src=product.image;image.alt=product.name||"FM Gifts product";image.loading="lazy";visual.append(image);}
    else{visual.textContent="FM";visual.setAttribute("aria-label","Product image not provided");}
    const details=document.createElement("div");details.className="product-details";
    const categoryText=document.createElement("span");categoryText.className="product-category";categoryText.textContent=product.category||"Collection";
    const name=document.createElement("h3");name.textContent=product.name||"Product";
    const price=document.createElement("p");price.className="product-price";price.textContent=Number.isFinite(product.price)?"PKR "+product.price.toLocaleString("en-PK"):"Price to be confirmed";
    details.append(categoryText,name,price);card.append(visual,details);productGrid.append(card);
  }
}
categoryFilter.addEventListener("change",renderProducts);
searchInput.addEventListener("input",renderProducts);
document.querySelectorAll("[data-category]").forEach(button=>button.addEventListener("click",()=>{categoryFilter.value=button.dataset.category;renderProducts();document.getElementById("catalogue").scrollIntoView({behavior:"smooth"});}));
fetch("/data/catalog.json",{headers:{"Accept":"application/json"}}).then(response=>{if(!response.ok)throw new Error("catalogue unavailable");return response.json();}).then(data=>{catalogue=Array.isArray(data.products)?data.products:[];renderProducts();}).catch(()=>{catalogue=[];renderProducts();});
