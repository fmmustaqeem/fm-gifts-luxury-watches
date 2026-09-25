export function createDbAdapter(db){
  if(!db) throw new Error("Database binding is required");
  return {
    async findUserByEmail(email){
      return db.getUserByEmail(email);
    },
    async createUser(user){
      return db.createUser(user);
    }
  };
}

export function assertDbAdapter(adapter){
  if(!adapter||typeof adapter.findUserByEmail!=="function"||typeof adapter.createUser!=="function")
    throw new Error("Invalid database adapter");
  return adapter;
}
