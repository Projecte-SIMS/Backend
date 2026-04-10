# Render Deployment Guide

## Database Configuration

### Internal vs External Database URLs

In Render, there are two types of database URLs:

- **Internal URL** (for services within Render): `dpg-d7b94pp4tr6s73be63b0-a` - Use this for your app service
- **External URL** (for external services): `dpg-d7b94pp4tr6s73be63b0-a.oregon-postgres.render.com` - Use this only for services outside Render

Since both the app and database are services within Render, you should use the **Internal URL**.

### Current Configuration

The `.env.production` file contains:

```env
DB_URL=postgresql://admin_sims:aUh4gBVPMiIuTDavQDD4bTjFst3o2Nxf@dpg-d7b94pp4tr6s73be63b0-a/project_sims_backend_sdrf
```

This is the internal URL used when both services are within Render.

## Fresh Database Migration

To reset the database completely on Render deployment:

1. **Via Render Dashboard - Set Environment Variable**
   - Go to your Web Service settings on Render
   - Add/modify environment variable: `FRESH_MIGRATION=true`
   - Trigger a redeploy

2. **The Script Will**
   - Run `php artisan migrate:fresh --force` (drops all tables and recreates them)
   - Run `php artisan db:seed --force` (populates with initial data)
   - Start the application

3. **After Fresh Migration**
   - Remove the `FRESH_MIGRATION=true` variable before the next deploy
   - Or leave it and it will always reset on deploy (not recommended for production)

## Normal Deployment

For regular deployments (without reset):

1. Push your code to the main branch
2. Render automatically deploys
3. The `docker-entrypoint.sh` runs:
   - `php artisan migrate --force` - applies new migrations only
   - `php artisan db:seed --force` - runs seeders (idempotent)
   - Starts the application

## Troubleshooting

### Database Connection Errors

**Error**: `could not translate host name`

**Solution**: Verify you're using the Internal Database URL in `.env.production` (not the external one):
```
// ✅ Correct (Internal - for Render services)
dpg-d7b94pp4tr6s73be63b0-a

// ❌ Wrong (External - only for services outside Render)
dpg-d7b94pp4tr6s73be63b0-a.oregon-postgres.render.com
```

### Migration Failures

If migrations fail:
1. Check Render logs in the Dashboard
2. Verify database credentials in `.env.production`
3. Ensure `APP_KEY` is set (base64 encoded)
4. Try setting `FRESH_MIGRATION=true` to reset

### CORS/Connection Issues

- Frontend and backend are on different Render services
- CORS must be configured in `config/cors.php`
- Update `FRONTEND_URL` in `.env.production` to match your frontend domain

## Environment Variables

Required for Render:

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_ENV` | Must be `production` | production |
| `APP_KEY` | Base64 encoded key | base64:... |
| `APP_DEBUG` | Should be `false` in production | false |
| `DB_URL` | Full PostgreSQL connection string | postgresql://... |
| `FRONTEND_URL` | Frontend domain for CORS | https://frontend.vercel.app |
| `CENTRAL_DOMAIN` | Your Render app domain | sims-backend-api-0b2w.onrender.com |

## Deployment Checklist

- [ ] `.env.production` has correct `DB_URL` with external hostname
- [ ] `APP_KEY` is set and base64 encoded
- [ ] `FRONTEND_URL` matches your actual frontend domain
- [ ] `CENTRAL_DOMAIN` is set to your Render app URL
- [ ] Code is committed and pushed to main branch
- [ ] Render redeploy is triggered (automatic or manual)
- [ ] Check Render logs to verify no errors
- [ ] Test API endpoints from frontend (should not have CORS errors)
