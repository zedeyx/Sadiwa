-- SADIWA Database Schema
-- Create database
/*CREATE DATABASE IF NOT EXISTS sadiwa_db;
USE sadiwa_db;*/

-- Users table
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Contributor', 'Viewer') NOT NULL DEFAULT 'Viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Risk levels table
CREATE TABLE IF NOT EXISTS RiskLevels (
    risk_id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(50) NOT NULL,
    description TEXT,
    color_code VARCHAR(20)
);

-- Churches table
CREATE TABLE IF NOT EXISTS Churches (
    church_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    municipality VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    year_built INT,
    architectural_style VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Damage reports table
CREATE TABLE IF NOT EXISTS DamageReports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    church_id INT NOT NULL,
    contributor_id INT NOT NULL,
    report_date DATE NOT NULL,
    damage_description TEXT NOT NULL,
    risk_level INT NOT NULL,
    image_path VARCHAR(255),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (church_id) REFERENCES Churches(church_id) ON DELETE CASCADE,
    FOREIGN KEY (contributor_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (risk_level) REFERENCES RiskLevels(risk_id)
);

-- Insert default risk levels
INSERT INTO RiskLevels (level_name, description, color_code) VALUES
('Low Risk', 'Minor damages, no structural concerns', '#28a745'),
('Moderate Risk', 'Visible damages, some structural concerns', '#ffc107'),
('High Risk', 'Significant damages, structural integrity compromised', '#fd7e14'),
('Critical Risk', 'Severe damages, immediate attention required', '#dc3545');

-- Insert sample users (password: 'password123' for all)
INSERT INTO Users (username, password_hash, role) VALUES
('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),
('Contributor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Contributor'),
('Viewer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Viewer');

-- Insert sample churches in Bohol with accurate coordinates
INSERT INTO Churches (name, municipality, latitude, longitude, year_built, architectural_style) VALUES
('Baclayon Church (Immaculate Conception Church)', 'Baclayon', 9.62290000, 123.91250000, 1727, 'Baroque'),
('Loboc Church (San Pedro Apostol Parish)', 'Loboc', 9.63640000, 124.03090000, 1602, 'Spanish Colonial'),
('Maribojoc Church (Santa Cruz Parish)', 'Maribojoc', 9.74060000, 123.84380000, 1768, 'Baroque'),
('Loon Church (Our Lady of Light Parish)', 'Loon', 9.79820000, 123.79330000, 1753, 'Spanish Colonial'),
('Dauis Church (Our Lady of Assumption)', 'Dauis', 9.62585000, 123.86500000, 1697, 'Baroque'),
('Panglao Church (San Agustin Parish)', 'Panglao', 9.58420000, 123.75360000, 1780, 'Spanish Colonial'),
('Tagbilaran Cathedral (St. Joseph the Worker)', 'Tagbilaran', 9.64860000, 123.85390000, 1767, 'Neo-Classical'),
('Dimiao Church (St. Nicholas de Tolentino)', 'Dimiao', 9.60500000, 124.16150000, 1797, 'Baroque'),
('Jagna Church (San Miguel Archangel Parish)', 'Jagna', 9.65190000, 124.36980000, 1870, 'Neo-Gothic'),
('Alburquerque Church (San Jose Parish)', 'Alburquerque', 9.61090000, 123.95750000, 1878, 'Spanish Colonial'),
('Loay Church (Our Lady of Guadalupe)', 'Loay', 9.60080000, 124.01190000, 1822, 'Baroque'),
('Cortes Church (San Roque Parish)', 'Cortes', 9.72230000, 123.90520000, 1855, 'Spanish Colonial');

-- Insert sample damage reports
INSERT INTO DamageReports (church_id, contributor_id, report_date, damage_description, risk_level, status) VALUES
(1, 2, '2023-10-15', 'Cracks observed on the facade and bell tower. Structural damage from 2013 earthquake still visible.', 3, 'Approved'),
(2, 2, '2023-11-20', 'Minor wall cracks detected. Regular monitoring required.', 2, 'Approved'),
(3, 2, '2023-09-10', 'Complete collapse of church structure during 2013 earthquake. Only ruins remain.', 4, 'Approved'),
(4, 2, '2023-12-05', 'Facade showing signs of deterioration. Needs restoration work.', 2, 'Approved'),
(5, 2, '2023-10-25', 'Minor cracks on walls. Overall structure stable.', 1, 'Approved'),
(7, 2, '2024-01-15', 'Bell tower showing cracks. Foundation assessment needed.', 3, 'Pending'),
(9, 2, '2024-02-01', 'Minor weathering on exterior walls. No structural concerns.', 1, 'Approved');