/* FM Gifts — supplier/WhatsApp product intake seed.
   Admin-only research data. Customer storefront must consume only approved products. */
const FM_SUPPLIER_FEED = [
  {
    id:"gharri-pk-reseller",
    network:"Gharri.Pk Reseller",
    group:"Ghari.pk reseller 31",
    channel:"WhatsApp Community",
    groupImageUrl:"",
    memberCount:56,
    joined:"2022-01-16",
    verification:"research",
    latestAnnouncement:{text:"NEW ARRIV...",date:"2026-09-22"},
    products:[]
  }
];

const FM_PRODUCT_STATES=["pending","approved","published","rejected"];
const FM_MARGIN_RATE=0.20;
function fmCustomerPrice(sourcePrice){
  const n=Number(sourcePrice);
  return Number.isFinite(n)?Math.ceil(n*(1+FM_MARGIN_RATE)):null;
}
function fmSupplierProduct(candidate){
  return {
    id:candidate.id||("sp-"+Date.now()),
    supplierId:candidate.supplierId||"gharri-pk-reseller",
    sourceGroup:candidate.sourceGroup||"Ghari.pk reseller 31",
    name:candidate.name||"",
    brand:candidate.brand||"",
    category:candidate.category||"",
    subcategory:candidate.subcategory||"",
    image:candidate.image||"",
    sourcePrice:candidate.sourcePrice??null,
    customerPrice:fmCustomerPrice(candidate.sourcePrice),
    stock:candidate.stock||"unknown",
    sourcePost:candidate.sourcePost||"",
    capturedAt:candidate.capturedAt||new Date().toISOString(),
    state:candidate.state||"pending"
  };
}
