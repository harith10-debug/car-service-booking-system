-- Car Service Booking Management System SQL reference schema
-- Laravel migrations are the source of truth. This file is provided for IMS560 documentation.

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  phone VARCHAR(30) NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_users_role (role),
  INDEX idx_users_name (name)
);

CREATE TABLE vehicles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  plate_number VARCHAR(20) NOT NULL,
  brand VARCHAR(100) NOT NULL,
  model VARCHAR(100) NOT NULL,
  year SMALLINT UNSIGNED NOT NULL,
  color VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_user_plate (user_id, plate_number),
  INDEX idx_plate_number (plate_number),
  INDEX idx_brand_model (brand, model),
  CONSTRAINT fk_vehicles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE service_packages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  package_name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  estimated_duration INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_package_name (package_name),
  INDEX idx_package_status (status)
);

CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  vehicle_id BIGINT UNSIGNED NOT NULL,
  service_package_id BIGINT UNSIGNED NOT NULL,
  preferred_date DATE NOT NULL,
  preferred_time TIME NOT NULL,
  additional_notes TEXT NULL,
  status ENUM('Pending','Approved','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  total_price DECIMAL(10,2) NOT NULL,
  admin_remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_booking_status (status),
  INDEX idx_booking_date (preferred_date),
  INDEX idx_booking_date_time (preferred_date, preferred_time),
  INDEX idx_user_status (user_id, status),
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bookings_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bookings_package FOREIGN KEY (service_package_id) REFERENCES service_packages(id) ON DELETE RESTRICT
);

CREATE TABLE booking_status_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  changed_by BIGINT UNSIGNED NULL,
  from_status VARCHAR(30) NULL,
  to_status VARCHAR(30) NOT NULL,
  remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_log_booking_created (booking_id, created_at),
  INDEX idx_log_status (to_status),
  CONSTRAINT fk_logs_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_logs_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
