#!/bin/bash

# MySQL Database Backup Script

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BACKUP_DIR="$SCRIPT_DIR/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/ecommerce_backup_$TIMESTAMP.sql"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

echo "Starting database backup..."

# Create backup
docker-compose exec -T mysql mysqldump \
    -u ecommerce \
    -p"${DB_PASSWORD:-password}" \
    ecommerce > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✓ Backup completed: $BACKUP_FILE"
    echo "Size: $(du -h "$BACKUP_FILE" | cut -f1)"
    
    # Keep only last 7 backups
    echo "Cleaning up old backups..."
    ls -t "$BACKUP_DIR"/*.sql | tail -n +8 | xargs rm -f 2>/dev/null
    echo "✓ Cleanup completed"
else
    echo "✗ Backup failed"
    exit 1
fi
