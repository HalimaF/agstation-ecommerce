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
('G.E.Design', 'info@gedesign.com', '555-0102', 'Tools', 'Active'),
('NatureCare', 'support@naturecare.com', '555-0103', 'Beauty & Personal Care', 'Active'),
('HomeComfort', 'hello@homecomfort.com', '555-0104', 'Home & Garden', 'Active'),
('SportsPro', 'contact@sportspro.com', '555-0105', 'Sports & Outdoors', 'Active'),
('EduTech', 'info@edutech.com', '555-0106', 'Books & Education', 'Active'),
('AutoMax', 'support@automax.com', '555-0107', 'Automotive', 'Active');

-- Insert sample distributors
INSERT INTO Distributors (name, contact_person, email, phone_number, address, country, status) VALUES
('Global Supply Co', 'Jane Smith', 'jane@globalsupply.com', '555-0201', '456 Distributor Ave, City, State 67890', 'USA', 'Active');

-- Insert sample products
INSERT INTO Products (asin, name, category, cost_price, retail_price, description, status, brand_id, distributor_id) VALUES
('B07S28X9KZ', 'Vivilux LED Light Strip', 'Electronics', 25.99, 45.99, 'High-quality LED light strip with remote control', 'Active', 1, 1),
('B0DB9Z88RG', 'Vivilux Smart Bulb', 'Electronics', 15.50, 29.99, 'Smart WiFi enabled LED bulb with app control', 'Active', 1, 1),
('B075H3MLR5', 'G.E.Design Agricultural Tool', 'Tools', 35.00, 65.99, 'Professional grade agricultural hand tool', 'Active', 2, 1),
('B08N5WRWNW', 'Wireless Bluetooth Headphones', 'Electronics', 45.00, 89.99, 'Premium noise-cancelling wireless headphones with 30-hour battery life', 'Active', 1, 1),
('B09D3KPT8L', 'Smart Home Security Camera', 'Electronics', 55.00, 119.99, '1080p HD indoor security camera with night vision and motion detection', 'Active', 1, 1),
('B08HJMWZDX', 'Portable Power Bank 20000mAh', 'Electronics', 22.50, 49.99, 'Fast charging power bank with LED display and multiple USB ports', 'Active', 1, 1),
('B07GNMKYCR', 'Professional Drill Set', 'Tools', 65.00, 129.99, 'Cordless drill with 50-piece accessory kit and carrying case', 'Active', 2, 1),
('B08K9HQXYZ', 'Garden Pruning Shears', 'Tools', 18.00, 34.99, 'Sharp bypass pruning shears with comfortable grip for gardening', 'Active', 2, 1),
('B09L7KTMNP', 'Smart Water Bottle', 'Health & Fitness', 28.00, 59.99, 'Temperature-controlled smart water bottle with hydration tracking', 'Active', 1, 1),
('B08Q5RTUVW', 'Yoga Mat Premium', 'Health & Fitness', 25.00, 54.99, 'Non-slip eco-friendly yoga mat with alignment guides', 'Active', 2, 1),
('B07X9YZABC', 'Wireless Charging Pad', 'Electronics', 15.99, 32.99, 'Fast wireless charger compatible with all Qi-enabled devices', 'Active', 1, 1),
('B08T6UVWXY', 'Stainless Steel Water Tumbler', 'Kitchen', 12.50, 24.99, 'Insulated tumbler keeps drinks hot for 12 hours, cold for 24 hours', 'Active', 2, 1),

