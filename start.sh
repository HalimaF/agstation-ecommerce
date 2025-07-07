#!/bin/bash
# Simple start script for Railway deployment

# Check if PORT is set, default to 8080
PORT=${PORT:-8080}

# Start PHP built-in server
php -S 0.0.0.0:$PORT -t . -d display_errors=1 -d log_errors=1
