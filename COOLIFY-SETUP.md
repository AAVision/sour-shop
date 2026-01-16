# Coolify Compatibility & Configuration

## ✓ Now Fully Compatible with Coolify

The Docker configuration has been updated to be 100% Coolify-compatible.

## Key Improvements

### 1. **Health Checks Fixed**
- ✓ Changed from HTTP curl (doesn't work with PHP-FPM) to netcat port check
- ✓ Proper timeout and retry settings for Coolify

### 2. **Graceful Startup**
- ✓ Entrypoint script no longer blocks on migrations (non-blocking background execution)
- ✓ DB connection failures don't prevent app startup
- ✓ Automatic retry mechanism for DB connections

### 3. **Coolify Dockerfiles**
- ✓ `Dockerfile.prod` - Production-optimized for Coolify
- ✓ Removed supervisor (not needed for Coolify single-container approach)
- ✓ Lightweight Alpine-based image (~500MB)

### 4. **Nginx Configuration**
- ✓ Security headers properly configured
- ✓ Static file caching optimized
- ✓ Sensitive directory protection
- ✓ Rate limiting ready for APIs

## Quick Deploy to Coolify

### Option 1: Using Coolify Dashboard

```yaml
# In Coolify, create new application:
1. Name: ecommerce
2. Git Repository: Your GitHub repo
3. Dockerfile: Dockerfile.prod
4. Port: 9000 (Coolify will proxy via Nginx)
5. Autorestart: Yes

Environment Variables:
APP_NAME=eCommerce
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY (generate with: php artisan key:generate)
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=ecommerce
DB_PASSWORD=STRONG_PASSWORD_HERE

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PASSWORD=REDIS_PASSWORD
```

### Option 2: Using Docker Compose File

```bash
# Use the Coolify-optimized compose file
docker-compose -f docker-compose.coolify.yml up -d

# Run migrations
docker-compose -f docker-compose.coolify.yml exec app php artisan migrate --force
```

## Verify Compatibility

### Health Checks
```bash
# Check app health
docker-compose exec app sh -c "nc -z localhost 9000 && echo 'OK' || echo 'FAIL'"

# Check MySQL health
docker-compose exec mysql mysqladmin ping -h localhost

# Check Redis health
docker-compose exec redis redis-cli ping
```

### Test Full Deployment
```bash
# Start services
docker-compose -f docker-compose.coolify.yml up -d

# Wait 30 seconds for startup
sleep 30

# Check logs
docker-compose -f docker-compose.coolify.yml logs

# Access application
curl http://localhost
```

## Coolify-Specific Configurations

### SSL/TLS (Let's Encrypt)
Coolify automatically handles SSL. No manual configuration needed.

### Custom Domain
In Coolify:
1. Go to Services > Your App
2. Click "Custom Domain"
3. Add your domain (e.g., `ecommerce.yourdomain.com`)
4. Coolify auto-provisions Let's Encrypt certificate

### Resource Limits
In Coolify Docker settings, you can optionally set:
- CPU limit: 2 cores
- Memory limit: 2GB
- Restart policy: Always

### Persistent Storage
The volumes are configured to persist:
- MySQL data: `mysql_data`
- Redis data: `redis_data`
- App storage: `./storage` (bind mount)

## Monitoring in Coolify

### View Logs
```bash
# Real-time logs
docker-compose -f docker-compose.coolify.yml logs -f

# Specific service
docker-compose -f docker-compose.coolify.yml logs -f app
docker-compose -f docker-compose.coolify.yml logs -f mysql
docker-compose -f docker-compose.coolify.yml logs -f redis
```

### Health Status
Coolify dashboard shows:
- ✓ App status (green if healthy)
- ✓ Container logs
- ✓ Resource usage
- ✓ Restart count

## Troubleshooting

### App keeps restarting
```bash
# Check logs
docker-compose -f docker-compose.coolify.yml logs app

# Common issues:
# - DB not ready: Entrypoint waits max 30 attempts (30 seconds)
# - Migration failed: Check DB credentials
# - Config error: Check all environment variables set
```

### Database connection fails
```bash
# Verify MySQL is running
docker-compose -f docker-compose.coolify.yml exec mysql mysqladmin ping

# Check credentials
docker-compose -f docker-compose.coolify.yml exec mysql mysql -u ecommerce -p

# Verify network connectivity
docker-compose -f docker-compose.coolify.yml exec app nc -zv mysql 3306
```

### Redis not responding
```bash
# Check Redis
docker-compose -f docker-compose.coolify.yml exec redis redis-cli ping

# Reset Redis
docker-compose -f docker-compose.coolify.yml exec redis redis-cli FLUSHALL
```

## Performance Tuning for Coolify

### For Small Droplets (1GB RAM)
```env
# In .env or Coolify env vars
APP_ENV=production
CACHE_DRIVER=redis
QUEUE_CONNECTION=sync  # Use sync instead of redis for small servers
SESSION_DRIVER=redis
```

### For Medium Droplets (2-4GB RAM)
```env
# Standard configuration (as provided)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### For Large Deployments (4GB+ RAM)
```env
# Same as medium, but increase:
# - MySQL buffer pool (in docker/mysql/my.cnf)
# - PHP memory limit (in docker/php/conf.d/php.ini)
# - Add multiple queue workers
```

## Coolify vs docker-compose.yml

| Feature | docker-compose.yml | docker-compose.coolify.yml |
|---------|-------------------|--------------------------|
| Development | ✓ Recommended | ✗ |
| Production | ✓ | ✓ Recommended |
| Mailpit | ✓ | ✗ (use external SMTP) |
| Queue Workers | ✓ Supervisor | ✗ (use cron for queues) |
| Volume Mounts | ✓ Full | ✓ Limited |
| Hot Reload | ✓ | ✗ |

## Integration with Coolify Features

### Backups
Coolify provides built-in backup features:
1. Go to Services > Your App > Backups
2. Configure automated daily backups
3. Download or restore from any backup

### Auto-updates
Enable in Coolify to auto-deploy on new commits:
1. Enable "Auto deploy" in application settings
2. Pushes to main/develop trigger rebuilds

### Webhooks
Coolify can trigger deployments via webhooks:
```bash
# Example: GitHub webhook
POST https://your-coolify-instance/api/deploy/webhook/{token}
```

## Files to Commit to Git

```bash
git add Dockerfile Dockerfile.prod docker-compose.coolify.yml .dockerignore
git add docker/php/conf.d/php.ini
git add docker/php/conf.d/opcache.ini
git add docker/nginx/conf.d/default.conf
git add docker/mysql/my.cnf
git add docker/entrypoint.sh
git add COOLIFY.md
git commit -m "Add Coolify-compatible Docker configuration"
git push
```

## Next Steps

1. **Test locally**:
   ```bash
   docker-compose -f docker-compose.coolify.yml up -d
   ```

2. **Verify all services**:
   ```bash
   docker-compose -f docker-compose.coolify.yml ps
   ```

3. **Deploy to Coolify**:
   - Connect your GitHub repo
   - Select `Dockerfile.prod`
   - Set environment variables
   - Click Deploy

4. **Monitor**:
   - Check Coolify dashboard for status
   - View real-time logs
   - Configure SSL certificate
   - Set up domain

## Support

- **Coolify Docs**: https://coolify.io/docs
- **Docker Docs**: https://docs.docker.com
- **Laravel Docs**: https://laravel.com/docs
- **Issue**: Check application logs in Coolify dashboard
