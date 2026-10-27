import crypto from "node:crypto";

const ITERATIONS=210000;
const KEYLEN=32;
const DIGEST="sha256";

export function hashPassword(password){
  if(typeof password!=="string"||password.length<8) throw new Error("Password must be at least 8 characters");
  const salt=crypto.randomBytes(16).toString("hex");
  const hash=crypto.pbkdf2Sync(password,salt,ITERATIONS,KEYLEN,DIGEST).toString("hex");
  return `pbkdf2_${DIGEST}$${ITERATIONS}$${salt}$${hash}`;
}

export function verifyPassword(password,stored){
  if(typeof password!=="string"||typeof stored!=="string") return false;
  const [scheme,iterations,salt,hash]=stored.split("$");
  if(scheme!==`pbkdf2_${DIGEST}`||!iterations||!salt||!hash)return false;
  const derived=crypto.pbkdf2Sync(password,salt,Number(iterations),KEYLEN,DIGEST).toString("hex");
  return derived.length===hash.length&&crypto.timingSafeEqual(Buffer.from(derived),Buffer.from(hash));
}

export function normalizeEmail(email){
  return String(email||"").trim().toLowerCase();
}
