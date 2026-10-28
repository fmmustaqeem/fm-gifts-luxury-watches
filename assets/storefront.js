const menuButton=document.getElementById("menuButton");
const navigation=document.getElementById("navigation");
if(menuButton&&navigation){
  menuButton.addEventListener("click",()=>{const open=navigation.classList.toggle("is-open");menuButton.setAttribute("aria-expanded",String(open));});
  navigation.addEventListener("click",event=>{if(event.target.closest("a")){navigation.classList.remove("is-open");menuButton.setAttribute("aria-expanded","false");}});
}
const year=document.getElementById("year");if(year)year.textContent=new Date().getFullYear();
const categoryFilter=document.getElementById("categoryFilter");
const searchInput=document.getElementById("searchInput");
const productGrid=document.getElementById("productGrid");
const arrivalsRail=document.getElementById("newArrivalsRail");
let catalogue=[];
function makeEmptyState(title,message){
  const box=document.createElement("div");box.className="empty-state";
  const mark=document.createElement("span");mark.className="empty-mark";mark.textContent="FM";
  const heading=document.createElement("h3");heading.textContent=title;
  const copy=document.createElement("p");copy.textContent=message;
  box.append(mark,heading,copy);return box;
}
function makeProductCard(product){
  const card=document.createElement("article");card.className="product-card";
  const visual=document.createElement("div");visual.className="product-image";
  if(product.image){const image=document.createElement("img");image.src=product.image;image.alt=product.name||"FM Gifts product";image.loading="lazy";visual.append(image);}
  else{visual.textContent="FM";visual.setAttribute("aria-label","Product image not provided");}
  const details=document.createElement("div");details.className="product-details";
  const categoryText=document.createElement("span");categoryText.className="product-category";categoryText.textContent=product.category||"Collection";
  const name=document.createElement("h3");name.textContent=product.name||"Product";
  const price=document.createElement("p");price.className="product-price";price.textContent=Number.isFinite(Number(product.price))?"PKR "+Number(product.price).toLocaleString("en-PK"):"Price to be confirmed";
  details.append(categoryText,name,price);
  if(Array.isArray(product.color_variations)&&product.color_variations.length){
    const colorWrap=document.createElement("label");colorWrap.className="product-color-wrap";
    const colorLabel=document.createElement("span");colorLabel.className="product-color-label";colorLabel.textContent="Available colour";
    const colorSelect=document.createElement("select");colorSelect.className="product-color-select";colorSelect.setAttribute("aria-label","Choose colour for "+(product.name||"product"));
    product.color_variations.forEach(color=>{const option=document.createElement("option");option.value=color;option.textContent=color;colorSelect.append(option);});
    colorWrap.append(colorLabel,colorSelect);details.append(colorWrap);
  }
  if(product.sku&&product.sku.startsWith("DEMO-")){const demo=document.createElement("span");demo.className="demo-label";demo.textContent="DEMO ITEM";details.append(demo);}
  card.append(visual,details);return card;
}
function renderProducts(){
  if(!productGrid)return;
  const query=searchInput.value.trim().toLocaleLowerCase();
  const category=categoryFilter.value;
  const products=catalogue.filter(product=>product.status==="Active"&&(!category||product.category===category)&&(!query||[product.name,product.brand,product.category,product.subcategory,product.sku].filter(Boolean).join(" ").toLocaleLowerCase().includes(query)));
  productGrid.replaceChildren();
  if(!products.length){productGrid.append(makeEmptyState(catalogue.length?"No products match your search.":"Our collection is being prepared.",catalogue.length?"Try another keyword or category.":"Products from the shop will appear here when the shared catalogue is available."));return;}
  products.forEach(product=>productGrid.append(makeProductCard(product)));
}
function renderArrivals(){
  if(!arrivalsRail)return;
  arrivalsRail.replaceChildren();
  const active=catalogue.filter(product=>product.status==="Active");
  const arrivals=active.slice(-8).reverse();
  if(!arrivals.length){arrivalsRail.append(makeEmptyState("New arrivals coming soon","Newly added shop products will be featured here."));return;}
  arrivals.forEach(product=>arrivalsRail.append(makeProductCard(product)));
}
if(categoryFilter&&searchInput){categoryFilter.addEventListener("change",renderProducts);searchInput.addEventListener("input",renderProducts);}
document.querySelectorAll("[data-category]").forEach(button=>button.addEventListener("click",()=>{if(categoryFilter&&productGrid){categoryFilter.value=button.dataset.category;renderProducts();document.getElementById("catalogue").scrollIntoView({behavior:window.matchMedia("(prefers-reduced-motion: reduce)").matches?"auto":"smooth"});}}));
document.querySelectorAll("[data-rail]").forEach(button=>button.addEventListener("click",()=>{const rail=document.getElementById(button.dataset.rail);if(rail)rail.scrollBy({left:Number(button.dataset.direction)*rail.clientWidth*.85,behavior:window.matchMedia("(prefers-reduced-motion: reduce)").matches?"auto":"smooth"});}));
fetch("data/catalog.json",{headers:{"Accept":"application/json"}}).then(response=>{if(!response.ok)throw new Error("catalogue unavailable");return response.json();}).then(data=>{catalogue=Array.isArray(data.products)?data.products:[];renderProducts();renderArrivals();}).catch(()=>{catalogue=[];renderProducts();renderArrivals();});
