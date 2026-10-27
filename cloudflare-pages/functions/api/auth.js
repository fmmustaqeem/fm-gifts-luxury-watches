const ITERATIONS=210000, KEYLEN=256;
const enc=new TextEncoder();
function b64(bytes){let s="";for(const b of bytes)s+=String.fromCharCode(b);return btoa(s).replace(/\+/g,"-").replace(/\//g,"_").replace(/=+$/,"")}
function hex(bytes){return [...new Uint8Array(bytes)].map(b=>b.toString(16).padStart(2,"0")).join("")}
function bytesFromHex(s){const a=new Uint8Array(s.length/2);for(let i=0;i<a.length;i++)a[i]=parseInt(s.slice(i*2,i*2+2),16);return a}
async function key(password,salt){return crypto.subtle.importKey("raw",enc.encode(password),"PBKDF2",false,["deriveBits"])}
export async function hashPassword(password){if(typeof password!=="string"||password.length<8)throw new Error("Password must be at least 8 characters");const salt=crypto.getRandomValues(new Uint8Array(16));const k=await key(password,salt);const bits=await crypto.subtle.deriveBits({name:"PBKDF2",salt,iterations:ITERATIONS,hash:"SHA-256"},k,KEYLEN);return `pbkdf2_sha256$${ITERATIONS}$${hex(salt)}$${hex(bits)}`}
export async function verifyPassword(password,stored){if(typeof password!=="string"||typeof stored!=="string")return false;const [scheme,it,salt,hash]=stored.split("$");if(scheme!=="pbkdf2_sha256"||Number(it)!==ITERATIONS||!salt||!hash)return false;const k=await key(password,bytesFromHex(salt));const bits=await crypto.subtle.deriveBits({name:"PBKDF2",salt:bytesFromHex(salt),iterations:Number(it),hash:"SHA-256"},k,KEYLEN);const a=new Uint8Array(bits),b=bytesFromHex(hash);return a.length===b.length&&a.every((v,i)=>v===b[i])}
async function hmac(secret,data){const k=await crypto.subtle.importKey("raw",enc.encode(secret),{name:"HMAC",hash:"SHA-256"},false,["sign"]);return b64(await crypto.subtle.sign("HMAC",k,enc.encode(data)))}
export async function createSession(user,secret){const payload={sub:user.id,role:user.role||"customer",tier:user.tier||"free",exp:Date.now()+28800000};const body=b64(enc.encode(JSON.stringify(payload)));return body+"."+await hmac(secret,body)}
export function normalizeEmail(email){return String(email||"").trim().toLowerCase()}
export async function parseJson(request){try{return await request.json()}catch{return {}}}
export function json(data,status=200){return new Response(JSON.stringify(data),{status,headers:{"content-type":"application/json"}})}
