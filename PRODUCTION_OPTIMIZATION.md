# Production Optimization Checklist

## PHP Configuration
- opcache.enable=1
- opcache.memory_consumption=256
- opcache.max_accelerated_files=20000
- opcache.validate_timestamps=0

## Laravel Optimizations
- APP_DEBUG=false
- APP_ENV=production
- CACHE_DRIVER=redis
- SESSION_DRIVER=redis
- QUEUE_CONNECTION=redis

## Web Server (Nginx)
- Enable gzip compression
- Set proper cache headers
- Enable HTTP/2
- Configure static file caching

## Database
- Enable query cache
- Optimize MySQL/PostgreSQL settings
- Regular database maintenance

## Monitoring
- Set up application monitoring
- Configure log rotation
- Enable performance tracking