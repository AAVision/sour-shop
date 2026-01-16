# Coolify Deployment Guide

## Prerequisites

- Coolify instance running
- Docker and Docker Compose available on the server
- Domain name configured
- SSL certificate (Coolify can auto-generate with Let's Encrypt)

## Step 1: Prepare Your Repository

1. Commit all Docker files to your repository:
```bash
git add Dockerfile Dockerfile.prod docker-compose.prod.yml .env.docker .dockerignore
git commit -m "Add Docker configuration for Coolify deployment"
git push
```

2. Ensure your repository contains:
   - `Dockerfile` or `Dockerfile.prod`
   - `docker-compose.prod.yml` (if using compose)
   - Application code in the root

## Step 2: Create Coolify Application

1. In Coolify Dashboard, click "New Application"
2. Select "Docker" as the deployment method
3. Connect your Git repository
4. Configure build settings:
   - **Build Command**: Leave empty (Dockerfile handles it)
   - **Start Command**: Leave empty
   - **Expose Port**: 9000

## Step 3: Configure Environment Variables

In Coolify Application Settings, add these environment variables:

```
APP_NAME=eCommerce
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=ecommerce
DB_PASSWORD=STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=ROOT_PASSWORD

REDIS_HOST=redis
REDIS_PASSWORD=REDIS_PASSWORD
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## Step 4: Configure Services

### Option A: Using Docker Compose (Recommended)

1. Use `docker-compose.prod.yml` in Coolify
2. Coolify will automatically manage MySQL, Redis, and other services

### Option B: Using External Services

If using managed databases:

1. Skip mysql and redis services from compose
2. Point to external DB_HOST and REDIS_HOST
3. Ensure network access from app to external services

## Step 5: Configure Reverse Proxy

### In Coolify:

1. Go to Reverse Proxy settings
2. Add your domain
3. Point to port 9000 (PHP-FPM)
4. Enable SSL (Let's Encrypt)
5. Set up Nginx configuration to serve static files

### Nginx Configuration (Optional):

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    
    root /app/public;
    index index.php;
    
    client_max_body_size 512M;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Step 6: Deploy

1. In Coolify, click "Deploy"
2. Monitor deployment logs
3. Wait for services to be ready
4. Access your application at your domain

## Step 7: Post-Deployment

### Run Migrations

```bash
# Via Coolify terminal or SSH
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### Create Initial Admin

```bash
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')])
```

### Verify Services

```bash
# Check all services
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f

# Check database connection
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

## Monitoring & Maintenance

### Monitor Resources

```bash
docker stats
```

### Backup Database

```bash
docker-compose -f docker-compose.prod.yml exec -T mysql mysqldump \
  -u ecommerce \
  -p'PASSWORD' \
  ecommerce > backup.sql
```

### View Logs

```bash
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f nginx
docker-compose -f docker-compose.prod.yml logs -f mysql
```

### Update Application

1. Push changes to repository
2. In Coolify, trigger new deployment
3. Coolify will pull latest, rebuild, and restart

### Scale Services (if needed)

Edit `docker-compose.prod.yml` and increase replicas:

```yaml
app:
  deploy:
    replicas: 2
```

## Troubleshooting

### 502 Bad Gateway

1. Check if app container is running
2. Verify PHP-FPM is listening on port 9000
3. Check Nginx logs

### Database Connection Fails

1. Verify DB_HOST points to correct service
2. Check database credentials
3. Ensure MySQL is healthy

### High Memory Usage

1. Increase MySQL buffer pool in `docker/mysql/my.cnf`
2. Optimize Redis memory
3. Increase server RAM

### SSL Certificate Issues

1. Ensure domain is properly configured
2. Check Coolify proxy settings
3. Review SSL renewal logs

## Performance Optimization

### For High Traffic:

1. Increase PHP-FPM workers
2. Enable Redis caching
3. Use CDN for static assets
4. Enable MySQL query cache
5. Set up load balancing (multiple app replicas)

### For Low Latency:

1. Choose server near your users
2. Enable gzip compression
3. Use HTTP/2
4. Optimize images
5. Minify CSS/JS

## Security Checklist

- [ ] Use strong database password
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_KEY`
- [ ] Configure firewall rules
- [ ] Enable HTTPS/SSL
- [ ] Regular backups
- [ ] Monitor logs for attacks
- [ ] Keep dependencies updated
- [ ] Use environment variables for secrets
- [ ] Set up rate limiting

## Support

For Coolify-specific issues: https://coolify.io/docs
For Laravel issues: https://laravel.com/docs
For Docker issues: https://docs.docker.com
