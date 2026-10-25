const menuButton=document.getElementById("menuButton");
const navigation=document.getElementById("navigation");
if(menuButton&&navigation){
  menuButton.addEventListener("click",()=>{const open=navigation.classList.toggle("is-open");menuButton.setAttribute("aria-expanded",String(open));});
  navigation.addEventListener("click",event=>{if(event.target.closest("a")){navigation.classList.remove("is-open");menuButton.setAttribute("aria-expanded","false");}});
}
const year=document.getElementById("year");if(year)year.textContent=new Date().getFullYear();

/* Accessible hero carousel: pauses on hover/focus and respects reduced motion. */
const slides=[...document.querySelectorAll(".hero-slide")];
const dots=[...document.querySelectorAll("[data-go]")];
let currentSlide=0;
function showSlide(index){
  if(!slides.length)return;
  currentSlide=(index+slides.length)%slides.length;
  slides.forEach((slide,i)=>{slide.classList.toggle("active",i===currentSlide);slide.setAttribute("aria-hidden",String(i!==currentSlide));});
  dots.forEach((dot,i)=>{dot.classList.toggle("active",i===currentSlide);dot.setAttribute("aria-pressed",String(i===currentSlide));});
}
document.getElementById("heroPrev")?.addEventListener("click",()=>showSlide(currentSlide-1));
document.getElementById("heroNext")?.addEventListener("click",()=>showSlide(currentSlide+1));
dots.forEach(dot=>dot.addEventListener("click",()=>showSlide(Number(dot.dataset.go))));
const heroSlider=document.getElementById("heroSlider");
const reduceMotion=window.matchMedia("(prefers-reduced-motion: reduce)").matches;
let heroTimer=null;
function startHero(){if(!reduceMotion&&slides.length>1&&!heroTimer)heroTimer=setInterval(()=>showSlide(currentSlide+1),6500);}
function stopHero(){if(heroTimer){clearInterval(heroTimer);heroTimer=null;}}
heroSlider?.addEventListener("mouseenter",stopHero);heroSlider?.addEventListener("mouseleave",startHero);
heroSlider?.addEventListener("focusin",stopHero);heroSlider?.addEventListener("focusout",startHero);
showSlide(0);startHero();

/* Horizontal category and new-arrival rails. */
function scrollRail(id,direction){const rail=document.getElementById(id);if(rail)rail.scrollBy({left:direction*Math.max(240,rail.clientWidth*.72),behavior:reduceMotion?"auto":"smooth"});}
document.getElementById("categoryPrev")?.addEventListener("click",()=>scrollRail("categoryRail",-1));
document.getElementById("categoryNext")?.addEventListener("click",()=>scrollRail("categoryRail",1));
document.getElementById("arrivalPrev")?.addEventListener("click",()=>scrollRail("arrivalRail",-1));
document.getElementById("arrivalNext")?.addEventListener("click",()=>scrollRail("arrivalRail",1));

const categoryFilter=document.getElementById("categoryFilter");
const searchInput=document.getElementById("searchInput");
const shopSearch=document.getElementById("shopSearch");
const productGrid=document.getElementById("productGrid");
let catalogue=[];
function makeEmptyState(title,message){
  const box=document.createElement("div");box.className="product-empty";
  const mark=document.createElement("span");mark.className="empty-symbol";mark.textContent="FM";
  const heading=document.createElement("h3");heading.textContent=title;
  const copy=document.createElement("p");copy.textContent=message;
  box.append(mark,heading,copy);return box;
}
function renderProducts(){
  if(!productGrid)return;
  const query=(shopSearch?.value||searchInput?.value||"").trim().toLocaleLowerCase();
  const category=categoryFilter?.value||"";
  const products=catalogue.filter(product=>(!category||product.category===category)&&(!query||[product.name,product.brand,product.category,product.subcategory].filter(Boolean).join(" ").toLocaleLowerCase().includes(query)));
  productGrid.replaceChildren();
  if(!products.length){
    productGrid.append(makeEmptyState(catalogue.length?"No products match your search.":"Your collection starts here.","The product catalogue is awaiting verified inventory. Product names, photos, prices and stock will be shown after they are checked. No sample listings or invented prices are published."));
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
categoryFilter?.addEventListener("change",renderProducts);
shopSearch?.addEventListener("input",()=>{if(searchInput)searchInput.value=shopSearch.value;renderProducts();});
searchInput?.addEventListener("input",()=>{if(shopSearch)shopSearch.value=searchInput.value;renderProducts();});
document.getElementById("searchForm")?.addEventListener("submit",event=>{event.preventDefault();if(shopSearch)shopSearch.value=searchInput?.value||"";document.getElementById("shop")?.scrollIntoView({behavior:reduceMotion?"auto":"smooth"});renderProducts();});
document.querySelectorAll("[data-category]").forEach(button=>button.addEventListener("click",()=>{if(categoryFilter)categoryFilter.value=button.dataset.category;renderProducts();document.getElementById("shop")?.scrollIntoView({behavior:reduceMotion?"auto":"smooth"});}));
fetch("data/catalog.json",{headers:{"Accept":"application/json"}}).then(response=>{if(!response.ok)throw new Error("catalogue unavailable");return response.json();}).then(data=>{catalogue=Array.isArray(data.products)?data.products:[];renderProducts();}).catch(()=>{catalogue=[];renderProducts();});
