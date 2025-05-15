-- Database schema for the 2nd Phone Shop

-- Keeps track of important actions and changes within the system.
CREATE TABLE `audit_trail` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `user_email_snapshot` VARCHAR(100) NULL COMMENT 'Email of the user at the time of action, if applicable',
  `activity_type` VARCHAR(100) NOT NULL COMMENT 'e.g., USER_LOGIN, PRODUCT_UPDATE, ORDER_PLACED',
  `target_entity` VARCHAR(50) NULL COMMENT 'e.g., product, user, order',
  `target_id` INT NULL COMMENT 'ID of the entity affected',
  `description` TEXT NOT NULL COMMENT 'Detailed description of the activity',
  `details_before` TEXT NULL COMMENT 'JSON snapshot of data before change',
  `details_after` TEXT NULL COMMENT 'JSON snapshot of data after change',
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stores user account information, credentials, and roles.
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(50) NOT NULL,
  `middle_name` VARCHAR(50) NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `mobile_number` VARCHAR(20) UNIQUE NULL,
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'Stored hashed password',
  `role` VARCHAR(20) NOT NULL DEFAULT 'customer' COMMENT 'e.g., customer, admin',
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `email_verified_at` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product categories for organizing items in the shop.
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(120) NOT NULL UNIQUE COMMENT 'URL-friendly identifier',
  `description` TEXT NULL,
  `parent_category_id` INT NULL,
  `image_url` VARCHAR(255) NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `display_order` INT DEFAULT 0,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detailed information about each product available for sale.
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(280) NOT NULL UNIQUE COMMENT 'URL-friendly identifier',
  `sku` VARCHAR(100) UNIQUE NULL COMMENT 'Stock Keeping Unit',
  `description` TEXT NULL,
  `brand` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `condition` VARCHAR(50) NULL COMMENT 'e.g., New, Used - Like New',
  `price` DECIMAL(10, 2) NOT NULL,
  `sale_price` DECIMAL(10, 2) NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `is_featured` BOOLEAN DEFAULT FALSE,
  `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Whether the product is visible in the shop',
  `main_image_url` VARCHAR(255) NULL,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_products_name` (`name`),
  INDEX `idx_products_is_featured` (`is_featured`),
  INDEX `idx_products_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stores multiple images associated with each product.
CREATE TABLE `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) NULL,
  `is_thumbnail` BOOLEAN DEFAULT FALSE,
  `display_order` INT DEFAULT 0,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Allows users to store multiple shipping and billing addresses.
CREATE TABLE `user_addresses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address_type` ENUM('shipping', 'billing') NOT NULL DEFAULT 'shipping',
  `is_default` BOOLEAN DEFAULT FALSE,
  `recipient_name` VARCHAR(100) NOT NULL,
  `company_name` VARCHAR(100) NULL,
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state_province_region` VARCHAR(100) NOT NULL,
  `postal_code` VARCHAR(20) NOT NULL,
  `country_code` VARCHAR(3) NOT NULL COMMENT 'ISO 3166-1 alpha-3 country code',
  `phone_number` VARCHAR(20) NULL,
  `delivery_instructions` TEXT NULL,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Records of customer orders, including payment and shipping status.
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `order_number` VARCHAR(32) UNIQUE NOT NULL COMMENT 'Publicly visible order identifier',
  `shipping_address_id` INT NOT NULL,
  `billing_address_id` INT NOT NULL,
  `subtotal_amount` DECIMAL(10, 2) NOT NULL,
  `shipping_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50) NULL,
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` VARCHAR(100) NULL UNIQUE,
  `order_status` ENUM('pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_payment',
  `customer_notes` TEXT NULL,
  `admin_notes` TEXT NULL,
  `shipping_carrier` VARCHAR(50) NULL,
  `tracking_number` VARCHAR(100) NULL,
  `date_ordered` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_shipped` DATETIME NULL,
  `date_delivered` DATETIME NULL,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`shipping_address_id`) REFERENCES `user_addresses`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`billing_address_id`) REFERENCES `user_addresses`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_orders_status` (`order_status`),
  INDEX `idx_orders_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Junction table linking products to orders, detailing items in each order.
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NULL COMMENT 'Allow NULL if product is deleted but order history needs to be maintained',
  `product_name_snapshot` VARCHAR(255) NOT NULL COMMENT 'Name of product at time of purchase',
  `product_sku_snapshot` VARCHAR(100) COMMENT 'SKU of product at time of purchase',
  `quantity` INT NOT NULL,
  `price_at_purchase` DECIMAL(10, 2) NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stores items currently in a user's shopping cart.
