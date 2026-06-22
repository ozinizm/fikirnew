#!/bin/bash
# start-project.sh - Fikir Creative automatic setup and runner script

# Colors for nice output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}====================================================${NC}"
echo -e "${BLUE}        Fikir Creative - Local Setup System         ${NC}"
echo -e "${BLUE}====================================================${NC}"

# 1. Install Node.js dependencies
echo -e "\n${YELLOW}[1/3] Dependency check...${NC}"
if [ -d "node_modules" ]; then
    echo -e "${GREEN}node_modules directory found. Checking package updates...${NC}"
fi
echo "Running npm install..."
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Dependencies installed successfully!${NC}"
else
    echo -e "${RED}Warning: npm install returned an error. Check logs.${NC}"
fi

# 2. Database check and schema import
echo -e "\n${YELLOW}[2/3] Checking MariaDB/MySQL database...${NC}"
if mysql -u root -e "SHOW DATABASES;" >/dev/null 2>&1; then
    echo -e "${GREEN}Successfully connected to MariaDB server.${NC}"
    
    # Check if database exists
    if mysql -u root -e "USE fikircreative;" >/dev/null 2>&1; then
        echo -e "${GREEN}Database 'fikircreative' already exists.${NC}"
    else
        echo -e "${YELLOW}Database 'fikircreative' not found. Creating and importing schema...${NC}"
        mysql -u root < database/admin-panel-schema.sql
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}Database and schema successfully created & imported!${NC}"
        else
            echo -e "${RED}Error: Failed to import schema from database/admin-panel-schema.sql${NC}"
        fi
    fi
else
    echo -e "${RED}Error: Could not connect to MariaDB server using user 'root' and no password.${NC}"
    echo -e "${YELLOW}Please ensure MariaDB/MySQL is running and port 3306 is open.${NC}"
fi

# 3. Running local servers
echo -e "\n${YELLOW}[3/3] Launching local servers...${NC}"
echo -e "${BLUE}Starting PHP admin panel server on http://127.0.0.1:8000 in background...${NC}"
php -d opcache.enable=0 -S 127.0.0.1:8000 > /dev/null 2>&1 &
PHP_PID=$!

echo -e "${BLUE}Starting Next.js development server on http://localhost:3000...${NC}"
echo -e "${BLUE}Open http://localhost:3000/admin in your browser to access the Admin Panel.${NC}"
echo -e "${BLUE}====================================================${NC}\n"

# Auto-cleanup PHP server process when this shell script exits
trap "kill $PHP_PID" EXIT

npm run dev
