-- NANDSKILLS EduOS — MySQL production initialization
-- This file runs once during container first boot.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Ensure proper charset
ALTER DATABASE nandskills_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create read-only analytics user (optional for reporting tools)
-- CREATE USER IF NOT EXISTS 'nandskills_reader'@'%' IDENTIFIED BY 'readerpassword';
-- GRANT SELECT ON nandskills_db.* TO 'nandskills_reader'@'%';
-- FLUSH PRIVILEGES;
