<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// Obtener productos del carrito y calcular el total
$productos = [];
$total = 0;
if (!empty($_SESSION['carrito'])) {
    $ids = implode(",", array_map('intval', array_keys($_SESSION['carrito'])));
    if (!empty($ids)) {
        $sql = "SELECT * FROM productos WHERE id IN ($ids)";
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()) {
            $row['cantidad'] = $_SESSION['carrito'][$row['id']];
            $row['subtotal'] = $row['cantidad'] * $row['precio'];
            $productos[] = $row;
            $total += $row['subtotal'];
        }
    }
}

// Productos de interés (extras no repetidos del carrito)
$extras = [];
if (!empty($_SESSION['carrito'])) {
    $ids = implode(",", array_map('intval', array_keys($_SESSION['carrito'])));
    $sql_extras = "SELECT * FROM productos WHERE id NOT IN ($ids) ORDER BY RAND() LIMIT 4";
} else {
    $sql_extras = "SELECT * FROM productos ORDER BY RAND() LIMIT 4";
}
$extras_result = $conexion->query($sql_extras);
while ($extra = $extras_result->fetch_assoc()) {
    $extras[] = $extra;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Carpintería Artesanal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #a97436;
            --primary-light: #e7cba0;
            --primary-dark: #7a5e3a;
            --primary-gradient: linear-gradient(135deg, #a97436 0%, #c29556 100%);
            --accent-color: #4e8a4a;
            --accent-light: #67b862;
            --success-color: #00c853;
            --error-color: #ff3d00;
            --white: #ffffff;
            --light-bg: #fffbeeea;
            --gray-light: #f5f5f5;
            --gray: #e0e0e0;
            --text-dark: #333333;
            --text-muted: #757575;
            --shadow-sm: 0 2px 10px rgba(169,116,54,0.1);
            --shadow-md: 0 4px 20px rgba(169,116,54,0.15);
            --shadow-lg: 0 8px 30px rgba(169,116,54,0.2);
            --border-radius-sm: 8px;
            --border-radius-md: 16px;
            --border-radius-lg: 24px;
            --font-primary: 'Poppins', sans-serif;
            --font-secondary: 'Merriweather', serif;
            --transition-fast: 0.2s ease;
            --transition-medium: 0.3s ease;
            --transition-slow: 0.5s ease;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font-primary);
            color: var(--text-dark);
            background-color: #fbf7f2;
        }
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }
        .preloader-hidden {
            opacity: 0;
            visibility: hidden;
        }
        .preloader-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--primary-light);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .checkout-main-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #fffbe9 0%, #f8eed9 100%);
            padding: 0 0 60px 0;
            position: relative;
            overflow: hidden;
        }
        .checkout-hero {
            background: var(--primary-gradient);
            color: var(--white);
            text-align: center;
            padding: 60px 20px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .checkout-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/img/wood-pattern.png');
            opacity: 0.1;
            z-index: 1;
        }
        .checkout-hero-title {
            font-family: var(--font-secondary);
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .checkout-hero-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            position: relative;
            z-index: 2;
        }
        .checkout-content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .checkout-container {
            margin-bottom: 40px;
        }
        .checkout-card {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-medium);
            height: 100%;
        }
        .checkout-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .primary-card {
            overflow: hidden;
            position: relative;
        }
        .primary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary-gradient);
        }
        .checkout-header {
            display: flex;
            align-items: center;
            padding: 30px 30px 20px;
            border-bottom: 1px solid var(--gray);
        }
        .checkout-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(169,116,54,0.3);
        }
        .checkout-icon i {
            color: var(--white);
            font-size: 22px;
        }
        .checkout-title {
            margin: 0;
            font-family: var(--font-secondary);
            font-size: 1.8rem;
            color: var(--primary-dark);
            font-weight: 700;
        }
        .checkout-empty-state {
            padding: 60px 30px;
            text-align: center;
        }
        .checkout-empty-icon {
            font-size: 4rem;
            color: var(--primary-light);
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .checkout-empty-title {
            font-family: var(--font-secondary);
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        .checkout-empty-message {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .btn-carpinteria {
            background: var(--primary-gradient);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-md);
            padding: 15px 30px;
            font-weight: 600;
            transition: all var(--transition-fast);
            box-shadow: 0 4px 10px rgba(169,116,54,0.2);
        }
        .btn-carpinteria:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(169,116,54,0.3);
            background: linear-gradient(135deg, #c29556 0%, #a97436 100%);
            color: var(--white);
        }
        .checkout-products-container {
            padding: 30px;
        }
        .checkout-products-header {
            display: flex;
            justify-content: space-between;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--gray);
            font-weight: 600;
            color: var(--text-muted);
        }
        .checkout-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .checkout-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-light);
            transition: background-color var(--transition-fast);
        }
        .checkout-list-item:hover {
            background-color: rgba(169,116,54,0.05);
        }
        .checkout-product-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .checkout-thumb-container {
            width: 70px;
            height: 70px;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            margin-right: 15px;
            box-shadow: var(--shadow-sm);
        }
        .checkout-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-fast);
        }
        .checkout-list-item:hover .checkout-thumb {
            transform: scale(1.05);
        }
        .checkout-product-details {
            flex: 1;
        }
        .checkout-prodname {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-dark);
            margin: 0 0 5px 0;
        }
        .checkout-prod-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .checkout-prodqty, .checkout-unit-price {
            background-color: var(--gray-light);
            padding: 3px 10px;
            border-radius: 30px;
        }
        .checkout-prodprice {
            font-weight: 700;
            color: var(--success-color);
            font-size: 1.2rem;
        }
        .checkout-summary {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--gray);
        }
        .checkout-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 1.05rem;
        }
        .checkout-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-dark);
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px dashed var(--primary-light);
        }
        .checkout-total-price {
            color: var(--success-color);
            font-size: 1.4rem;
        }
        .checkout-payment-methods {
            padding: 30px;
            border-top: 1px solid var(--gray);
        }
        .checkout-section-title {
            font-family: var(--font-secondary);
            color: var(--primary-dark);
            font-size: 1.4rem;
            margin-bottom: 20px;
        }
        .payment-options {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .payment-option {
            flex: 1;
            padding: 15px;
            border: 2px solid var(--gray);
            border-radius: var(--border-radius-md);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .payment-option.active {
            border-color: var(--primary-color);
            background-color: rgba(169,116,54,0.05);
        }
        .payment-option i {
            font-size: 2rem;
            color: var(--primary-color);
        }
        .payment-option span {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .checkout-form {
            margin-bottom: 20px;
        }
        .checkout-paybtn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-light) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-md);
            padding: 18px 25px;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all var(--transition-medium);
            box-shadow: 0 4px 15px rgba(78,138,74,0.3);
            position: relative;
            overflow: hidden;
        }
        .checkout-paybtn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.6s;
        }
        .checkout-paybtn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(78,138,74,0.4);
        }
        .checkout-paybtn:hover::before {
            left: 100%;
        }
        .checkout-btn-price {
            background-color: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 1rem;
        }
        .checkout-guarantee {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .checkout-extras {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .extras-card {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: transform var(--transition-medium);
        }
        .extras-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .extras-header {
            display: flex;
            align-items: center;
            padding: 20px;
            background: var(--primary-gradient);
            color: var(--white);
        }
        .extras-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
        }
        .extras-icon i {
            color: var(--white);
            font-size: 20px;
        }
        .extras-title {
            margin: 0;
            font-family: var(--font-secondary);
            font-size: 1.3rem;
            font-weight: 600;
        }
        .extras-list {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .extras-item {
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-medium);
        }
        .extras-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-md);
        }
        .extras-item-inner {
            display: flex;
            background-color: var(--white);
            border: 1px solid var(--gray-light);
            border-radius: var(--border-radius-md);
            overflow: hidden;
        }
        .extras-img-container {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .extras-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-medium);
        }
        .extras-item:hover .extras-img {
            transform: scale(1.1);
        }
        .extras-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .extras-nombre {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--primary-dark);
            margin: 0 0 5px 0;
            line-height: 1.3;
        }
        .extras-precio {
            font-weight: 700;
            color: var(--success-color);
            font-size: 1.15rem;
            margin-bottom: 10px;
        }
        .btn-add-cart {
            align-self: flex-start;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 8px 15px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all var(--transition-fast);
            box-shadow: 0 2px 8px rgba(169,116,54,0.2);
        }
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(169,116,54,0.3);
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--primary-color) 100%);
            color: var(--white);
        }
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        .btn-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 14px;
            height: 14px;
            margin-top: -7px;
            margin-left: -7px;
            border: 2px solid rgba(255,255,255,0.5);
            border-top-color: var(--white);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .btn-success {
            background: var(--success-color) !important;
            color: var(--white) !important;
        }
        .btn-danger {
            background: var(--error-color) !important;
            color: var(--white) !important;
        }
        .extras-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
        }
        .extras-empty-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--primary-light);
        }
        .checkout-benefits {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            padding: 20px;
            transition: transform var(--transition-medium);
        }
        .checkout-benefits:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .benefit-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-light);
        }
        .benefit-item:last-child {
            border-bottom: none;
        }
        .benefit-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .benefit-icon i {
            color: var(--white);
            font-size: 20px;
        }
        .benefit-content {
            flex: 1;
        }
        .benefit-content h4 {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--primary-dark);
            margin: 0 0 3px 0;
        }
        .benefit-content p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--white);
            border-radius: var(--border-radius-md);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transform: translateX(120%);
            opacity: 0;
            transition: all var(--transition-medium);
            z-index: 9999;
        }
        .toast-active {
            transform: translateX(0);
            opacity: 1;
        }
        .toast-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .toast-icon i {
            color: var(--white);
            font-size: 18px;
        }
        .toast-icon.success {
            background: var(--success-color);
        }
        .toast-icon.error {
            background: var(--error-color);
        }
        .toast-message {
            font-weight: 500;
            font-size: 0.95rem;
        }
        .animate__animated {
            animation-duration: 0.8s;
        }
        .whatsapp-icon {
            font-size: 20px;
            margin-right: 5px;
            color: #25D366;
        }
        .contact-button {
            background-color: #25D366;
            color: white;
            border-radius: 30px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        .contact-button:hover {
            background-color: #1da851;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37,211,102,0.3);
        }
        .menu-icon {
            display: none;
        }
        @media (max-width: 1199px) {
            .checkout-container, .checkout-extras {
                padding: 0;
            }
        }
        @media (max-width: 991px) {
            .checkout-hero {
                padding: 40px 20px;
                margin-bottom: 30px;
            }
            .checkout-hero-title {
                font-size: 2.2rem;
            }
            .checkout-content-wrapper {
                flex-direction: column;
            }
            .checkout-extras {
                max-width: 540px;
                margin: 0 auto;
            }
        }
        @media (max-width: 767px) {
            .checkout-hero {
                padding: 30px 15px;
                margin-bottom: 20px;
            }
            .checkout-hero-title {
                font-size: 1.8rem;
            }
            .checkout-header {
                padding: 20px 20px 15px;
            }
            .checkout-icon {
                width: 40px;
                height: 40px;
            }
            .checkout-title {
                font-size: 1.5rem;
            }
            .checkout-products-container {
                padding: 20px;
            }
            .checkout-thumb-container {
                width: 60px;
                height: 60px;
            }
            .checkout-payment-methods {
                padding: 20px;
            }
            .checkout-section-title {
                font-size: 1.3rem;
            }
            .payment-option {
                padding: 10px;
            }
            .checkout-paybtn {
                padding: 15px 20px;
                font-size: 1.1rem;
            }
            .menu-icon {
                display: block;
            }
        }
        @media (max-width: 575px) {
            .checkout-hero-title {
                font-size: 1.5rem;
            }
            .checkout-hero-subtitle {
                font-size: 1rem;
            }
            .checkout-header {
                padding: 15px 15px 10px;
            }
            .checkout-title {
                font-size: 1.3rem;
            }
            .checkout-products-container {
                padding: 15px;
            }
            .checkout-thumb-container {
                width: 50px;
                height: 50px;
            }
            .checkout-prodname {
                font-size: 1rem;
            }
            .checkout-prod-meta {
                font-size: 0.8rem;
            }
            .checkout-prodprice {
                font-size: 1.1rem;
            }
            .checkout-payment-methods {
                padding: 15px;
            }
            .checkout-section-title {
                font-size: 1.2rem;
            }
            .checkout-paybtn {
                padding: 12px 15px;
                font-size: 1rem;
            }
            .extras-img-container {
                width: 80px;
                height: 80px;
            }
            .extras-content {
                padding: 10px;
            }
            .extras-nombre {
                font-size: 0.95rem;
            }
            .extras-precio {
                font-size: 1.05rem;
            }
            .btn-add-cart {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-spinner"></div>
    </div>

    <main class="checkout-main-wrapper">
        <div class="checkout-hero">
            <h1 class="checkout-hero-title">Finaliza tu Compra</h1>
            <div class="checkout-hero-subtitle">Estás a un paso de disfrutar nuestros productos</div>
        </div>
        
        <div class="checkout-content-wrapper row m-0 justify-content-center">
            <!-- Columna principal -->
            <section class="checkout-container col-12 col-lg-7 col-xl-6">
                <div class="checkout-card primary-card animate__animated animate__fadeInUp">
                    <div class="checkout-header">
                        <div class="checkout-icon">
                            <i class="fa fa-credit-card"></i>
                        </div>
                        <h2 class="checkout-title">Resumen de Compra</h2>
                    </div>
                    
                    <?php if (empty($productos)): ?>
                        <div class="checkout-empty-state">
                            <div class="checkout-empty-icon"><i class="fa fa-shopping-cart"></i></div>
                            <h3 class="checkout-empty-title">¡Tu carrito está vacío!</h3>
                            <p class="checkout-empty-message">Agrega algunos productos para continuar con tu compra</p>
                            <a href="productos.php" class="btn btn-carpinteria btn-lg">
                                <i class="fa fa-cube"></i> Ver productos
                            </a>
                        </div>
                    <?php else: ?>
                    
                    <!-- Resumen de compra -->
                    <div class="checkout-products-container">
                        <div class="checkout-products-header">
                            <span>Producto</span>
                            <span>Precio</span>
                        </div>
                        
                        <ul class="checkout-list">
                            <?php foreach ($productos as $p): ?>
                            <li class="checkout-list-item">
                                <div class="checkout-product-info">
                                    <div class="checkout-thumb-container">
                                        <img src="assets/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="checkout-thumb">
                                    </div>
                                    <div class="checkout-product-details">
                                        <h4 class="checkout-prodname"><?php echo htmlspecialchars($p['nombre']); ?></h4>
                                        <div class="checkout-prod-meta">
                                            <span class="checkout-prodqty">Cantidad: <?php echo $p['cantidad']; ?></span>
                                            <span class="checkout-unit-price">$<?php echo number_format($p['precio'],2); ?> c/u</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="checkout-prodprice">$<?php echo number_format($p['subtotal'],2); ?></div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="checkout-summary">
                            <div class="checkout-summary-row">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total,2); ?></span>
                            </div>
                            <div class="checkout-summary-row">
                                <span>Envío:</span>
                                <span>300$</span>
                            </div>
                            <div class="checkout-summary-row checkout-total">
                                <span>Total:</span>
                                <span class="checkout-total-price">$<?php echo number_format($total,2); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Método de pago -->
                    <div class="checkout-payment-methods">
                        <h3 class="checkout-section-title">Método de pago</h3>
                        <div class="payment-options">
                            <div class="payment-option active">
                                <i class="fab fa-cc-visa"></i>
                                <span>Tarjeta de Crédito/Débito</span>
                                <span>Transferencia Bancaria</span>
                            </div>
                        </div>
                        
                        <form action="procesar_pago.php" method="POST" class="checkout-form" id="paymentForm">
                            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                            <button type="submit" class="btn checkout-paybtn" id="submitPayment">
                                <i class="fa fa-credit-card"></i> Finalizar compra
                                <span class="checkout-btn-price">$<?php echo number_format($total,2); ?></span>
                            </button>
                        </form>
                        
                        <div class="checkout-guarantee">
                            <i class="fa fa-lock"></i> Pago 100% seguro y protegido
                        </div>
                    </div>
                    
                    <div class="checkout-result" id="paymentResult"></div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Columna productos de interés -->
            <aside class="checkout-extras col-12 col-lg-4 offset-xl-1">
                <div class="extras-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <div class="extras-header">
                        <div class="extras-icon"><i class="fa fa-heart"></i></div>
                        <h3 class="extras-title">Productos recomendados</h3>
                    </div>
                    
                    <?php if (!empty($extras)): ?>
                    <div class="extras-list">
                        <?php foreach ($extras as $i => $extra): ?>
                            <div class="extras-item animate__animated animate__fadeInUp" style="animation-delay: <?php echo ($i + 1) * 0.1; ?>s">
                                <div class="extras-item-inner">
                                    <div class="extras-img-container">
                                        <img src="assets/img/<?php echo htmlspecialchars($extra['imagen']); ?>" class="extras-img" alt="<?php echo htmlspecialchars($extra['nombre']); ?>">
                                    </div>
                                    <div class="extras-content">
                                        <h4 class="extras-nombre"><?php echo htmlspecialchars($extra['nombre']); ?></h4>
                                        <div class="extras-precio">$<?php echo number_format($extra['precio'],2); ?></div>
                                        <button type="button" class="btn btn-add-cart" data-id="<?php echo $extra['id']; ?>">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="extras-empty">
                        <div class="extras-empty-icon"><i class="fa fa-box"></i></div>
                        <p>¡No hay recomendaciones disponibles!</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="checkout-benefits animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fa fa-truck"></i></div>
                        <div class="benefit-content">
                            <h4>Envío Gratis</h4>
                            <p>En todos tus pedidos</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fa fa-shield-alt"></i></div>
                        <div class="benefit-content">
                            <h4>Garantía de Calidad</h4>
                            <p>100% productos garantizados</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fa fa-headset"></i></div>
                        <div class="benefit-content">
                            <h4>Soporte 24/7</h4>
                            <p>Estamos para ayudarte</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <a class="nav-link contact-button w-100 text-center" href="https://wa.me/7861009990?text=Necesito%20mas%20informacion%20sobre%20los%20productos" target="_blank">
                            <i class="fab fa-whatsapp whatsapp-icon"></i> Contacto WhatsApp
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
// Reemplaza todo el código JavaScript actual con este:

<script>
    // Preloader
    $(window).on('load', function() {
        setTimeout(function() {
            $('.preloader').addClass('preloader-hidden');
        }, 800);
    });

    // Función para mostrar notificaciones toast
    function showNotification(type, message) {
        // Crear elemento de notificación
        const toast = $('<div class="toast-notification"></div>');
        
        // Icono según tipo de notificación
        let icon = 'check';
        if (type === 'error') {
            icon = 'times';
        }
        
        // Estructura de la notificación
        toast.html(`
            <div class="toast-icon ${type}">
                <i class="fa fa-${icon}"></i>
            </div>
            <div class="toast-message">${message}</div>
        `);
        
        // Agregar al cuerpo del documento
        $('body').append(toast);
        
        // Mostrar con retraso
        setTimeout(function() {
            toast.addClass('toast-active');
        }, 100);
        
        // Ocultar después de 3 segundos
        setTimeout(function() {
            toast.removeClass('toast-active');
            
            // Eliminar después de la animación
            setTimeout(function() {
                toast.remove();
            }, 500);
        }, 3000);
    }

    // Botones agregar al carrito (con delegación de eventos)
    $(document).on('click', '.btn-add-cart', function() {
        const btn = $(this);
        const productId = btn.data('id');
        
        // Prevenir múltiples clics
        if (btn.hasClass('btn-loading') || btn.hasClass('btn-success')) {
            return;
        }
        
        // Efecto de carga
        btn.addClass('btn-loading');
        btn.html('<span class="btn-spinner"></span>');
        
        // Llamada AJAX para agregar al carrito
        $.ajax({
            url: 'carrito_ajax.php',
            type: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: 1
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Cambiar estado del botón
                    btn.removeClass('btn-loading');
                    btn.addClass('btn-success');
                    btn.html('<i class="fa fa-check"></i> Agregado');
                    
                    // Mostrar notificación
                    showNotification('success', response.message || 'Producto agregado al carrito');
                    
                    // Actualizar contador del carrito en el header
                    $('.cart-counter').text(response.cart_count || '0');
                    
                    // Restaurar botón después de 2 segundos
                    setTimeout(function() {
                        btn.removeClass('btn-success');
                        btn.html('<i class="fa fa-plus"></i> Agregar');
                    }, 2000);
                } else {
                    // Mostrar error
                    btn.removeClass('btn-loading');
                    btn.addClass('btn-danger');
                    btn.html('<i class="fa fa-times"></i> Error');
                    showNotification('error', response.message || 'Error al agregar producto');
                    
                    setTimeout(function() {
                        btn.removeClass('btn-danger');
                        btn.html('<i class="fa fa-plus"></i> Agregar');
                    }, 2000);
                }
            },
            error: function(xhr) {
                btn.removeClass('btn-loading');
                btn.addClass('btn-danger');
                btn.html('<i class="fa fa-times"></i> Error');
                showNotification('error', 'Error de conexión. Intente nuevamente.');
                
                setTimeout(function() {
                    btn.removeClass('btn-danger');
                    btn.html('<i class="fa fa-plus"></i> Agregar');
                }, 2000);
                console.error(xhr.responseText);
            }
        });
    });

    // Procesar pago
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submitPayment');
        const form = $(this);
        
        // Mostrar estado de carga
        btn.addClass('btn-loading');
        btn.html('<span class="btn-spinner"></span> Procesando...');
        
        // Enviar datos del formulario
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirigir a página de éxito o mostrar mensaje
                    window.location.href = 'pago_exitoso.php?order_id=' + response.order_id;
                } else {
                    // Mostrar error
                    btn.removeClass('btn-loading');
                    btn.html('<i class="fa fa-credit-card"></i> Finalizar compra');
                    showNotification('error', response.message || 'Error al procesar el pago');
                    
                    // Mostrar detalles del error si existen
                    if (response.errors) {
                        let errorHtml = '<div class="alert alert-danger mt-3">';
                        $.each(response.errors, function(key, value) {
                            errorHtml += `<p>${value}</p>`;
                        });
                        errorHtml += '</div>';
                        $('#paymentResult').html(errorHtml);
                    }
                }
            },
            error: function(xhr) {
                btn.removeClass('btn-loading');
                btn.html('<i class="fa fa-credit-card"></i> Finalizar compra');
                showNotification('error', 'Error en la conexión. Intente nuevamente.');
                console.error(xhr.responseText);
            }
        });
    });
</script>
</body>
</html>