// Server-side authentication boundary.
// Production runtime must provide SESSION_SECRET through environment/bindings.
// Do not place credentials or secrets in browser code.
export function requireAuth(req){
  const auth=req?.headers?.authorization||"";
  if(!auth.startsWith("Bearer ")) return {ok:false,status:401,error:"Authentication required"};
  const token=auth.slice(7).trim();
  if(!token) return {ok:false,status:401,error:"Authentication required"};
  // Token verification is intentionally delegated to the production
  // identity/session provider; never accept an unverified token as identity.
  return {ok:false,status:501,error:"Session provider not connected"};
}

export function requireAdmin(req){
  const result=requireAuth(req);
  if(!result.ok) return result;
  return {ok:false,status:501,error:"Admin role provider not connected"};
}
