<?php
// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'buku_tamu';

// Membuat koneksi ke database
try {
    $conn = new mysqli($host, $username, $password, $database);
    
    // Cek koneksi
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Fungsi untuk escape string
function escape_string($string) {
    global $conn;
    return $conn->real_escape_string(trim($string));
}

// Fungsi untuk prepared statement
function execute_query($query, $params = [], $types = '') {
    global $conn;
    
    if (empty($params)) {
        return $conn->query($query);
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $result = $stmt->execute();
    
    if (strpos($query, 'SELECT') === 0) {
        return $stmt->get_result();
    }
    
    return $result;
}
?>
