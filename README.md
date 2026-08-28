# FM Gifts & Luxury Watches

Pakistan-focused e-commerce foundation for luxury watches and gifts.

## Stack
- Next.js 14 App Router
- TypeScript strict mode
- Tailwind CSS
- Prisma + PostgreSQL
- Zod validation
- GitHub Actions CI
- Vercel deployment

## Setup
1. Copy `.env.example` to `.env` and configure `DATABASE_URL`.
2. Run `npm install`.
3. Run `npx prisma db push`.
4. Run `npm run db:seed`.
5. Run `npm run dev`.

## CI
Pull requests to `main` run dependency installation, type-check, lint, Prisma generation, and production build.

## Roadmap
- Day 1: Foundation
- Day 2: Storefront UI
- Day 3: Product system
- Day 4: Checkout and orders
- Day 5: Admin dashboard
- Day 6: Security and SEO
- Day 7: Testing and production hardening