-- Home & Garden Products
('B09M3NQRST', 'Ceramic Plant Pot Set', 'Home & Garden', 18.50, 39.99, 'Set of 3 modern ceramic plant pots with drainage holes and saucers', 'Active', 4, 1),
('B08L5JWXYZ', 'Solar Garden Lights', 'Home & Garden', 32.00, 69.99, 'Set of 8 solar-powered LED garden stake lights with auto on/off', 'Active', 4, 1),
('B07R6MNPQR', 'Bamboo Cutting Board Set', 'Kitchen', 24.00, 49.99, 'Set of 3 bamboo cutting boards with non-slip feet and juice grooves', 'Active', 4, 1),
('B09K8STUVW', 'Essential Oil Diffuser', 'Home & Garden', 28.50, 59.99, 'Ultrasonic aromatherapy diffuser with 7 LED colors and timer', 'Active', 4, 1),

-- Beauty & Personal Care Products
('B08J7LMNOP', 'Organic Face Moisturizer', 'Beauty & Personal Care', 16.00, 34.99, 'Natural organic face moisturizer with hyaluronic acid and vitamin E', 'Active', 3, 1),
('B09P2QRSTU', 'Vitamin C Serum', 'Beauty & Personal Care', 22.00, 44.99, 'Anti-aging vitamin C serum with retinol and niacinamide', 'Active', 3, 1),
('B08G5VWXYZ', 'Bamboo Toothbrush Set', 'Beauty & Personal Care', 8.50, 19.99, 'Set of 4 eco-friendly bamboo toothbrushes with soft bristles', 'Active', 3, 1),
('B07N4ABCDE', 'Shampoo and Conditioner Set', 'Beauty & Personal Care', 19.00, 39.99, 'Sulfate-free shampoo and conditioner for all hair types', 'Active', 3, 1),

-- Sports & Outdoors Products
('B08F3GHIJK', 'Resistance Bands Set', 'Sports & Outdoors', 14.50, 29.99, 'Set of 5 resistance bands with handles, door anchor, and carrying bag', 'Active', 5, 1),
('B09H6LMNOP', 'Camping Lantern LED', 'Sports & Outdoors', 21.00, 42.99, 'Rechargeable LED lantern with power bank function and SOS mode', 'Active', 5, 1),
('B08S4QRSTU', 'Hiking Backpack 40L', 'Sports & Outdoors', 45.00, 89.99, 'Lightweight hiking backpack with hydration system and rain cover', 'Active', 5, 1),
('B07T7VWXYZ', 'Foam Roller', 'Sports & Outdoors', 16.50, 34.99, 'High-density foam roller for muscle recovery and massage therapy', 'Active', 5, 1),

-- Books & Education Products
('B09E8ABCDE', 'Python Programming Book', 'Books & Education', 25.00, 49.99, 'Complete guide to Python programming for beginners and advanced users', 'Active', 6, 1),
('B08W9FGHIJ', 'Digital Drawing Tablet', 'Books & Education', 55.00, 109.99, 'Graphics tablet with pressure-sensitive pen for digital art and design', 'Active', 6, 1),
('B07Y1KLMNO', 'Scientific Calculator', 'Books & Education', 18.00, 35.99, 'Advanced scientific calculator with graphing capabilities', 'Active', 6, 1),
('B09Z2PQRST', 'Chess Set Premium', 'Books & Education', 32.00, 64.99, 'Handcrafted wooden chess set with folding board and felt interior', 'Active', 6, 1),

-- Automotive Products
('B08A3UVWXY', 'Car Phone Mount', 'Automotive', 12.00, 24.99, 'Magnetic car phone mount with 360-degree rotation and strong grip', 'Active', 7, 1),
('B09B4ZABCD', 'Tire Pressure Gauge', 'Automotive', 8.50, 17.99, 'Digital tire pressure gauge with backlit display and auto shut-off', 'Active', 7, 1),
('B07C5EFGHI', 'Car Air Freshener Set', 'Automotive', 6.00, 14.99, 'Set of 6 long-lasting car air fresheners in various scents', 'Active', 7, 1),
('B08D6JKLMN', 'Emergency Car Kit', 'Automotive', 38.00, 79.99, 'Complete roadside emergency kit with jumper cables, flashlight, and tools', 'Active', 7, 1),

