-- Production database schema foundation
CREATE TABLE IF NOT EXISTS users (id TEXT PRIMARY KEY, name TEXT NOT NULL, phone TEXT, email TEXT, role TEXT NOT NULL DEFAULT 'customer', tier TEXT NOT NULL DEFAULT 'free', status TEXT NOT NULL DEFAULT 'active', password_hash TEXT, created_at TEXT NOT NULL, updated_at TEXT);
CREATE TABLE IF NOT EXISTS products (id TEXT PRIMARY KEY, sku TEXT UNIQUE NOT NULL, name TEXT NOT NULL, brand TEXT, category TEXT, description TEXT, cost REAL, price REAL, status TEXT NOT NULL DEFAULT 'Draft', source_id TEXT, created_at TEXT NOT NULL, updated_at TEXT);
CREATE TABLE IF NOT EXISTS media (id TEXT PRIMARY KEY, sku TEXT NOT NULL, type TEXT NOT NULL, url TEXT NOT NULL, alt TEXT, created_at TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS orders (id TEXT PRIMARY KEY, customer_id TEXT, subtotal REAL NOT NULL, delivery_cost REAL, total REAL NOT NULL, courier TEXT, tracking TEXT, status TEXT NOT NULL DEFAULT 'Pending', created_at TEXT NOT NULL, updated_at TEXT);
CREATE TABLE IF NOT EXISTS order_items (id TEXT PRIMARY KEY, order_id TEXT NOT NULL, sku TEXT NOT NULL, name TEXT NOT NULL, qty INTEGER NOT NULL, unit_price REAL NOT NULL);
CREATE TABLE IF NOT EXISTS couriers (id TEXT PRIMARY KEY, name TEXT NOT NULL, website TEXT, whatsapp TEXT, status TEXT NOT NULL DEFAULT 'active');
CREATE TABLE IF NOT EXISTS courier_rates (id TEXT PRIMARY KEY, courier_id TEXT NOT NULL, area TEXT NOT NULL, mode TEXT NOT NULL, base_weight_kg REAL NOT NULL DEFAULT 1, rate REAL NOT NULL, verified_at TEXT);
CREATE TABLE IF NOT EXISTS catalog_sources (id TEXT PRIMARY KEY, type TEXT NOT NULL, name TEXT NOT NULL, contact TEXT, url TEXT, private INTEGER NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS notifications (id TEXT PRIMARY KEY, type TEXT NOT NULL, message TEXT NOT NULL, read INTEGER NOT NULL DEFAULT 0, created_at TEXT NOT NULL);
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_orders_customer ON orders(customer_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_rates_area ON courier_rates(area);
