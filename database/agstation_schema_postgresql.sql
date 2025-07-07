-- PostgreSQL Schema for AG Station
-- This is a PostgreSQL version of the original MySQL schema

-- Table: Roles
CREATE TABLE Roles (
    role_id SERIAL PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Users
CREATE TABLE Users (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role_id INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table: WebsiteCustomers
CREATE TABLE WebsiteCustomers (
    customer_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    shipping_address TEXT,
    billing_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Brands
CREATE TABLE Brands (
    brand_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255),
    phone_number VARCHAR(50),
    website_url VARCHAR(255),
    authorized_reseller BOOLEAN DEFAULT FALSE,
    contract_document_url TEXT,
    category VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Pending' CHECK (status IN ('Active', 'Pending', 'Blacklisted')),
    notes TEXT
);

-- Table: Distributors
CREATE TABLE Distributors (
    distributor_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255),
    phone_number VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    website_url VARCHAR(255),
    credit_limit DECIMAL(15,2) DEFAULT 0.00,
    payment_terms INTEGER DEFAULT 30,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    tax_id VARCHAR(50),
    business_license VARCHAR(100),
    status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive', 'Suspended')),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add more tables as needed...
-- (Continue with the rest of your schema, converting MySQL syntax to PostgreSQL)

-- Create indexes for better performance
CREATE INDEX idx_users_email ON Users(email);
CREATE INDEX idx_websitecustomers_email ON WebsiteCustomers(email);
CREATE INDEX idx_brands_status ON Brands(status);
CREATE INDEX idx_distributors_status ON Distributors(status);

-- Insert default roles
INSERT INTO Roles (role_name) VALUES 
('Admin'),
('Staff'),
('Manager'),
('Viewer');

-- Insert default admin user (password: 'admin123' - change this!)
INSERT INTO Users (name, email, password_hash, role_id) VALUES 
('System Admin', 'admin@agstation.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
