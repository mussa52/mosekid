# Employee Management System

A pure PHP, PDO-based Employee Management System structured with an MVC-like folder organization.

## Features
- Authentication and role-based access control
- Employee, department, and position management
- Attendance, leave, and payroll modules
- Search, validation, reporting, and encryption support

## Setup
1. Create a MySQL database named employee_management.
2. Import the SQL script from sql/schema.sql.
3. Update config/.env with database credentials and APP_ENCRYPTION_KEY.
4. Serve the project from the workspace root with Apache or PHP built-in server.

## Security Notes
- Passwords are hashed with password_hash().
- Sensitive fields are encrypted with AES-256-CBC before storage.
- Prepared statements are used for database access.
- CSRF protection is included for form submissions.
