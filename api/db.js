export function createSqlUsersRepository(db){
  if(!db||typeof db.prepare!=="function") throw new Error("D1 DB binding is required");
  return {
    async findUserByEmail(email){
      const row=await db.prepare(
        "SELECT id,name,email,password_hash,role,tier,status,created_at FROM users WHERE email = ? LIMIT 1"
      ).bind(String(email).trim().toLowerCase()).first();
      return row ?? null;
    },
    async createUser(user){
      const result=await db.prepare(
        "INSERT INTO users (name,email,password_hash,role,tier,status) VALUES (?,?,?,?,?,?)"
      ).bind(user.name,user.email,user.password_hash,user.role,user.tier,user.status).run();
      return {id:result.meta?.last_row_id ?? null,...user};
    }
  };
}
