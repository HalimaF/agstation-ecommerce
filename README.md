# AG Station - Agricultural Equipment Management System

A comprehensive PHP-based agricultural equipment management system with customer portal, admin dashboard, and inventory management.

## 🚀 Live Demo

🔗 **Deploy on Render**: [![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com)

## ✨ Features

- **Customer Portal**: Registration, login, product browsing, order management
- **Admin Dashboard**: Inventory management, order processing, user management
- **Product Management**: Catalog management, image uploads, pricing
- **Order System**: Cart functionality, checkout process, order tracking
- **Payment Integration**: Ready for payment gateway integration
- **User Roles**: Admin, staff, and customer role management
- **Responsive Design**: Mobile-friendly interface

## 🛠️ Tech Stack

- **Backend**: PHP 8.1+, PDO, MySQL/PostgreSQL
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Database**: MySQL 5.7+ or PostgreSQL 13+
- **Deployment**: Render, Railway, Heroku, or traditional hosting

## 🆓 Free Deployment Options

### Option 1: Render (Recommended)

Render offers excellent free tier with PostgreSQL database.

**Quick Deploy Steps:**
1. Fork this repository
2. Sign up at [render.com](https://render.com)
3. Create **PostgreSQL Database**:
   - Name: `agstation-db`
   - Plan: **Free**
4. Create **Web Service**:
   - Runtime: **Docker**
   - Connect your forked repository
   - Auto-deploy: **Yes**
5. **Set Environment Variables**:
   - Copy database connection details from PostgreSQL service
   - Add to web service environment variables
6. **Import Database Schema**:
   - Use `database/agstation_schema_postgresql.sql`
   - Connect via psql or database client

### Option 2: Railway

1. Sign up at [railway.app](https://railway.app)
2. Deploy from GitHub repository
3. Add MySQL database service
4. Set environment variables

### Option 3: Heroku

1. `heroku create your-app-name`
2. `heroku addons:create heroku-postgresql:hobby-dev`
3. `git push heroku master`

## 🔧 Environment Variables

Set these in your hosting platform:

### For Render (PostgreSQL):
```env
DB_HOST=your-postgres-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASSWORD=your-database-password
DB_PORT=5432
DB_TYPE=pgsql
```

### For Railway/Heroku (MySQL):
```env
DB_HOST=your-mysql-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASSWORD=your-database-password
DB_PORT=3306
DB_TYPE=mysql
```

## 📁 Project Structure

```
agstation/
├── admin/              # Admin dashboard and management
├── auth/               # Authentication (login/register)
├── frontend/           # Customer-facing pages
├── user/               # User dashboard
├── config/             # Database configuration
├── database/           # SQL schemas (MySQL & PostgreSQL)
├── assets/             # CSS, JS, images
├── includes/           # Shared components (header, footer, etc.)
├── uploads/            # File uploads directory
├── Dockerfile          # Docker configuration
├── composer.json       # PHP dependencies
├── Procfile           # Deployment configuration
└── index.php          # Main router
```

## 🗄️ Database Setup

### PostgreSQL (Render):
1. Import `database/agstation_schema_postgresql.sql`
2. Update environment variables
3. Test connection via `/health` endpoint

### MySQL (Railway/Local):
1. Import `database/agstation_schema.sql`
2. Update environment variables
3. Test connection via `/health` endpoint

## 🏃‍♂️ Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/HalimaF/agstation-ecommerce.git
   cd agstation-ecommerce
   ```

2. **Set up database**
   - Create MySQL/PostgreSQL database
   - Import appropriate schema file

3. **Configure environment**
   - Copy `.env.example` to `.env`
   - Update database credentials

4. **Run locally**
   ```bash
   php -S localhost:8000
   ```

5. **Access the application**
   - Frontend: `http://localhost:8000`
   - Admin: `http://localhost:8000/admin`
   - Health Check: `http://localhost:8000/health`

## 🔐 Default Admin Access

After setting up the database:
- Email: `admin@agstation.com`
- Password: `admin123` (change this immediately!)

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7+ or PostgreSQL 13+
- PDO PHP extension (with MySQL/PostgreSQL drivers)
- Web server (Apache/Nginx) or PHP built-in server

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📞 Support

For deployment issues:
- Check the hosting provider's documentation
- Review the deployment logs
- Contact the hosting provider's support

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

**Made with ❤️ for agricultural equipment management**
