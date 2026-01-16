#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo -e "${GREEN}=== eCommerce Docker Setup ===${NC}\n"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Docker is installed${NC}"

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Docker Compose is installed${NC}\n"

# Check if .env file exists
if [ ! -f "$SCRIPT_DIR/.env" ]; then
    echo -e "${YELLOW}⚠ .env file not found. Creating from .env.docker...${NC}"
    if [ -f "$SCRIPT_DIR/.env.docker" ]; then
        cp "$SCRIPT_DIR/.env.docker" "$SCRIPT_DIR/.env"
        echo -e "${GREEN}✓ .env created${NC}\n"
    else
        echo -e "${RED}✗ .env.docker not found${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ .env file exists${NC}\n"
fi

# Ask user for action
echo "What would you like to do?"
echo "1) Start services (docker-compose up -d)"
echo "2) Build and start services"
echo "3) Run migrations"
echo "4) Seed database"
echo "5) Fresh install (build + migrate + seed)"
echo "6) Stop services"
echo "7) View logs"
echo "8) Shell access"
echo "q) Quit"
echo ""
read -p "Enter your choice [1-8, q]: " choice

case $choice in
    1)
        echo -e "\n${GREEN}Starting services...${NC}"
        docker-compose up -d
        echo -e "${GREEN}✓ Services started${NC}"
        echo -e "\n${YELLOW}Services available at:${NC}"
        echo "  Application: http://localhost"
        echo "  Mailpit: http://localhost:8025"
        echo "  MySQL: localhost:3306"
        echo "  Redis: localhost:6379"
        ;;
    2)
        echo -e "\n${GREEN}Building and starting services...${NC}"
        docker-compose build
        docker-compose up -d
        echo -e "${GREEN}✓ Services built and started${NC}"
        ;;
    3)
        echo -e "\n${GREEN}Running migrations...${NC}"
        docker-compose exec app php artisan migrate --force
        echo -e "${GREEN}✓ Migrations completed${NC}"
        ;;
    4)
        echo -e "\n${GREEN}Seeding database...${NC}"
        docker-compose exec app php artisan db:seed
        echo -e "${GREEN}✓ Database seeded${NC}"
        ;;
    5)
        echo -e "\n${GREEN}Fresh install...${NC}"
        echo "Building services..."
        docker-compose build --no-cache
        echo "Starting services..."
        docker-compose up -d
        echo "Waiting for services to be ready..."
        sleep 10
        echo "Running migrations..."
        docker-compose exec app php artisan migrate:fresh --force
        echo "Seeding database..."
        docker-compose exec app php artisan db:seed
        echo "Creating storage link..."
        docker-compose exec app php artisan storage:link || true
        echo "Optimizing..."
        docker-compose exec app php artisan optimize
        echo -e "${GREEN}✓ Fresh install completed${NC}"
        echo -e "\n${YELLOW}Application ready at:${NC} http://localhost"
        ;;
    6)
        echo -e "\n${GREEN}Stopping services...${NC}"
        docker-compose down
        echo -e "${GREEN}✓ Services stopped${NC}"
        ;;
    7)
        echo -e "\n${YELLOW}Following logs (Ctrl+C to stop)...${NC}\n"
        docker-compose logs -f
        ;;
    8)
        echo -e "\n${GREEN}Accessing app container shell...${NC}"
        echo "Type 'exit' to quit"
        docker-compose exec app sh
        ;;
    q|Q)
        echo "Exiting..."
        exit 0
        ;;
    *)
        echo -e "${RED}Invalid choice${NC}"
        exit 1
        ;;
esac
