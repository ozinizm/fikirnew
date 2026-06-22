<?php
/**
 * db_migration.php - Create necessary tables for Page Builder
 */
require_once __DIR__ . '/../includes/db.php';

try {
    // 1. Pages Table
    $db->exec("CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        page_type ENUM('service', 'location', 'blog', 'portfolio', 'standard') DEFAULT 'standard',
        h1 VARCHAR(255),
        excerpt TEXT,
        content LONGTEXT,
        seo_title VARCHAR(255),
        meta_description TEXT,
        canonical_url VARCHAR(255),
        meta_robots VARCHAR(50) DEFAULT 'index, follow',
        og_title VARCHAR(255),
        og_description TEXT,
        og_image VARCHAR(255),
        focus_keyword VARCHAR(255),
        related_keywords TEXT,
        cover_image VARCHAR(255),
        mobile_cover_image VARCHAR(255),
        cta_title VARCHAR(255),
        cta_text TEXT,
        primary_button_text VARCHAR(100),
        primary_button_url VARCHAR(255),
        secondary_button_text VARCHAR(100),
        secondary_button_url VARCHAR(255),
        status ENUM('draft', 'published', 'passive') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        published_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 2. Page Blocks Table
    $db->exec("CREATE TABLE IF NOT EXISTS page_blocks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_id INT NOT NULL,
        block_type VARCHAR(50) NOT NULL,
        block_data LONGTEXT,
        sort_order INT DEFAULT 0,
        status TINYINT(1) DEFAULT 1,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 3. Page FAQs Table
    $db->exec("CREATE TABLE IF NOT EXISTS page_faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_id INT NOT NULL,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 4. Page Relations Table (Internal Linking)
    $db->exec("CREATE TABLE IF NOT EXISTS page_relations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_id INT NOT NULL,
        related_id INT NOT NULL,
        relation_type ENUM('service', 'location', 'blog', 'portfolio') NOT NULL,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    echo "Migration completed successfully.";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
