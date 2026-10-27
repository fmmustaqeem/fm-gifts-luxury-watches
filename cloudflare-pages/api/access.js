export const ROLES={CUSTOMER:"customer",ADMIN:"admin"};
export const TIERS={FREE:"free",VIP:"vip"};

export function canAccess(user,{roles=[],tiers=[]}={}){
  if(!user||user.status!=="active") return false;
  if(roles.length && !roles.includes(user.role)) return false;
  if(tiers.length && !tiers.includes(user.tier)) return false;
  return true;
}

export function requireAccess(user,policy={}){
  if(!canAccess(user,policy)) return {ok:false,status:403,error:"Access denied"};
  return {ok:true,user};
}
