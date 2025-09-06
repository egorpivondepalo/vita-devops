#!/bin/bash

START=$(date +%s)

COUNT=0

TARGET="php"

if [ -f logs/nginx/access.log ]; then
    COUNT=$((COUNT + $(grep -c "$TARGET" logs/nginx/access.log 2>/dev/null || echo 0)))
fi

if [ -f logs/nginx/error.log ]; then
    COUNT=$((COUNT + $(grep -c "$TARGET" logs/nginx/error.log 2>/dev/null || echo 0)))
fi

if [ -f logs/php_fpm/php-access.log ]; then
    COUNT=$((COUNT + $(grep -c "$TARGET" logs/php_fpm/php-access.log 2>/dev/null || echo 0)))
fi

END=$(date +%s)
TIME=$((END - START))

echo "Count: $COUNT" > report.txt
echo "Script time: ${TIME} секунд" >> report.txt

echo "count: $COUNT; time: ${TIME}"
