export const FM_STORE_FEED = [
  {
    id: "fm-primary-karachi",
    name: "FM Gifts & Luxury Watches",
    role: "retailer",
    city: "Karachi",
    area: "Gulistan-e-Johar",
    address: "Shop #75, Baseera Shopping Centre, Block 17, Gulistan-e-Johar, Karachi",
    phone: "0312-2972922",
    whatsapp: "923122972922",
    verified: true,
    products: [
      {name:"J. Men EDP",category:"Perfume",subcategory:"Men",brand:"J. Fragrances",sourcePrice:null,image:""},
      {name:"Bonanza Women EDP",category:"Perfume",subcategory:"Women",brand:"Bonanza Satrangi",sourcePrice:null,image:""},
      {name:"Rasasi Unisex",category:"Perfume",subcategory:"Unisex",brand:"Rasasi",sourcePrice:null,image:""},
      {name:"Lattafa Gift Set",category:"Perfume",subcategory:"Gift Set",brand:"Lattafa",sourcePrice:null,image:""},
      {name:"Armaf Fragrance",category:"Perfume",subcategory:"Unisex",brand:"Armaf",sourcePrice:null,image:""}
    ]
  }
];

export const FM_MARGIN_RATE = 0.20;
export const fmWhatsAppUrl = phone => phone ? "https://wa.me/" + String(phone).replace(/\D/g,"") : "";
export const fmCustomerPrice = sourcePrice => {
  const n = Number(sourcePrice);
  return Number.isFinite(n) ? Math.ceil(n * (1 + FM_MARGIN_RATE)) : null;
};
export const FM_FEED_PRODUCTS = FM_STORE_FEED.flatMap(store =>
  (store.products || []).map(p => ({
    ...p,
    storeId: store.id,
    storeName: store.name,
    storeRole: store.role,
    storeCity: store.city,
    storeArea: store.area,
    storeAddress: store.address,
    storePhone: store.phone,
    storeWhatsapp: store.whatsapp,
    storeVerified: store.verified,
    marginRate: FM_MARGIN_RATE,
    price: fmCustomerPrice(p.sourcePrice)
  }))
);
