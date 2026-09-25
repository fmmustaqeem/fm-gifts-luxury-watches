import { hashPassword, normalizeEmail } from "./password.js";

export function validateRegistration(input={}){
  const email=normalizeEmail(input.email);
  const password=String(input.password||"");
  const name=String(input.name||"").trim();
  if(!name) return {ok:false,status:400,error:"Name is required"};
  if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) return {ok:false,status:400,error:"Valid email is required"};
  if(password.length<8) return {ok:false,status:400,error:"Password must be at least 8 characters"};
  return {ok:true,user:{name,email,password_hash:hashPassword(password),role:"customer",tier:"free",status:"active"}};
}

export async function registerUser(input,{findByEmail,createUser}){
  const validated=validateRegistration(input);
  if(!validated.ok)return validated;
  if(await findByEmail(validated.user.email)) return {ok:false,status:409,error:"Account already exists"};
  const user=await createUser(validated.user);
  return {ok:true,user:{id:user.id,name:user.name,email:user.email,role:user.role,tier:user.tier,status:user.status}};
}
