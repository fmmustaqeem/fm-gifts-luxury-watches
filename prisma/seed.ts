import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

async function main() {
  const category = await prisma.category.upsert({
    where: { slug: 'luxury-watches' },
    update: {},
    create: {
      name: 'Luxury Watches',
      slug: 'luxury-watches',
      description: 'Premium timepieces for the discerning',
    },
  })

  await prisma.product.upsert({
    where: { slug: 'rolex-style-gold-watch' },
    update: {},
    create: {
      name: 'Rolex Style Gold Watch',
      slug: 'rolex-style-gold-watch',
      description: 'Elegant gold-plated luxury watch with Swiss movement.',
      price: 29999,
      stock: 10,
      brand: 'Rolex Style',
      categoryId: category.id,
      images: {
        create: {
          url: '/images/demo-watch.jpg',
          alt: 'Gold luxury watch',
          isPrimary: true,
        },
      },
    },
  })

  console.log('Seed completed')
}

main().catch(console.error).finally(() => prisma.$disconnect())
