-- MySQL Workbench Forward Engineering
SET FOREIGN_KEY_CHECKS = 0;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

DROP SCHEMA IF EXISTS `mydb`;
CREATE SCHEMA IF NOT EXISTS `mydb`;
USE `mydb`;

-- Drop old tables if present (safe)
DROP TABLE IF EXISTS `mydb`.`invitations`;
DROP TABLE IF EXISTS `mydb`.`audit_logs`;
DROP TABLE IF EXISTS `mydb`.`department_asset`;
DROP TABLE IF EXISTS `mydb`.`receipts`;
DROP TABLE IF EXISTS `mydb`.`special_request`;
DROP TABLE IF EXISTS `mydb`.`department_request`;
DROP TABLE IF EXISTS `mydb`.`sector_budgets`;
DROP TABLE IF EXISTS `mydb`.`orders`;
DROP TABLE IF EXISTS `mydb`.`vendors`;
DROP TABLE IF EXISTS `mydb`.`asset_request`;
DROP TABLE IF EXISTS `mydb`.`inventory`;
DROP TABLE IF EXISTS `mydb`.`assets`;
DROP TABLE IF EXISTS `mydb`.`asset_categories`;
DROP TABLE IF EXISTS `mydb`.`company_verification`;
DROP TABLE IF EXISTS `mydb`.`superadmins`;
DROP TABLE IF EXISTS `mydb`.`login_attempts`;
DROP TABLE IF EXISTS `mydb`.`password_reset`;
DROP TABLE IF EXISTS `mydb`.`email_verification`;
DROP TABLE IF EXISTS `mydb`.`users`;
DROP TABLE IF EXISTS `mydb`.`sectors`;
DROP TABLE IF EXISTS `mydb`.`roles`;
DROP TABLE IF EXISTS `mydb`.`companies`;
DROP TABLE IF EXISTS `mydb`.`notifications`;

-- ======================================================
-- 1) COMPANIES (root table) — create WITHOUT creator_id FK
-- ======================================================
CREATE TABLE `mydb`.`companies` (
  `company_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creator_id` BIGINT UNSIGNED NULL, 
  `company_name` VARCHAR(150) NOT NULL,
  `company_email` VARCHAR(255) NOT NULL,
  `company_number` VARCHAR(50) NULL,
  `company_address` VARCHAR(255) NULL,
  `company_desc` TEXT NULL,
  `company_website` VARCHAR(255) NULL,
  `verification_notes` TEXT NULL,
  `verification_status` ENUM('pending','verified','rejected') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `company_email_UNIQUE` (`company_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 2) ROLES (depends on companies)
-- ======================================================
CREATE TABLE `mydb`.`roles` (
  `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `role_name` VARCHAR(100) NOT NULL,
  `category` ENUM('admin','manager','employee') NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  KEY `fk_roles_companies1_idx` (`company_id`),
  CONSTRAINT `fk_roles_companies1`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 3) SECTORS (depends on companies) — manager_id FK will be added later
-- ======================================================
CREATE TABLE `mydb`.`sectors` (
  `sector_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `manager_id` BIGINT UNSIGNED NULL, -- FK added later
  `department_name` VARCHAR(150) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sector_id`),
  KEY `fk_sectors_companies1_idx` (`company_id`),
  CONSTRAINT `fk_sectors_companies1`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 4) USERS (depends on companies, roles, sectors)