-- Additional Electronics
('B09Q7OPQRS', 'Bluetooth Speaker Waterproof', 'Electronics', 35.00, 69.99, 'Portable waterproof Bluetooth speaker with 20-hour battery life', 'Active', 1, 1),
('B08R8TUVWX', 'Smartphone Stand Adjustable', 'Electronics', 11.50, 22.99, 'Adjustable aluminum phone stand compatible with all devices', 'Active', 1, 1),
('B07S9YZABC', 'USB Cable Multi-Pack', 'Electronics', 9.99, 19.99, 'Pack of 3 USB cables - Lightning, USB-C, and Micro-USB', 'Active', 1, 1),
('B09T1DEFGH', 'Smart Watch Fitness Tracker', 'Electronics', 68.00, 139.99, 'Fitness smartwatch with heart rate monitor, GPS, and sleep tracking', 'Active', 1, 1);

-- Update products with local image paths
UPDATE Products SET image_url = 'vivilux-led-strip.svg' WHERE asin = 'B07S28X9KZ';
UPDATE Products SET image_url = 'vivilux-smart-bulb.svg' WHERE asin = 'B0DB9Z88RG';
UPDATE Products SET image_url = 'ge-design-tool.svg' WHERE asin = 'B075H3MLR5';
UPDATE Products SET image_url = 'bluetooth-headphones.svg' WHERE asin = 'B08N5WRWNW';
UPDATE Products SET image_url = 'security-camera.svg' WHERE asin = 'B09D3KPT8L';
UPDATE Products SET image_url = 'power-bank.svg' WHERE asin = 'B08HJMWZDX';
UPDATE Products SET image_url = 'drill-set.svg' WHERE asin = 'B07GNMKYCR';
UPDATE Products SET image_url = 'pruning-shears.svg' WHERE asin = 'B08K9HQXYZ';
UPDATE Products SET image_url = 'smart-water-bottle.svg' WHERE asin = 'B09L7KTMNP';
UPDATE Products SET image_url = 'yoga-mat.svg' WHERE asin = 'B08Q5RTUVW';
UPDATE Products SET image_url = 'wireless-charger.svg' WHERE asin = 'B07X9YZABC';
UPDATE Products SET image_url = 'water-tumbler.svg' WHERE asin = 'B08T6UVWXY';

-- New product images
UPDATE Products SET image_url = 'plant-pot-set.svg' WHERE asin = 'B09M3NQRST';
UPDATE Products SET image_url = 'solar-garden-lights.svg' WHERE asin = 'B08L5JWXYZ';
UPDATE Products SET image_url = 'bamboo-cutting-board.svg' WHERE asin = 'B07R6MNPQR';
UPDATE Products SET image_url = 'essential-oil-diffuser.svg' WHERE asin = 'B09K8STUVW';
UPDATE Products SET image_url = 'face-moisturizer.svg' WHERE asin = 'B08J7LMNOP';
UPDATE Products SET image_url = 'vitamin-c-serum.svg' WHERE asin = 'B09P2QRSTU';
UPDATE Products SET image_url = 'bamboo-toothbrush.svg' WHERE asin = 'B08G5VWXYZ';
UPDATE Products SET image_url = 'shampoo-conditioner.svg' WHERE asin = 'B07N4ABCDE';
UPDATE Products SET image_url = 'resistance-bands.svg' WHERE asin = 'B08F3GHIJK';
UPDATE Products SET image_url = 'camping-lantern.svg' WHERE asin = 'B09H6LMNOP';
UPDATE Products SET image_url = 'hiking-backpack.svg' WHERE asin = 'B08S4QRSTU';
UPDATE Products SET image_url = 'foam-roller.svg' WHERE asin = 'B07T7VWXYZ';
UPDATE Products SET image_url = 'python-programming-book.svg' WHERE asin = 'B09E8ABCDE';
UPDATE Products SET image_url = 'drawing-tablet.svg' WHERE asin = 'B08W9FGHIJ';
UPDATE Products SET image_url = 'scientific-calculator.svg' WHERE asin = 'B07Y1KLMNO';
UPDATE Products SET image_url = 'chess-set.svg' WHERE asin = 'B09Z2PQRST';
UPDATE Products SET image_url = 'car-phone-mount.svg' WHERE asin = 'B08A3UVWXY';
UPDATE Products SET image_url = 'tire-pressure-gauge.svg' WHERE asin = 'B09B4ZABCD';
UPDATE Products SET image_url = 'car-air-freshener.svg' WHERE asin = 'B07C5EFGHI';
UPDATE Products SET image_url = 'emergency-car-kit.svg' WHERE asin = 'B08D6JKLMN';
UPDATE Products SET image_url = 'bluetooth-speaker.svg' WHERE asin = 'B09Q7OPQRS';
UPDATE Products SET image_url = 'phone-stand.svg' WHERE asin = 'B08R8TUVWX';
UPDATE Products SET image_url = 'usb-cable-pack.svg' WHERE asin = 'B07S9YZABC';
UPDATE Products SET image_url = 'smart-watch.svg' WHERE asin = 'B09T1DEFGH';

