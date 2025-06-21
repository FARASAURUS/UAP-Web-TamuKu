<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Buku Tamu Digital'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-pink: #e91e63;
            --primary-pink-dark: #c2185b;
            --primary-pink-light: #f8bbd9;
            --primary-green: #4caf50;
            --primary-green-dark: #388e3c;
            --primary-green-light: #c8e6c9;
            --gradient-pink: linear-gradient(135deg, #e91e63 0%, #f06292 100%);
            --gradient-green: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            --gradient-mixed: linear-gradient(135deg, #e91e63 0%, #4caf50 100%);
        }

        .sidebar {
            min-height: 100vh;
            background: var(--gradient-mixed);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .card {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .card-header {
            background: var(--gradient-pink);
            color: white;
            border: none;
        }
        
        .btn-primary {
            background: var(--gradient-pink);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--primary-pink-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
        }
        
        .btn-success {
            background: var(--gradient-green);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: var(--primary-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
            border: none;
            color: white;
            border-radius: 20px;
        }
        
        .btn-warning:hover {
            background: #f57c00;
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #f44336 0%, #ef5350 100%);
            border: none;
            border-radius: 20px;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
            border: none;
            border-radius: 20px;
        }
        
        .btn-info:hover {
            background: #1976d2;
        }
        
        .nav-link {
            border-radius: 10px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--gradient-pink);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .table tbody tr:hover {
            background: rgba(233, 30, 99, 0.05);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }
        
        .modal-header {
            background: var(--gradient-mixed);
            color: white;
            border: none;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }
        
        .alert-success {
            background: var(--primary-green-light);
            border-color: var(--primary-green);
            color: var(--primary-green-dark);
            border-radius: 15px;
        }
        
        .alert-danger {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
            border-radius: 15px;
        }
        
        .alert-warning {
            background: #fff8e1;
            border-color: #ff9800;
            color: #ef6c00;
            border-radius: 15px;
        }
        
        .alert-info {
            background: #e3f2fd;
            border-color: #2196f3;
            color: #1565c0;
            border-radius: 15px;
        }
        
        /* Stats Cards */
        .bg-primary {
            background: var(--gradient-pink) !important;
        }
        
        .bg-success {
            background: var(--gradient-green) !important;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%) !important;
        }
        
        .bg-warning {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%) !important;
        }
        
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Hover effects */
        .card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gradient-pink);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-pink-dark);
        }
        
        /* Border radius untuk elemen lain */
        .border-bottom {
            border-bottom: 3px solid var(--primary-pink) !important;
        }
        
        .text-primary {
            color: var(--primary-pink) !important;
        }
        
        .text-success {
            color: var(--primary-green) !important;
        }
        
        /* Login page specific */
        .login-bg-primary {
            background: var(--gradient-mixed) !important;
        }
        
        /* Form kunjungan tamu specific */
        .form-card-header {
            background: var(--gradient-mixed);
        }
        
        .welcome-text {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .card {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
