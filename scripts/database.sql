-- Membuat database
CREATE DATABASE IF NOT EXISTS buku_tamu;
USE buku_tamu;

-- Tabel pengguna/admin
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel departemen
CREATE TABLE IF NOT EXISTS departemen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_departemen VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel petugas
CREATE TABLE IF NOT EXISTS petugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_petugas VARCHAR(100) NOT NULL,
    departemen_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departemen_id) REFERENCES departemen(id)
);

-- Tabel keperluan
CREATE TABLE IF NOT EXISTS keperluan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_keperluan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel tamu
CREATE TABLE IF NOT EXISTS tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tamu VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20),
    alamat TEXT,
    keperluan_id INT,
    petugas_id INT,
    departemen_id INT,
    waktu_kunjungan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (keperluan_id) REFERENCES keperluan(id),
    FOREIGN KEY (petugas_id) REFERENCES petugas(id),
    FOREIGN KEY (departemen_id) REFERENCES departemen(id)
);

-- Insert data default
INSERT INTO users (username, password, nama) VALUES 
('admin', MD5('admin123'), 'Administrator');

INSERT INTO departemen (nama_departemen) VALUES 
('IT Support'),
('Human Resources'),
('Finance'),
('Marketing');

INSERT INTO keperluan (nama_keperluan) VALUES 
('Meeting'),
('Interview'),
('Konsultasi'),
('Pengiriman Dokumen');

INSERT INTO petugas (nama_petugas, departemen_id) VALUES 
('John Doe', 1),
('Jane Smith', 2),
('Bob Johnson', 3),
('Alice Brown', 4);
