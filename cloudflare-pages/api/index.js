// Production API boundary foundation.
// Deploy behind an authenticated server runtime with DATABASE_URL.
// Never expose DATABASE_URL or admin credentials to the browser.
export default async function handler(req,res){
  const method=req.method||"GET";
  const path=req.url||"/";
  if(method==="GET"&&path.startsWith("/api/products")) return res.status(501).json({ok:false,message:"Database adapter required",endpoint:"GET /api/products"});
  if(method==="POST"&&path==="/api/orders") return res.status(501).json({ok:false,message:"Database adapter required",endpoint:"POST /api/orders"});
  if(method==="GET"&&path.startsWith("/api/orders/")) return res.status(501).json({ok:false,message:"Authenticated database adapter required",endpoint:"GET /api/orders/:id"});
  if(method==="PATCH"&&path.startsWith("/api/admin/orders/")) return res.status(501).json({ok:false,message:"Admin authentication and database adapter required",endpoint:"PATCH /api/admin/orders/:id"});
  return res.status(404).json({ok:false,message:"Not found"});
}
