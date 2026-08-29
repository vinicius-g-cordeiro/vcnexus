FROM node:20-alpine AS base

WORKDIR /app

ARG FRONTEND_DIR=./frontend

FROM base AS dependencies

ARG FRONTEND_DIR

COPY ${FRONTEND_DIR}/package*.json ./

RUN npm ci --prefer-offline --no-audit --legacy-peer-deps || npm install

FROM dependencies AS builder

ARG FRONTEND_DIR

COPY ${FRONTEND_DIR} .

RUN npm run build

FROM dependencies AS development

ARG FRONTEND_DIR

COPY ${FRONTEND_DIR} .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

FROM nginx:alpine AS production

COPY --from=builder /app/dist /usr/share/nginx/html

RUN echo 'server { \
    listen 80; \
    root /usr/share/nginx/html; \
    index index.html; \
    location / { \
        try_files $uri $uri/ /index.html; \
    } \
}' > /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]