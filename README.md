# AI Training Platform

## Overview
This is a web-based AI training platform designed to provide educational modules on artificial intelligence topics. The platform includes user authentication, module tracking, and certificate generation.

## Features
- User authentication (login/registration)
- Admin dashboard for content management
- Interactive AI training modules
- Progress tracking
- Certificate generation

## Installation
1. Clone the repository to your XAMPP htdocs folder
2. Import the database schema (if not using auto-creation)
3. Configure your database settings in `config.php` if needed

## Database Configuration
The application uses MySQL with the following default settings:
- Host: localhost
- Database: ai_training
- Username: root
- Password: (empty)

These settings can be overridden using environment variables:
- DB_HOST
- DB_NAME
- DB_USER
- DB_PASSWORD

## Test Users
The application comes with pre-configured test users:

### Regular User
- Email: test@example.com
- Password: password123

### Admin User
- Email: admin@example.com
- Password: admin123

