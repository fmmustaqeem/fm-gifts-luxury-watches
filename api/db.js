export function createSqlUsersRepository(sql){
  if(!sql||typeof sql.query!=="function") throw new Error("SQL binding is required");
  return {
    async findUserByEmail(email){
      const rows=await sql.query("SELECT id,name,email,password_hash,role,tier,status,created_at FROM users WHERE email = ? LIMIT 1",[String(email).trim().toLowerCase()]);
      return rows?.[0] ?? null;
    },
    async createUser(user){
      const result=await sql.query("INSERT INTO users (name,email,password_hash,role,tier,status) VALUES (?,?,?,?,?,?)",[user.name,user.email,user.password_hash,user.role,user.tier,user.status]);
      return {id:result.insertId,...user};
    }
  };
}
