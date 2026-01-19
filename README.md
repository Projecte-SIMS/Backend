# Backend

## Deployment
1) Copy the example and put the real creds on the .env
```bash
cp .env.example .env
```

2) Run container

```bash
docker compose up -d --build
```

3) Execute migrations:

```bash
docker compose exec app php artisan migrate --force
```