CREATE TABLE `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL COMMENT 'Null if guest cart',
  `session_id` VARCHAR(128) NULL COMMENT 'For guest carts, using PHP session ID or similar',
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uq_user_product` (`user_id`, `product_id`),
  UNIQUE KEY `uq_session_product` (`session_id`, `product_id`),
  INDEX `idx_cart_session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer reviews and ratings for products.
CREATE TABLE `product_reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL COMMENT 'Rating from 1 to 5',
  `title` VARCHAR(255) NULL,
  `comment` TEXT NULL,
  `is_approved` BOOLEAN DEFAULT FALSE COMMENT 'For moderation by admin',
  `approved_by_user_id` INT NULL,
  `date_reviewed` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`approved_by_user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CHECK (`rating` >= 1 AND `rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stores messages submitted through the 'Contact Us' form.
CREATE TABLE `contact_submissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(20) NULL,
  `subject` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `submission_ip` VARCHAR(45) NULL,
  `status` ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
  `date_submitted` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example: Adding FK constraints after all tables are defined if there are circular dependencies
-- (Not strictly needed here with current design but good to keep in mind)
-- ALTER TABLE `audit_trail` ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Table for homepage hero section
CREATE TABLE IF NOT EXISTS homepage_hero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    image_url VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for homepage reviews
CREATE TABLE IF NOT EXISTS homepage_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reviewer_name VARCHAR(100),
    avatar_url VARCHAR(255),
    review_text TEXT,
    rating INT,
    is_active BOOLEAN DEFAULT 1
);

-- Table for homepage brands
CREATE TABLE IF NOT EXISTS homepage_brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    logo_url VARCHAR(255),
    alt_text VARCHAR(100),
    is_active BOOLEAN DEFAULT 1
); 

CREATE TABLE site_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mission TEXT,
    vision TEXT
);

CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
    setting_type VARCHAR(20) NOT NULL DEFAULT 'text',
    setting_label VARCHAR(255) NOT NULL,
    setting_description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO site_settings (setting_key, setting_value, setting_group, setting_type, setting_label, setting_description, is_public) VALUES
-- General Settings
('site_name', '2nd Phone Shop', 'general', 'text', 'Site Name', 'The name of your website', TRUE),
('site_description', 'Your trusted source for quality second-hand phones', 'general', 'textarea', 'Site Description', 'A brief description of your website', TRUE),
('contact_email', 'contact@example.com', 'general', 'email', 'Contact Email', 'Primary contact email address', TRUE),
('contact_phone', '+1234567890', 'general', 'text', 'Contact Phone', 'Primary contact phone number', TRUE),
('address', '123 Main Street, City, Country', 'general', 'textarea', 'Business Address', 'Physical business address', TRUE),

-- Social Media
('facebook_url', '', 'social', 'url', 'Facebook URL', 'Your Facebook page URL', TRUE),
('instagram_url', '', 'social', 'url', 'Instagram URL', 'Your Instagram profile URL', TRUE),
('twitter_url', '', 'social', 'url', 'Twitter URL', 'Your Twitter profile URL', TRUE),

-- Shop Settings
('currency_symbol', '₱', 'shop', 'text', 'Currency Symbol', 'Currency symbol to display with prices', TRUE),
('tax_rate', '12', 'shop', 'number', 'Tax Rate (%)', 'Default tax rate for products', FALSE),
('shipping_fee', '100', 'shop', 'number', 'Default Shipping Fee', 'Default shipping fee for orders', FALSE),
('min_order_amount', '500', 'shop', 'number', 'Minimum Order Amount', 'Minimum amount required for orders', FALSE),

-- SEO Settings
('meta_title', '2nd Phone Shop - Quality Second-hand Phones', 'seo', 'text', 'Meta Title', 'Default meta title for pages', TRUE),
('meta_description', 'Find quality second-hand phones at great prices', 'seo', 'textarea', 'Meta Description', 'Default meta description for pages', TRUE),
('meta_keywords', 'second hand phones, used phones, refurbished phones', 'seo', 'text', 'Meta Keywords', 'Default meta keywords for pages', TRUE),

-- Maintenance Settings
('maintenance_mode', '0', 'maintenance', 'boolean', 'Maintenance Mode', 'Enable/disable maintenance mode', FALSE),
('maintenance_message', 'We are currently performing maintenance. Please check back soon.', 'maintenance', 'textarea', 'Maintenance Message', 'Message to display during maintenance', TRUE);