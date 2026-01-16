# Docker Setup for Ready eCommerce

## Quick Start

### Prerequisites
- Docker & Docker Compose installed
- `.env.docker` file configured (copy from `.env.docker` template)

### Local Development

```bash
# Build and start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Seed database (if seeders exist)
docker-compose exec app php artisan db:seed

# Create storage link
docker-compose exec app php artisan storage:link

# View logs
docker-compose logs -f app
```

### Access Services

- **Application**: http://localhost
- **Mailpit**: http://localhost:8025
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## Architecture

### Services

1. **app** - PHP-FPM (8.2) with Laravel
   - Runs on port 9000
   - Health checks enabled
   - Auto-restarts on failure

2. **nginx** - Web server
   - Serves on ports 80 & 443
   - Gzip compression enabled
   - Security headers included
   - Rate limiting on API routes

3. **mysql** - Database (8.0)
   - Data persisted in `mysql_data` volume
   - Configured with utf8mb4
   - Health checks enabled
   - Backup at `/docker-entrypoint-initdb.d/`

4. **redis** - Cache/Queue (7-Alpine)
   - Data persisted in `redis_data` volume
   - Password-protected
   - Health checks enabled

5. **mailpit** - Email testing
   - Catches all outgoing mail
   - Web UI at http://localhost:8025

## Configuration

### Environment Variables

Copy `.env.docker` to `.env` for local development:

```bash
cp .env.docker .env
```

Key variables:
- `APP_ENV`: Set to `production` or `local`
- `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`: MySQL credentials
- `CACHE_DRIVER`: Use `redis` for production
- `QUEUE_CONNECTION`: Use `redis` for background jobs

### PHP Configuration

- `upload_max_filesize`: 512M
- `post_max_size`: 512M
- `memory_limit`: 256M
- `max_execution_time`: 300s

Located in: `docker/php/conf.d/php.ini`

### Nginx Configuration

- Handles SSL/TLS (commented by default)
- Gzip compression enabled
- Security headers included
- Rate limiting on API endpoints
- Static file caching (1 year)

Located in: `docker/nginx/conf.d/default.conf`

### MySQL Configuration

- InnoDB storage engine
- UTF-8mb4 character set
- Max connections: 1000
- Buffer pool: 1GB (adjust for smaller servers)

Located in: `docker/mysql/my.cnf`

## Common Commands

### Build and Start
```bash
# Build images without cache
docker-compose build --no-cache

# Start services in background
docker-compose up -d

# Start with logs displayed
docker-compose up

# Stop all services
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

### Artisan Commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Fresh migration (caution!)
docker-compose exec app php artisan migrate:fresh --seed

# Seed database
docker-compose exec app php artisan db:seed

# Create cache tables
docker-compose exec app php artisan table:create-cache-table

# Generate app key
docker-compose exec app php artisan key:generate

# Optimize application
docker-compose exec app php artisan optimize
```

### Database Management
```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u ecommerce -p ecommerce

# Create database backup
docker-compose exec mysql mysqldump -u ecommerce -p ecommerce > backup.sql

# Restore from backup
docker-compose exec -T mysql mysql -u ecommerce -p ecommerce < backup.sql

# View MySQL logs
docker-compose logs mysql
```

### Cache & Queue
```bash
# Clear all caches
docker-compose exec app php artisan cache:clear

# Flush Redis cache
docker-compose exec redis redis-cli -a password FLUSHALL

# Process queue jobs
docker-compose exec app php artisan queue:work redis --sleep=3

# Monitor queue
docker-compose exec app php artisan queue:monitor
```

### Logs and Monitoring
```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# View last 100 lines
docker-compose logs --tail=100
```

### Shell Access
```bash
# Access app container
docker-compose exec app sh

# Access MySQL
docker-compose exec mysql mysql -u ecommerce -p ecommerce

# Access Redis
docker-compose exec redis redis-cli -a password
```

## Production Deployment

### For Coolify

1. **Create `.env.production`**:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_HOST=mysql
REDIS_HOST=redis
```

2. **SSL Configuration**:
   - Uncomment HTTPS server block in `docker/nginx/conf.d/default.conf`
   - Mount SSL certificates into `/etc/nginx/ssl/`

3. **Build Optimization**:
   - Multi-stage build reduces image size
   - OPcache enabled for better performance
   - Dependencies cached for faster rebuilds

4. **Resource Limits** (optional, in docker-compose.yml):
```yaml
deploy:
  resources:
    limits:
      cpus: '2'
      memory: 2G
```

### Performance Tips

1. **Increase Buffer Pool** (for larger servers):
```ini
innodb_buffer_pool_size = 4G  # in docker/mysql/my.cnf
```

2. **Enable Query Caching** (MySQL):
```ini
query_cache_size = 256M
query_cache_type = 1
```

3. **Optimize Nginx** (for high traffic):
   - Increase `worker_processes`
   - Adjust `worker_connections`

4. **Scale Queue Workers**:
   - Increase `numprocs` in `docker/supervisor/laravel-worker.conf`

## Troubleshooting

### Container won't start
```bash
# Check logs
docker-compose logs app

# Rebuild without cache
docker-compose build --no-cache app
```

### Database connection fails
```bash
# Verify MySQL is running
docker-compose ps

# Check MySQL logs
docker-compose logs mysql

# Ensure DB_HOST is set to 'mysql' in .env
```

### Redis connection issues
```bash
# Test Redis connection
docker-compose exec redis redis-cli -a password ping

# Clear Redis cache
docker-compose exec redis redis-cli -a password FLUSHALL
```

### Permission issues in storage
```bash
# Fix permissions
docker-compose exec app chmod -R 775 storage
docker-compose exec app chmod -R 775 bootstrap/cache
```

### Out of disk space
```bash
# Remove unused volumes
docker volume prune

# Remove unused images
docker image prune

# Clean Docker system
docker system prune -a
```

### High memory usage
```bash
# Check memory usage
docker stats

# Limit service memory in docker-compose.yml
```

## Security Checklist

- [ ] Change default database password in `.env`
- [ ] Generate secure `APP_KEY`: `docker-compose exec app php artisan key:generate`
- [ ] Enable SSL/TLS in production
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure firewall to restrict ports
- [ ] Use strong Redis password
- [ ] Enable database user permissions properly
- [ ] Regular database backups
- [ ] Monitor logs for suspicious activity

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## Support

For issues or improvements, check the project repository or contact support.
