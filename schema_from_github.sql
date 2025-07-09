-- PostgreSQL Schema for AG Station
-- Converted from MySQL to PostgreSQL syntax

-- Create ENUM types first
CREATE TYPE role_status AS ENUM ('Active', 'Inactive');
CREATE TYPE order_status AS ENUM ('Pending', 'Shipped', 'Delivered', 'Cancelled');
CREATE TYPE payment_status AS ENUM ('Paid', 'Unpaid', 'Refunded');
CREATE TYPE payment_method AS ENUM ('Card', 'PayPal', 'Bank Transfer', 'Credit Card', 'Check');
CREATE TYPE brand_status AS ENUM ('Active', 'Pending', 'Blacklisted');
CREATE TYPE distributor_status AS ENUM ('Active', 'Inactive', 'Blacklisted');
CREATE TYPE product_status AS ENUM ('Active', 'Inactive');
CREATE TYPE shipment_status AS ENUM ('Pending', 'Received', 'Delayed', 'Shipped', 'In Transit', 'Delivered');
CREATE TYPE return_status AS ENUM ('Requested', 'Approved', 'Rejected', 'Refunded');
CREATE TYPE return_resolution AS ENUM ('Refund', 'Replacement', 'Store Credit');
CREATE TYPE invoice_status AS ENUM ('Paid', 'Unpaid', 'Overdue');
CREATE TYPE warehouse_type AS ENUM ('Amazon FBA', 'Prep Center');
CREATE TYPE service_type AS ENUM ('Repricing', 'Analytics', 'Freight', 'Automation', 'Prep Center');
CREATE TYPE service_status AS ENUM ('Active', 'Inactive');
CREATE TYPE billing_cycle AS ENUM ('Monthly', 'Annually', 'One-Time');
CREATE TYPE payment_process_status AS ENUM ('Pending', 'Processed', 'Failed');
CREATE TYPE review_source AS ENUM ('Amazon', 'Website');

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
    status role_status DEFAULT 'Active',
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
    status brand_status DEFAULT 'Pending',
    notes TEXT
);

-- Table: Distributors
CREATE TABLE Distributors (
    distributor_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone_number VARCHAR(50),
    address TEXT,
    country VARCHAR(100),
    website_url VARCHAR(255),
    business_license_no VARCHAR(100),
    status distributor_status DEFAULT 'Active',
    notes TEXT
);

