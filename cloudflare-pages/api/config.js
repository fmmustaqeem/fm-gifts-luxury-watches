export function requireDatabase(env){
  const db=env?.DB;
  if(!db) throw new Error("DB binding is not configured");
  return db;
}

export function getSessionSecret(env){
  const secret=env?.SESSION_SECRET;
  if(!secret) throw new Error("SESSION_SECRET is not configured");
  return secret;
}
