# Deployment Guide for AI Training Platform

## Environment Variables

When deploying this application, you need to set the following environment variables on your hosting platform (like Render or GitHub Pages with a backend service):

```
DB_HOST=your_database_host
DB_NAME=your_database_name
DB_USER=your_database_username
DB_PASSWORD=your_database_password
```

## Render Deployment Steps

1. **Create a new Web Service**
   - Connect your GitHub repository
   - Select the repository containing this project

2. **Configure the service**
   - Name: `ai-training` (or your preferred name)
   - Environment: `Docker`
   - Region: Choose the closest to your users
   - Branch: `main` (or your deployment branch)

3. **Add Environment Variables**
   - Click on "Environment" tab
   - Add the environment variables listed above with your database credentials

4. **Database Setup**
   - Create a MySQL database service on Render or use an external MySQL provider
   - Make sure your database service is accessible from your web service
   - Use the database credentials in your environment variables

5. **Deploy**
   - Click "Create Web Service"
   - Wait for the build and deployment to complete

## Troubleshooting

### Login Issues

If you're experiencing login issues on the deployed version:

1. **Check Database Connection**
   - Verify that your database environment variables are correctly set
   - Ensure your database is accessible from your web service
   - Use the included `test_connection.html` page to diagnose database connectivity issues
   - You can also directly access `test_db.php` to check database connection status

2. **Check Error Logs**
   - On Render, check the logs in the "Logs" tab of your web service
   - Look for any database connection errors or PHP errors
   - The application is configured to log detailed error information

3. **CORS Issues**
   - If you're seeing CORS errors in the browser console, the application now includes improved CORS headers
   - The CORS configuration supports credentials and handles preflight requests correctly
   - Use the `test_connection.html` page to test CORS functionality

4. **Database Initialization**
   - The first time you access the application, it will attempt to create the necessary tables
   - The improved `test_db.php` script can now attempt to create the database if it doesn't exist
   - If automatic creation fails, you may need to manually create the tables using the SQL in `config.php`

5. **Authentication Debugging**
   - The authentication system now includes enhanced logging
   - Check browser console for detailed API request/response information
   - The `test_connection.html` page includes an authentication test tool

### Common Issues

1. **Database Connection Refused**
   - Ensure your database service is running
   - Check if your web service can reach your database service (network rules)
   - Verify the database credentials are correct
   - Use the `test_db.php` script which now includes improved error handling and diagnostics

2. **PHP Extensions**
   - The Docker image has been updated to include additional PHP extensions (intl, etc.)
   - PHP configuration has been optimized for production environments
   - If you need additional extensions, modify the Dockerfile

3. **File Permissions**
   - If you're seeing file permission errors, ensure your web service has the correct permissions to read/write files
   - The Dockerfile now sets appropriate permissions for log files

4. **CORS and Authentication Issues**
   - The application now includes improved CORS handling with support for credentials
   - Authentication requests now include detailed logging and error reporting
   - Use the `test_connection.html` tool to diagnose API connectivity issues

### Testing Tools

The application now includes several diagnostic tools to help troubleshoot deployment issues:

1. **test_connection.html**
   - A browser-based tool for testing API connectivity, CORS, and authentication
   - Provides detailed information about request/response headers and data
   - Helps diagnose common deployment issues

2. **test_db.php**
   - A PHP script that tests database connectivity and provides detailed diagnostics
   - Can attempt to create the database if it doesn't exist
   - Returns JSON with detailed information about the database connection

3. **env_test.php**
   - Tests if environment variables are properly set in the deployment environment
   - Shows server information and configuration details
   - Useful for verifying that your hosting provider is correctly setting environment variables

4. **health_check.php**
   - Provides a comprehensive health check of the application
   - Verifies PHP version, database connection, required extensions, and file permissions
   - Returns a JSON response with detailed status information
   - Can be used for monitoring and alerting in production environments

5. **phpinfo.php**
   - Shows detailed PHP configuration information
   - Useful for verifying PHP extensions and settings

## Local Development

For local development, the application will use the default values:

```
DB_HOST=localhost
DB_NAME=ai_training
DB_USER=root
DB_PASSWORD=
```

Make sure you have a local MySQL server running with these credentials, or update the default values in `config.php`.