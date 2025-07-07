# AG Station - Agricultural Equipment Management System

A comprehensive PHP-based agricultural equipment management system with customer portal, admin dashboard, and inventory management.

## 🚀 Live Demo

🔗 **Deploy your own**: [![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template/php)

## ✨ Features

- **Customer Portal**: Registration, login, product browsing, order management
- **Admin Dashboard**: Inventory management, order processing, user management
- **Product Management**: Catalog management, image uploads, pricing
- **Order System**: Cart functionality, checkout process, order tracking
- **Payment Integration**: Ready for payment gateway integration
- **User Roles**: Admin, staff, and customer role management
- **Responsive Design**: Mobile-friendly interface

## 🛠️ Tech Stack

- **Backend**: PHP 8.0+, PDO, MySQL
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Database**: MySQL 5.7+
- **Deployment**: Railway, Heroku, or traditional hosting

## 🆓 Free Deployment Options

### Option 1: Railway (Recommended)

Railway offers a generous free tier and is perfect for PHP applications.

**Quick Deploy:**
1. Fork this repository
2. Sign up at [railway.app](https://railway.app)
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your forked repository
5. Add MySQL database service
6. Set environment variables (see below)
7. Deploy!

### Option 2: Heroku

1. Install Heroku CLI
2. `heroku create your-app-name`
3. `heroku addons:create cleardb:ignite`
4. `git push heroku master`

### Option 3: Traditional Free Hosting

- **InfinityFree**: Upload via FTP, import database
- **000webhost**: File manager upload, phpMyAdmin for database
- **FreeHosting**: Similar process to above

## 🔧 Environment Variables

Set these in your hosting platform:

```env
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASSWORD=your-database-password
DB_PORT=3306
```

## 📁 Project Structure

```
agstation/
├── admin/              # Admin dashboard and management
├── auth/               # Authentication (login/register)
├── frontend/           # Customer-facing pages
├── user/               # User dashboard
├── config/             # Database configuration
├── database/           # SQL schema and migrations
├── assets/             # CSS, JS, images
├── includes/           # Shared components (header, footer, etc.)
├── uploads/            # File uploads directory
├── composer.json       # PHP dependencies
├── Procfile           # Deployment configuration
├── nixpacks.toml      # Railway/Nixpacks configuration
└── index.php          # Main router
```

## 🗄️ Database Setup

1. **Import Schema**: Use the SQL file in `database/agstation_schema.sql`
2. **Configure Connection**: Set environment variables for your database
3. **Test Connection**: Visit `/health` endpoint to verify connectivity

## 🏃‍♂️ Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/HalimaF/agstation-ecommerce.git
   cd agstation-ecommerce
   ```

2. **Set up database**
   - Create MySQL database
   - Import `database/agstation_schema.sql`

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

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- PDO PHP extension
- Web server (Apache/Nginx) or PHP built-in server

## 🔐 Default Admin Access

After setting up the database, you'll need to create an admin user. You can do this by:

1. Registering a regular user
2. Manually updating the database to set admin role
3. Or using the admin user creation script (if available)

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
