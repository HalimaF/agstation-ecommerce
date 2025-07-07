# PostgreSQL Schema Import Instructions for Render

## Steps to Import the Schema to Render

### 1. Push the Updated Schema to GitHub
```bash
git add database/agstation_schema_postgresql.sql
git commit -m "Add complete PostgreSQL schema with sample data"
git push origin main
```

### 2. Access Your Render PostgreSQL Database

1. Go to your Render Dashboard
2. Click on your PostgreSQL service
3. Click "Connect" to get your connection details
4. Note down the connection string

### 3. Import the Schema

**Option A: Using psql command line**
```bash
# Replace with your actual Render database connection string
psql "postgresql://username:password@hostname:port/database_name" < database/agstation_schema_postgresql.sql
```

**Option B: Using Render's Database Console**
1. In Render Dashboard, go to your PostgreSQL service
2. Click on "Console" or "Query"
3. Copy the entire contents of `agstation_schema_postgresql.sql`
4. Paste and execute in the console

**Option C: Using pgAdmin or similar tool**
1. Connect to your Render database using the connection details
2. Open a new query window
3. Copy and paste the schema content
4. Execute the script

### 4. Verify the Import

After importing, run these queries to verify:

```sql
-- Check if all tables are created
SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';

-- Check if sample data is inserted
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM products;
SELECT COUNT(*) FROM roles;

-- Test the admin login
SELECT name, email FROM users WHERE email = 'admin@agstation.com';
```

### 5. Test Your Application

After the schema is imported, your Render application should be able to:
- Connect to the database successfully
- Display products on the homepage
- Allow admin login (admin@agstation.com / admin123)
- Access admin dashboard features

### Notes

- The schema includes all ENUM types required for PostgreSQL
- All foreign key relationships are properly defined
- Sample data is included for immediate testing
- Indexes are created for better performance

If you encounter any errors during import, check the Render logs and ensure all ENUM types are created before the tables that use them.
