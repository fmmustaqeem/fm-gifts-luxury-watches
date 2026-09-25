import crypto from "node:crypto";
function secret(){const value=process.env.SESSION_SECRET;if(!value)throw new Error("SESSION_SECRET is required");return value}
function sign(p){const b=Buffer.from(JSON.stringify(p)).toString("base64url");return b+"."+crypto.createHmac("sha256",secret()).update(b).digest("base64url")}
function verify(t){const [b,s]=String(t||"").split(".");if(!b||!s)return null;const e=crypto.createHmac("sha256",secret()).update(b).digest("base64url");if(s.length!==e.length||!crypto.timingSafeEqual(Buffer.from(s),Buffer.from(e)))return null;const p=JSON.parse(Buffer.from(b,"base64url").toString());return p.exp>Date.now()?p:null}
export function createSession(user){return sign({sub:user.id,role:user.role||"customer",tier:user.tier||"free",exp:Date.now()+28800000})}
export function requireAuth(req){const h=req?.headers?.authorization||"";const p=h.startsWith("Bearer ")?verify(h.slice(7)):null;return p?{ok:true,user:p}:{ok:false,status:401,error:"Authentication required"}}
export function requireAdmin(req){const r=requireAuth(req);return r.ok&&["admin","owner"].includes(r.user.role)?r:{ok:false,status:r.status||403,error:r.error||"Admin access required"}}
