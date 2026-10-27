import { verifyPassword, normalizeEmail } from "./password.js";
import { createSession } from "./auth.js";

export async function loginUser(input={}, { findByEmail }){
  const email=normalizeEmail(input.email);
  const password=String(input.password||"");
  if(!email||!password)return {ok:false,status:400,error:"Email and password are required"};
  const user=await findByEmail(email);
  if(!user||user.status!=="active"||!verifyPassword(password,user.password_hash))
    return {ok:false,status:401,error:"Invalid credentials"};
  const token=createSession({id:user.id,role:user.role,tier:user.tier});
  return {ok:true,session:{token,expires_in:28800},user:{id:user.id,name:user.name,email:user.email,role:user.role,tier:user.tier}};
}