-- Table: Products
CREATE TABLE Products (
    asin VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    upc VARCHAR(100),
    brand_id INTEGER,
    distributor_id INTEGER,
    category VARCHAR(100),
    cost_price DECIMAL(10,2),
    retail_price DECIMAL(10,2),
    description TEXT,
    image_url TEXT,
    status product_status DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (distributor_id) REFERENCES Distributors(distributor_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: WebsiteOrders
CREATE TABLE WebsiteOrders (
    order_id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status order_status DEFAULT 'Pending',
    total_amount DECIMAL(10,2),
    payment_status payment_status DEFAULT 'Unpaid',
    FOREIGN KEY (customer_id) REFERENCES WebsiteCustomers(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: OrderItems
CREATE TABLE OrderItems (
    item_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    product_id VARCHAR(20) NOT NULL,
    quantity INTEGER NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES WebsiteOrders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(asin)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: Payments
CREATE TABLE Payments (
    payment_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    method payment_method NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status payment_status DEFAULT 'Paid',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES WebsiteOrders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: CustomerShipments
CREATE TABLE CustomerShipments (
    shipment_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    carrier VARCHAR(255),
    tracking_number VARCHAR(255),
    status shipment_status DEFAULT 'Shipped',
    FOREIGN KEY (order_id) REFERENCES WebsiteOrders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: Returns
CREATE TABLE Returns (
    return_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    product_id VARCHAR(20),
    return_date TIMESTAMP,
    reason TEXT,
    status return_status DEFAULT 'Requested',
    resolution return_resolution,
    refund_amount DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES WebsiteOrders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(asin)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: CustomerInvoices
CREATE TABLE CustomerInvoices (
    invoice_id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    issue_date TIMESTAMP,
    due_date TIMESTAMP,
    total_amount DECIMAL(10,2),
    status invoice_status DEFAULT 'Unpaid',
    items TEXT,
    FOREIGN KEY (customer_id) REFERENCES WebsiteCustomers(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: Warehouse
CREATE TABLE Warehouse (
    warehouse_id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    type warehouse_type,
    address TEXT,
    contact_person VARCHAR(255),
    phone VARCHAR(50)
);

-- Table: ShipmentsFromSuppliers
CREATE TABLE ShipmentsFromSuppliers (
    shipment_id SERIAL PRIMARY KEY,
    shipment_cost DECIMAL(10,2),
    distributor_id INTEGER,
    brand_id INTEGER,
    products_sent TEXT,
    shipment_date TIMESTAMP,
    warehouse_id INTEGER,
    tracking_number VARCHAR(255),
    status shipment_status DEFAULT 'Pending',
    FOREIGN KEY (distributor_id) REFERENCES Distributors(distributor_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES Warehouse(warehouse_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: ThirdPartyServices
CREATE TABLE ThirdPartyServices (
    service_id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    type service_type,
    account_id INTEGER,
    warehouse_id INTEGER,
    contact_email VARCHAR(255),
    api_key TEXT,
    base_url VARCHAR(255),
    status service_status DEFAULT 'Active',
    subscription_cost DECIMAL(10,2),
    billing_cycle billing_cycle,
    integration_date TIMESTAMP,
    last_synced TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (warehouse_id) REFERENCES Warehouse(warehouse_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: SupplierPayments
CREATE TABLE SupplierPayments (
    payment_id SERIAL PRIMARY KEY,
    amount DECIMAL(10,2) NOT NULL,
    method payment_method NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status payment_process_status DEFAULT 'Pending'
);

-- Table: SellerInvoices
CREATE TABLE SellerInvoices (
    invoice_id SERIAL PRIMARY KEY,
    payment_id INTEGER,
    brand_id INTEGER,
    distributor_id INTEGER,
    service_id INTEGER,
    issue_date TIMESTAMP,
    due_date TIMESTAMP,
    total_amount DECIMAL(10,2),
    status invoice_status DEFAULT 'Unpaid',
    items TEXT,
    FOREIGN KEY (payment_id) REFERENCES SupplierPayments(payment_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (distributor_id) REFERENCES Distributors(distributor_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (service_id) REFERENCES ThirdPartyServices(service_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: Expenses
CREATE TABLE Expenses (
    expense_id SERIAL PRIMARY KEY,
    type VARCHAR(100),
    amount DECIMAL(10,2),
    paid_to VARCHAR(255),
    date DATE,
    notes TEXT,
    service_id INTEGER,
    invoice_id INTEGER,
    FOREIGN KEY (service_id) REFERENCES ThirdPartyServices(service_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES SellerInvoices(invoice_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: Inventory
CREATE TABLE Inventory (
    inventory_id SERIAL PRIMARY KEY,
    product_id VARCHAR(20) NOT NULL,
    warehouse_id INTEGER NOT NULL,
    quantity INTEGER DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(asin)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES Warehouse(warehouse_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: ProductReviews
CREATE TABLE ProductReviews (
    review_id SERIAL PRIMARY KEY,
    product_id VARCHAR(20) NOT NULL,
    source review_source,
    rating INTEGER CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(asin)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON Users(email);
CREATE INDEX idx_websitecustomers_email ON WebsiteCustomers(email);
CREATE INDEX idx_brands_status ON Brands(status);
CREATE INDEX idx_distributors_status ON Distributors(status);
CREATE INDEX idx_products_status ON Products(status);
CREATE INDEX idx_orders_customer ON WebsiteOrders(customer_id);
CREATE INDEX idx_orders_status ON WebsiteOrders(status);
CREATE INDEX idx_orderitems_order ON OrderItems(order_id);
CREATE INDEX idx_orderitems_product ON OrderItems(product_id);
CREATE INDEX idx_payments_order ON Payments(order_id);
CREATE INDEX idx_inventory_product ON Inventory(product_id);
CREATE INDEX idx_inventory_warehouse ON Inventory(warehouse_id);
CREATE INDEX idx_reviews_product ON ProductReviews(product_id);

-- Insert default roles
INSERT INTO Roles (role_name) VALUES 
('Admin'),
('Staff'),
('Manager'),
('Customer');

-- Insert default admin user (password: 'admin123' - change this!)
-- Password hash for 'admin123' using bcrypt
INSERT INTO Users (name, email, password_hash, role_id) VALUES 
('System Admin', 'admin@agstation.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample warehouse
INSERT INTO Warehouse (name, type, address, contact_person, phone) VALUES
('Main Warehouse', 'Amazon FBA', '123 Warehouse St, City, State 12345', 'John Doe', '555-0123');

-- Insert sample brands
INSERT INTO Brands (name, contact_email, phone_number, category, status) VALUES
('Vivilux', 'contact@vivilux.com', '555-0101', 'Electronics', 'Active'),
('G.E.Design', 'info@gedesign.com', '555-0102', 'Tools', 'Active');

-- Insert sample distributors
INSERT INTO Distributors (name, contact_person, email, phone_number, address, country, status) VALUES
('Global Supply Co', 'Jane Smith', 'jane@globalsupply.com', '555-0201', '456 Distributor Ave, City, State 67890', 'USA', 'Active');

-- Insert sample products
INSERT INTO Products (asin, name, category, cost_price, retail_price, description, status, brand_id, distributor_id) VALUES
('B07S28X9KZ', 'Vivilux LED Light Strip', 'Electronics', 25.99, 45.99, 'High-quality LED light strip with remote control', 'Active', 1, 1),
('B0DB9Z88RG', 'Vivilux Smart Bulb', 'Electronics', 15.50, 29.99, 'Smart WiFi enabled LED bulb with app control', 'Active', 1, 1),
('B075H3MLR5', 'G.E.Design Agricultural Tool', 'Tools', 35.00, 65.99, 'Professional grade agricultural hand tool', 'Active', 2, 1);

-- Insert sample customer
INSERT INTO WebsiteCustomers (name, email, password_hash, phone, shipping_address, billing_address) VALUES
('John Customer', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0301', 
'789 Customer St, City, State 12345', '789 Customer St, City, State 12345');

-- Insert sample inventory
INSERT INTO Inventory (product_id, warehouse_id, quantity) VALUES
('B07S28X9KZ', 1, 150),
('B0DB9Z88RG', 1, 200),
('B075H3MLR5', 1, 75);

-- Insert sample product reviews
INSERT INTO ProductReviews (product_id, source, rating, review_text) VALUES
('B07S28X9KZ', 'Website', 5, 'Excellent LED strip, very bright and easy to install!'),
('B0DB9Z88RG', 'Amazon', 4, 'Good smart bulb, works well with the app.'),
('B075H3MLR5', 'Website', 5, 'Great tool for agricultural work, very durable.');

