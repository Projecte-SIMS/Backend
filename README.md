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

3) Start app service

```bash
docker compose run --rm app composer install --no-interaction
```

4) Install dependencies:

```bash
docker compose exec app composer install --no-interaction
```

5) Generate app key:
```bash
docker compose exec app php artisan key:generate --force
```

6) Execute migrations:

```bash
docker compose exec app php artisan migrate --force
```

## Migrations
### Refresh migrations
This will delete all the DB and exec all the migrations again:
```bash
docker compose exec app php artisan migrate:refresh --seed;
```