-- Insert sample customer
INSERT INTO WebsiteCustomers (name, email, password_hash, phone, shipping_address, billing_address) VALUES
('John Customer', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0301', 
'789 Customer St, City, State 12345', '789 Customer St, City, State 12345');

-- Insert sample inventory
INSERT INTO Inventory (product_id, warehouse_id, quantity) VALUES
('B07S28X9KZ', 1, 150),
('B0DB9Z88RG', 1, 200),
('B075H3MLR5', 1, 75),
('B08N5WRWNW', 1, 85),
('B09D3KPT8L', 1, 45),
('B08HJMWZDX', 1, 120),
('B07GNMKYCR', 1, 30),
('B08K9HQXYZ', 1, 95),
('B09L7KTMNP', 1, 60),
('B08Q5RTUVW', 1, 110),
('B07X9YZABC', 1, 180),
('B08T6UVWXY', 1, 140),
('B09M3NQRST', 1, 65),
('B08L5JWXYZ', 1, 40),
('B07R6MNPQR', 1, 90),
('B09K8STUVW', 1, 55),
('B08J7LMNOP', 1, 125),
('B09P2QRSTU', 1, 80),
('B08G5VWXYZ', 1, 200),
('B07N4ABCDE', 1, 70),
('B08F3GHIJK', 1, 155),
('B09H6LMNOP', 1, 35),
('B08S4QRSTU', 1, 25),
('B07T7VWXYZ', 1, 100),
('B09E8ABCDE', 1, 50),
('B08W9FGHIJ', 1, 30),
('B07Y1KLMNO', 1, 85),
('B09Z2PQRST', 1, 20),
('B08A3UVWXY', 1, 175),
('B09B4ZABCD', 1, 145),
('B07C5EFGHI', 1, 220),
('B08D6JKLMN', 1, 18),
('B09Q7OPQRS', 1, 95),
('B08R8TUVWX', 1, 160),
('B07S9YZABC', 1, 190),
('B09T1DEFGH', 1, 42);

