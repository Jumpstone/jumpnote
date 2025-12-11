-- Database setup script for JumpNote

-- Create database
CREATE DATABASE IF NOT EXISTS jumpnote;
USE jumpnote;

-- Table for homepage custom links
CREATE TABLE IF NOT EXISTS homepage_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon_url VARCHAR(500),
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for shortlinks/cards page elements
CREATE TABLE IF NOT EXISTS shortlink_elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500),
    icon_url VARCHAR(500),
    description TEXT,
    element_type ENUM('link', 'header', 'folder') NOT NULL,
    parent_id INT DEFAULT NULL,
    section_id INT DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES shortlink_elements(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES shortlink_elements(id) ON DELETE SET NULL
);

-- Indexes for better performance
CREATE INDEX idx_homepage_links_sort ON homepage_links(sort_order);
CREATE INDEX idx_shortlink_elements_type ON shortlink_elements(element_type);
CREATE INDEX idx_shortlink_elements_parent ON shortlink_elements(parent_id);
CREATE INDEX idx_shortlink_elements_section ON shortlink_elements(section_id);
CREATE INDEX idx_shortlink_elements_sort ON shortlink_elements(sort_order);