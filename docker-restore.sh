#!/bin/bash

# MySQL Database Restore Script

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BACKUP_DIR="$SCRIPT_DIR/backups"

if [ -z "$1" ]; then
    echo "Available backups:"
    ls -lh "$BACKUP_DIR"/*.sql 2>/dev/null || echo "No backups found"
    echo ""
    echo "Usage: $0 <backup-file>"
    echo "Example: $0 backups/ecommerce_backup_20240101_120000.sql"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "✗ Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "Restoring database from: $BACKUP_FILE"
read -p "Are you sure? This will overwrite the current database. (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Restore cancelled"
    exit 0
fi

docker-compose exec -T mysql mysql \
    -u ecommerce \
    -p"${DB_PASSWORD:-password}" \
    ecommerce < "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✓ Database restored successfully"
else
    echo "✗ Restore failed"
    exit 1
fi
