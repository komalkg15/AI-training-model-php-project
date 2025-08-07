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

## Troubleshooting
If you encounter issues with the application:

1. Check the Apache error logs at `C:\xampp\apache\logs\error.log`
2. Check the application logs at `logs/app_YYYY-MM-DD.log`
3. Use the test connection page at `test_connection.html` to verify API connectivity
4. Use the login test page at `login_test.html` to verify authentication

## Common Issues

### 404 Not Found Errors
If you encounter 404 errors when trying to access API endpoints, ensure that the JavaScript code is using the correct URL paths. The API URLs should include the full path to the endpoint, including the subdirectory (e.g., `/ai-training/auth.php`).

### Database Connection Issues
If you encounter database connection issues, verify that:
1. MySQL service is running
2. Database credentials are correct
3. The `ai_training` database exists

You can use the `test_db.php` script to verify database connectivity.

## License
This project is licensed under the MIT License - see the LICENSE file for details.