-- ======================================================
CREATE TABLE `mydb`.`users` (
  `user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NULL,
  `sector_id` BIGINT UNSIGNED NULL,
  `role_id` BIGINT UNSIGNED NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `firstname` VARCHAR(100) NULL,
  `lastname` VARCHAR(100) NULL,
  `contact` VARCHAR(50) NULL,
  `profile` VARCHAR(255) NULL,
  `verification` ENUM('pending','verified','rejected') DEFAULT 'pending',
  `profile_complete` TINYINT(1) DEFAULT 0,
  `usr_delete` TINYINT(1) DEFAULT 0,
  `remember_token` VARCHAR(100) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_users_companies1_idx` (`company_id`),
  KEY `fk_users_roles1_idx` (`role_id`),
  KEY `fk_users_sectors1_idx` (`sector_id`),
  CONSTRAINT `fk_users_companies1`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_roles1`
    FOREIGN KEY (`role_id`) REFERENCES `mydb`.`roles` (`role_id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_sectors1`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 5) Now add the circular foreign keys that reference users
--    (companies.creator_id and sectors.manager_id)
-- ======================================================
ALTER TABLE `mydb`.`companies`
  ADD CONSTRAINT `fk_companies_users2`
  FOREIGN KEY (`creator_id`)
  REFERENCES `mydb`.`users` (`user_id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `mydb`.`sectors`
  ADD CONSTRAINT `fk_sectors_users2`
  FOREIGN KEY (`manager_id`)
  REFERENCES `mydb`.`users` (`user_id`)
  ON DELETE SET NULL ON UPDATE CASCADE;


-- ======================================================
-- 6) email_verification (depends on users)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`email_verification` (
  `ver_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `ver_code` CHAR(6) NOT NULL,
  `expire_at` DATETIME NOT NULL,
  `verified_at` DATETIME NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ver_id`),
  INDEX `fk_email_verification_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_email_verification_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 7) password_reset (references users.email which is UNIQUE)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`password_reset` (
  `reset_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `res_token` VARCHAR(100) NOT NULL,
  `res_created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reset_id`),
  INDEX `fk_password_reset_users_idx` (`email` ASC),
  CONSTRAINT `fk_password_reset_users`
    FOREIGN KEY (`email`)
    REFERENCES `mydb`.`users` (`email`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 8) login_attempts (independent)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`login_attempts` (
  `log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(80) NOT NULL,
  `remote_ip` VARCHAR(80) NULL,
  `success` TINYINT(1) NULL DEFAULT 0,
  `log_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `log_update` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  INDEX `username_idx` (`username` ASC)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 9) superadmins (independent)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`superadmins` (
  `super_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `super_username` VARCHAR(100) NOT NULL,
  `super_email` VARCHAR(100) NOT NULL,
  `super_password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NULL,
  `last_name` VARCHAR(100) NULL,
  `contact` VARCHAR(50) NULL,
  `profile` VARCHAR(255) NULL,
  `status` ENUM('active', 'inactive') NULL DEFAULT 'active',
  `su_created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `su_update_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`super_id`),
  UNIQUE INDEX `super_username_UNIQUE` (`super_username` ASC),
  UNIQUE INDEX `super_email_UNIQUE` (`super_email` ASC)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 10) company_verification (depends on companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`company_verification` (
  `verification_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `company_token` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `verified_at` DATETIME NULL,
  `status` ENUM('pending', 'verified', 'expired') NULL DEFAULT 'pending',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`verification_id`),
  UNIQUE INDEX `company_token_UNIQUE` (`company_token` ASC),
  INDEX `fk_company_verification_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_company_verification_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 11) asset_categories (depends on companies, users)
--    Note: created_by made NULLABLE because FK uses SET NULL behavior
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`asset_categories` (
  `asset_category_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `category_name` VARCHAR(100) NOT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_category_id`),
  INDEX `fk_asset_categories_users_idx` (`created_by` ASC),
  INDEX `fk_asset_categories_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_asset_categories_users`
    FOREIGN KEY (`created_by`)
    REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_categories_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 12) assets (depends on asset_categories, users, companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`assets` (
  `asset_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `asset_category_id` INT UNSIGNED NULL,
  `asset_description` MEDIUMTEXT NULL,
  `purchase_date` DATE NULL,
  `purchase_cost` DECIMAL(15,2) NULL,
  `location` VARCHAR(120) NULL,
  `asset_status` ENUM('available', 'in_use', 'maintenance', 'disposed') NULL DEFAULT 'available',
  `asset_created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `asset_updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_id`),
  INDEX `fk_assets_asset_categories_idx` (`asset_category_id` ASC),
  INDEX `fk_assets_users_idx` (`user_id` ASC),
  INDEX `fk_assets_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_assets_asset_categories`
    FOREIGN KEY (`asset_category_id`)
    REFERENCES `mydb`.`asset_categories` (`asset_category_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_assets_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_assets_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 13) inventory (depends on asset_categories, companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`inventory` (
  `inventory_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `asset_category_id` INT UNSIGNED NOT NULL,
  `asset_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `quantity` INT NULL DEFAULT 0,
  `unit_cost` DECIMAL(15,2) NULL,
  `reorder_level` INT NULL DEFAULT 0,
  `last_restock` DATETIME NULL,
  `supplier` VARCHAR(100) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_id`),
  INDEX `fk_inventory_asset_categories_idx` (`asset_category_id` ASC),
  INDEX `fk_inventory_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_inventory_asset_categories`
    FOREIGN KEY (`asset_category_id`)
    REFERENCES `mydb`.`asset_categories` (`asset_category_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 14) asset_request (depends on inventory, users, sectors, companies)
--    Note: inventory_id made NULLABLE so ON DELETE SET NULL is valid
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`asset_request` (
  `requests_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `inventory_id` BIGINT UNSIGNED NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `asset_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `purpose` TEXT NOT NULL,
  `request_type` ENUM('common', 'special') NULL DEFAULT 'common',
  `manager_approval` ENUM('pending', 'approved', 'rejected') NULL DEFAULT 'pending',
  `manager_approve` DATETIME NULL,
  `admin_approval` ENUM('pending', 'approved', 'rejected') NULL DEFAULT 'pending',
  `admin_approve` DATETIME NULL,
  `final_status` ENUM('pending', 'approved', 'rejected') NULL DEFAULT 'pending',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`requests_id`),
  INDEX `fk_asset_request_inventory_idx` (`inventory_id` ASC),
  INDEX `fk_asset_request_users_idx` (`user_id` ASC),
  INDEX `fk_asset_request_sectors_idx` (`sector_id` ASC),
  INDEX `fk_asset_request_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_asset_request_inventory`
    FOREIGN KEY (`inventory_id`)
    REFERENCES `mydb`.`inventory` (`inventory_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_request_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_request_sectors`
    FOREIGN KEY (`sector_id`)
    REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_request_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 15) vendors (depends on companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`vendors` (
  `vendor_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `vendor_name` VARCHAR(255) NOT NULL,
  `contact_person` VARCHAR(255) NULL,
  `email` VARCHAR(100) NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `api_source` ENUM('manual', 'amazon', 'other') NULL DEFAULT 'manual',
  `api_vendor_id` VARCHAR(255) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_id`),
  INDEX `fk_vendors_companies_idx` (`company_id` ASC),
  CONSTRAINT `fk_vendors_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 16) orders (depends on vendors, asset_request, users)
--    Note: created_by made NULLABLE to allow ON DELETE SET NULL
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`orders` (
  `orders_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vendor_id` BIGINT UNSIGNED NOT NULL,
  `requests_id` BIGINT UNSIGNED NOT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `quantity` INT NULL DEFAULT 1,
  `unit_cost` DECIMAL(15,2) NULL,
  `total_cost` DECIMAL(15,2) GENERATED ALWAYS AS (quantity * COALESCE(unit_cost,0)) STORED,
  `order_status` ENUM('pending', 'shipped', 'delivered', 'cancelled') NULL DEFAULT 'pending',
  `order_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `expected_delivery` DATETIME NULL,
  `delivered_at` DATETIME NULL,
  PRIMARY KEY (`orders_id`),
  INDEX `fk_orders_asset_request_idx` (`requests_id` ASC),
  INDEX `fk_orders_vendors_idx` (`vendor_id` ASC),
  INDEX `fk_orders_created_by_idx` (`created_by` ASC),
  CONSTRAINT `fk_orders_asset_request`
    FOREIGN KEY (`requests_id`)
    REFERENCES `mydb`.`asset_request` (`requests_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_vendors`
    FOREIGN KEY (`vendor_id`)
    REFERENCES `mydb`.`vendors` (`vendor_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 17) sector_budgets (depends on companies, sectors)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`sector_budgets` (
  `budget_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `total_budget` DECIMAL(15,2) NOT NULL,
  `used_budget` DECIMAL(15,2) NULL DEFAULT 0.00,
  `start_date` DATE NULL,
  `end_date` DATE NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`budget_id`),
  INDEX `fk_sector_budgets_companies_idx` (`company_id` ASC),
  INDEX `fk_sector_budgets_sectors_idx` (`sector_id` ASC),
  CONSTRAINT `fk_sector_budgets_companies`
    FOREIGN KEY (`company_id`)
    REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_sector_budgets_sectors`
    FOREIGN KEY (`sector_id`)
    REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 18) department_request (depends on companies, sectors)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`department_request` (
  `dept_request` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `asset_name` VARCHAR(255) NOT NULL,
  `average_quantity` INT DEFAULT 1,
  `frequency` INT DEFAULT 0,
  `last_requested` DATETIME NULL,
  PRIMARY KEY (`dept_request`),
  INDEX `fk_department_request_sectors_idx` (`sector_id`),
  INDEX `fk_department_request_companies_idx` (`company_id`),
  CONSTRAINT `fk_department_request_sectors`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_request_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 19) special_request (depends on companies, sectors)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`special_request` (
  `special_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `special_asset` VARCHAR(255) NOT NULL,
  `justification` TEXT NOT NULL,
  `admin_approve` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`special_id`),
  INDEX `fk_special_request_sectors_idx` (`sector_id`),
  INDEX `fk_special_request_companies_idx` (`company_id`),
  CONSTRAINT `fk_special_request_sectors`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_special_request_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 20) receipts (depends on asset_request, users, sectors, companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`receipts` (
  `receipt_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `requests_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `asset_name` VARCHAR(255) NOT NULL,
  `quantity` INT DEFAULT 1,
  `total_cost` DECIMAL(15,2) NOT NULL,
  `approved_by` VARCHAR(100) NULL,
  `receipt_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `receipt_image` VARCHAR(255) NULL,
  PRIMARY KEY (`receipt_id`),
  INDEX `fk_receipts_asset_request_idx` (`requests_id`),
  INDEX `fk_receipts_users_idx` (`user_id`),
  INDEX `fk_receipts_sectors_idx` (`sector_id`),
  INDEX `fk_receipts_companies_idx` (`company_id`),
  CONSTRAINT `fk_receipts_asset_request`
    FOREIGN KEY (`requests_id`) REFERENCES `mydb`.`asset_request` (`requests_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipts_users`
    FOREIGN KEY (`user_id`) REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipts_sectors`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipts_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `receipts`
ADD COLUMN `receipt_number` VARCHAR(50) UNIQUE AFTER `receipt_id`,
ADD COLUMN `request_status` ENUM('pending', 'approved', 'rejected', 'fulfilled') DEFAULT 'pending' AFTER `total_cost`,
ADD COLUMN `verification_code` VARCHAR(255) NULL AFTER `approved_by`,
ADD COLUMN `qr_code_path` VARCHAR(255) NULL AFTER `receipt_image`;

-- ======================================================
-- 21) department_asset (depends on assets, inventory, users, sectors, companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`department_asset` (
  `dept_asset_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `asset_id` BIGINT UNSIGNED NOT NULL,
  `inventory_id` BIGINT UNSIGNED NULL,
  `assigned_quantity` INT DEFAULT 1,
  `assigned_by` BIGINT UNSIGNED NOT NULL,
  `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('available', 'in_use', 'maintenance', 'disposed') DEFAULT 'in_use',
  `notes` TEXT NULL,
  PRIMARY KEY (`dept_asset_id`),
  INDEX `fk_department_asset_assets_idx` (`asset_id`),
  INDEX `fk_department_asset_inventory_idx` (`inventory_id`),
  INDEX `fk_department_asset_users_idx` (`assigned_by`),
  INDEX `fk_department_asset_sectors_idx` (`sector_id`),
  INDEX `fk_department_asset_companies_idx` (`company_id`),
  CONSTRAINT `fk_department_asset_assets`
    FOREIGN KEY (`asset_id`) REFERENCES `mydb`.`assets` (`asset_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_asset_inventory`
    FOREIGN KEY (`inventory_id`) REFERENCES `mydb`.`inventory` (`inventory_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_asset_users`
    FOREIGN KEY (`assigned_by`) REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_asset_sectors`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_department_asset_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 22) audit_logs (depends on users, sectors, companies)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`audit_logs` (
  `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sector_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `action_type` ENUM('CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'LOGIN', 'LOGOUT', 'ORDER', 'RECEIVE', 'ASSIGN') NULL,
  `table_name` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  INDEX `fk_audit_logs_users_idx` (`user_id`),
  INDEX `fk_audit_logs_sectors_idx` (`sector_id`),
  INDEX `fk_audit_logs_companies_idx` (`company_id`),
  CONSTRAINT `fk_audit_logs_users`
    FOREIGN KEY (`user_id`) REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_audit_logs_sectors`
    FOREIGN KEY (`sector_id`) REFERENCES `mydb`.`sectors` (`sector_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_audit_logs_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ======================================================
-- 23) invitations (depends on companies, roles, users)
-- ======================================================
CREATE TABLE IF NOT EXISTS `mydb`.`invitations` (
  `invitation_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `inviter_id` BIGINT UNSIGNED NOT NULL,
  `inviter_email` VARCHAR(255) NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
  `approved_by` BIGINT UNSIGNED NULL,
  `invite_token` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`invitation_id`),
  UNIQUE INDEX `invite_token_UNIQUE` (`invite_token`),
  INDEX `fk_invitations_companies_idx` (`company_id`),
  INDEX `fk_invitations_roles_idx` (`role_id`),
  INDEX `fk_invitations_inviter_idx` (`inviter_id`),
  INDEX `fk_invitations_approved_by_idx` (`approved_by`),
  CONSTRAINT `fk_invitations_companies`
    FOREIGN KEY (`company_id`) REFERENCES `mydb`.`companies` (`company_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_invitations_roles`
    FOREIGN KEY (`role_id`) REFERENCES `mydb`.`roles` (`role_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_invitations_inviter`
    FOREIGN KEY (`inviter_id`) REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_invitations_approved_by`
    FOREIGN KEY (`approved_by`) REFERENCES `mydb`.`users` (`user_id`)
    ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE notifications (
    notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,                     
    sender_id BIGINT UNSIGNED NULL,                       
    related_table VARCHAR(100) NULL,                      
    related_id BIGINT UNSIGNED NULL,                      
    message TEXT NOT NULL,                                
    type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_notifications_sender
        FOREIGN KEY (sender_id) REFERENCES users(user_id)
        ON DELETE SET NULL
);

CREATE INDEX idx_notifications_user_read ON notifications (user_id, is_read);
CREATE INDEX idx_notifications_related ON notifications (related_table, related_id);


-- Finalize
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET FOREIGN_KEY_CHECKS = 1;

