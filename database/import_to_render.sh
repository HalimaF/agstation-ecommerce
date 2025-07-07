#!/bin/bash
# Script to import PostgreSQL schema to Render database
# Make sure to replace the connection details with your actual Render database credentials

echo "PostgreSQL Schema Import Script for Render"
echo "==========================================="
echo ""
echo "Before running this script, make sure you have:"
echo "1. PostgreSQL client (psql) installed"
echo "2. Your Render database connection details"
echo ""
echo "You can find your connection details in:"
echo "- Render Dashboard > Your PostgreSQL service > Connect"
echo ""
echo "Example command to import:"
echo "psql postgresql://username:password@hostname:port/database_name < agstation_schema_postgresql.sql"
echo ""
echo "Replace the connection string with your actual Render database details."
echo ""
echo "Alternative: You can also copy the contents of agstation_schema_postgresql.sql"
echo "and paste it directly into the Render database console or use a tool like pgAdmin."
