#!/bin/bash

# Colors for terminal output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Running Laravel Pint (Code Style Checker)...${NC}"
./vendor/bin/pint --test

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Code style checks passed!${NC}"
else
    echo -e "${RED}Code style checks failed! Please run './vendor/bin/pint' to fix the issues.${NC}"
    exit 1
fi

echo -e "\n${YELLOW}Running PHPUnit Tests...${NC}"
php artisan test --testsuite=Unit,Feature

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Unit and Feature tests passed!${NC}"
else
    echo -e "${RED}Some tests failed! Please fix the issues.${NC}"
    exit 1
fi

echo -e "\n${YELLOW}Running Dusk Browser Tests...${NC}"
php artisan dusk

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Browser tests passed!${NC}"
else
    echo -e "${RED}Some browser tests failed! Please fix the issues.${NC}"
    exit 1
fi

echo -e "\n${GREEN}All tests passed successfully!${NC}" 