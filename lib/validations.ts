import { z } from 'zod'

export const productSchema = z.object({
  name: z.string().min(3).max(100),
  description: z.string().min(10).max(1000),
  price: z.number().positive(),
  stock: z.number().int().nonnegative(),
  categoryId: z.string().cuid(),
  brand: z.string().optional(),
  featured: z.boolean().default(false),
  published: z.boolean().default(true),
})

export const orderSchema = z.object({
  customerName: z.string().min(2),
  phone: z.string().regex(/^03\d{9}$/),
  address: z.string().min(5),
  city: z.string().min(2),
  paymentMethod: z.enum(['COD', 'BANK_TRANSFER']),
})

export const userSchema = z.object({
  email: z.string().email(),
  password: z.string().min(8),
  role: z.enum(['ADMIN', 'CUSTOMER']).default('CUSTOMER'),
})
