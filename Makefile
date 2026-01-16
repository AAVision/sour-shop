.PHONY: help build up down logs shell migrate seed fresh clean backup restore

help:
	@echo "eCommerce Docker Commands:"
	@echo ""
	@echo "make build          - Build Docker images"
	@echo "make up             - Start all services"
	@echo "make down           - Stop all services"
	@echo "make logs           - View service logs"
	@echo "make shell          - Access app container shell"
	@echo "make migrate        - Run database migrations"
	@echo "make seed           - Seed database"
	@echo "make fresh          - Fresh install (build + migrate + seed)"
	@echo "make clean          - Clean up Docker resources"
	@echo "make backup         - Backup database"
	@echo "make restore        - Restore database from backup"
	@echo "make test           - Run tests"
	@echo "make tinker         - Access Laravel Tinker"
	@echo "make optimize       - Optimize application"

build:
	docker-compose build

up:
	docker-compose up -d
	@echo "Services started. Access at:"
	@echo "  App: http://localhost"
	@echo "  Mailpit: http://localhost:8025"

down:
	docker-compose down

logs:
	docker-compose logs -f

shell:
	docker-compose exec app sh

migrate:
	docker-compose exec app php artisan migrate --force

seed:
	docker-compose exec app php artisan db:seed

fresh:
	@echo "Building..."
	docker-compose build
	@echo "Starting..."
	docker-compose up -d
	@sleep 10
	@echo "Migrating..."
	docker-compose exec app php artisan migrate:fresh --force
	@echo "Seeding..."
	docker-compose exec app php artisan db:seed
	@echo "Optimizing..."
	docker-compose exec app php artisan optimize
	@docker-compose exec app php artisan storage:link || true
	@echo "✓ Fresh install completed!"

clean:
	docker-compose down -v
	docker system prune -f

backup:
	@bash docker-backup.sh

restore:
	@bash docker-restore.sh

test:
	docker-compose exec app php artisan test

tinker:
	docker-compose exec app php artisan tinker

optimize:
	docker-compose exec app php artisan optimize
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

ps:
	docker-compose ps

stop:
	docker-compose stop

start:
	docker-compose start

restart:
	docker-compose restart

ps-full:
	docker ps -a

images:
	docker images

prune-images:
	docker image prune -a

prune-containers:
	docker container prune -f

prune-volumes:
	docker volume prune -f

prune-all:
	docker system prune -a -f

db-cli:
	docker-compose exec mysql mysql -u ecommerce -p

redis-cli:
	docker-compose exec redis redis-cli -a password

cache-clear:
	docker-compose exec app php artisan cache:clear

queue-work:
	docker-compose exec app php artisan queue:work redis

stat:
	docker stats

version:
	@docker-compose --version
	@docker --version
