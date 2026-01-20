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

3) Install dependencies && generate key:

```bash
 docker compose exec app bash -lc "php -r 'copy(\"https://getcomposer.org/installer\", \"composer-setup.php\");' && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php || true; composer install --no-interaction || true; php artisan key:generate --force
```

4) Execute migrations:

```bash
docker compose exec app php artisan migrate --force
```