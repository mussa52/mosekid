CREATE DATABASE IF NOT EXISTS employee_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE employee_management;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_users_email (email(191))
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    position_id INT NOT NULL,
    employee_code VARCHAR(50) NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    date_of_birth DATE NOT NULL,
    national_id TEXT NOT NULL,
    phone_number TEXT NOT NULL,
    email TEXT NOT NULL,
    address TEXT NOT NULL,
    salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    employment_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    photo_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_employees_position FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_employees_name (first_name(191), last_name(191))
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    check_in DATETIME NULL,
    check_out DATETIME NULL,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'present',
    remarks TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_attendance_date (attendance_date)
);

CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    CONSTRAINT fk_leaves_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_leaves_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_leaves_status (status)
);

CREATE TABLE payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    salary_month VARCHAR(7) NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    allowances DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    deductions DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    net_salary DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'generated',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payroll_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_payroll_employee_month (employee_id, salary_month)
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login_attempts_email (email),
    INDEX idx_login_attempts_time (attempted_at)
);

CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_remember_tokens_hash (token_hash),
    INDEX idx_remember_tokens_user (user_id),
    CONSTRAINT fk_remember_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email TEXT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_password_resets_token (token(191))
);

INSERT INTO roles (name, description) VALUES
('admin', 'System administrator'),
('manager', 'Department manager'),
('employee', 'Regular employee');