-- Insert sample product reviews
INSERT INTO ProductReviews (product_id, source, rating, review_text) VALUES
('B07S28X9KZ', 'Website', 5, 'Excellent LED strip, very bright and easy to install!'),
('B0DB9Z88RG', 'Amazon', 4, 'Good smart bulb, works well with the app.'),
('B075H3MLR5', 'Website', 5, 'Great tool for agricultural work, very durable.'),
('B08N5WRWNW', 'Website', 5, 'Amazing sound quality! Battery lasts exactly as advertised.'),
('B08N5WRWNW', 'Amazon', 4, 'Comfortable to wear for long periods, great noise cancellation.'),
('B09D3KPT8L', 'Website', 4, 'Clear video quality, easy to set up. Motion alerts work perfectly.'),
('B08HJMWZDX', 'Amazon', 5, 'Charges my phone 4-5 times. Perfect for travel!'),
('B07GNMKYCR', 'Website', 5, 'Professional quality drill, comes with everything you need.'),
('B08K9HQXYZ', 'Website', 4, 'Sharp and comfortable grip. Makes pruning so much easier.'),
('B09L7KTMNP', 'Amazon', 4, 'Love the temperature control feature. Keeps water perfect all day.'),
('B08Q5RTUVW', 'Website', 5, 'Best yoga mat I have ever owned. Great grip and cushioning.'),
('B07X9YZABC', 'Amazon', 4, 'Fast charging, works through phone cases. Very convenient.'),
('B08T6UVWXY', 'Website', 5, 'Keeps drinks cold for hours. Fits perfectly in car cup holder.'),

-- Home & Garden Reviews
('B09M3NQRST', 'Website', 5, 'Beautiful ceramic pots! Perfect size for my succulents.'),
('B08L5JWXYZ', 'Amazon', 4, 'Solar lights work great, automatically turn on at dusk.'),
('B07R6MNPQR', 'Website', 5, 'High-quality bamboo boards, much better than plastic ones.'),
('B09K8STUVW', 'Website', 4, 'Love the color-changing LED feature. Makes my room smell amazing.'),

-- Beauty & Personal Care Reviews
('B08J7LMNOP', 'Amazon', 5, 'My skin feels so much softer after using this moisturizer.'),
('B09P2QRSTU', 'Website', 4, 'Noticed improvement in skin texture after 2 weeks of use.'),
('B08G5VWXYZ', 'Website', 5, 'Eco-friendly and gentle on gums. Great value for 4 brushes.'),
('B07N4ABCDE', 'Amazon', 4, 'Hair feels clean and healthy. No harsh chemicals.'),

-- Sports & Outdoors Reviews
('B08F3GHIJK', 'Website', 5, 'Great resistance bands! Perfect for home workouts.'),
('B09H6LMNOP', 'Amazon', 4, 'Bright lantern with long battery life. Perfect for camping.'),
('B08S4QRSTU', 'Website', 5, 'Comfortable backpack with lots of storage space.'),
('B07T7VWXYZ', 'Website', 4, 'Firm roller that really helps with muscle recovery.'),

-- Books & Education Reviews
('B09E8ABCDE', 'Amazon', 5, 'Excellent book for learning Python. Clear explanations.'),
('B08W9FGHIJ', 'Website', 4, 'Responsive tablet, great for digital art. Good value.'),
('B07Y1KLMNO', 'Website', 5, 'Perfect calculator for engineering students. Easy to use.'),
('B09Z2PQRST', 'Amazon', 5, 'Beautiful chess set. Great craftsmanship and smooth pieces.'),

-- Automotive Reviews
('B08A3UVWXY', 'Website', 4, 'Strong magnetic hold. Phone stays secure even on bumpy roads.'),
('B09B4ZABCD', 'Amazon', 5, 'Accurate readings and easy to read display. Very reliable.'),
('B07C5EFGHI', 'Website', 4, 'Long-lasting scents. Variety pack is great value.'),
('B08D6JKLMN', 'Website', 5, 'Complete emergency kit. Hope I never need it but glad I have it.'),

-- Additional Electronics Reviews
('B09Q7OPQRS', 'Amazon', 5, 'Excellent sound quality and truly waterproof. Perfect for pool parties.'),
('B08R8TUVWX', 'Website', 4, 'Sturdy stand that holds phone at perfect angle. Very adjustable.'),
('B07S9YZABC', 'Website', 5, 'Good quality cables that charge fast. Great to have extras.'),
('B09T1DEFGH', 'Amazon', 4, 'Accurate fitness tracking. Battery lasts about 5 days with regular use.');

