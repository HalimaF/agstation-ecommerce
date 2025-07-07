# AG Station - Agricultural Equipment Management System

A comprehensive PHP-based agricultural equipment management system with customer portal, admin dashboard, and inventory management.

## Features

- Customer registration and login
- Product catalog and ordering
- Admin dashboard for inventory management
- Order tracking and management
- Payment processing
- User role management

## Free Deployment Options

### Option 1: Railway (Recommended)

Railway offers a generous free tier and is perfect for PHP applications.

#### Steps:
1. **Sign up** at [railway.app](https://railway.app)
2. **Connect your GitHub repository**
3. **Add MySQL database** service
4. **Set environment variables** in Railway dashboard:
   - `DB_HOST` - Your Railway MySQL host
   - `DB_NAME` - Database name
   - `DB_USER` - Database username
   - `DB_PASSWORD` - Database password
   - `DB_PORT` - Database port (usually 3306)
5. **Deploy** - Railway will automatically deploy your app

#### Database Setup:
1. In Railway dashboard, add a MySQL service
2. Copy the database credentials to your environment variables
3. Import your schema: `database/agstation_schema.sql`

### Option 2: Heroku

1. **Install Heroku CLI**
2. **Create new Heroku app**
3. **Add ClearDB MySQL add-on**
4. **Deploy using Git**

### Option 3: Traditional Free Hosting

- **InfinityFree**: Upload via FTP, import database
- **000webhost**: File manager upload, phpMyAdmin for database
- **FreeHosting**: Similar process to above

## Local Development

1. **Clone the repository**
2. **Set up local MySQL database**
3. **Import schema** from `database/agstation_schema.sql`
4. **Copy `.env.example` to `.env`** and update database credentials
5. **Run locally**: `php -S localhost:8000`

## Project Structure

```
agstation/
├── admin/          # Admin dashboard
├── frontend/       # Customer-facing pages
├── auth/           # Authentication
├── config/         # Database configuration
├── database/       # SQL schema
├── assets/         # CSS, JS, images
├── includes/       # Shared components
├── user/           # User dashboard
└── uploads/        # File uploads
```

## Environment Variables

- `DB_HOST` - Database host
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASSWORD` - Database password
- `DB_PORT` - Database port

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- PDO PHP extension

## Support

For deployment issues, check the hosting provider's documentation or contact their support team.
