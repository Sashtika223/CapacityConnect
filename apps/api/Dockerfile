FROM node:20-alpine AS builder

WORKDIR /app

# Copy root workspace configs
COPY package*.json ./
COPY packages/shared-types/package*.json ./packages/shared-types/
COPY apps/api/package*.json ./apps/api/
COPY prisma ./prisma/

RUN npm ci

# Copy shared types and build
COPY packages/shared-types ./packages/shared-types/
RUN npm run build --workspace=@capacity-connect/shared-types

# Copy API and generate Prisma
COPY apps/api ./apps/api/
RUN npx prisma generate --schema=prisma/schema.prisma
RUN npm run build --workspace=@capacity-connect/api

FROM node:20-alpine AS runner
WORKDIR /app

ENV NODE_ENV=production

COPY package*.json ./
COPY packages/shared-types/package*.json ./packages/shared-types/
COPY apps/api/package*.json ./apps/api/
COPY prisma ./prisma/

RUN npm ci --omit=dev

COPY --from=builder /app/packages/shared-types/dist ./packages/shared-types/dist
COPY --from=builder /app/apps/api/dist ./apps/api/dist
COPY --from=builder /app/node_modules/.prisma ./node_modules/.prisma
COPY --from=builder /app/node_modules/@prisma ./node_modules/@prisma

EXPOSE 5000
CMD ["node", "apps/api/dist/index.js"]
