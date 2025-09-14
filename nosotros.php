<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nosotros - Carpintería Esquivel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            color: #593f29;
            background: #f9f7f3;
        }
        
        /* HEADER MADERA PREMIUM */
        .navbar {
            background: linear-gradient(90deg, #6d5231 0%, #a97436 50%, #76582f 100%);
            box-shadow: 0 4px 15px rgba(89, 63, 41, 0.4);
            padding: 15px 0;
            position: relative;
        }
        
        .navbar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #d5b06b 0%, #eacb8a 50%, #d5b06b 100%);
            opacity: 0.7;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            color: #fffbe9 !important;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .navbar-brand img {
            border: 3px solid #fffbe9;
            background: #fffbe9;
            border-radius: 50%;
            margin-right: 14px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
            height: 54px;
            width: 54px;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }
        
        .navbar-brand span {
            border-left: 2px solid rgba(255, 251, 233, 0.5);
            padding-left: 14px;
            margin-left: 5px;
            position: relative;
        }
        
        .navbar-brand span::after {
            content: "Artesanía en madera";
            display: block;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 400;
            letter-spacing: 1.5px;
            opacity: 0.85;
            margin-top: -2px;
        }
        
        .navbar-nav {
            margin-left: 20px;
        }
        
        .nav-link {
            color: #fffbe9 !important;
            font-weight: 500;
            font-size: 1.05rem;
            padding: 8px 16px !important;
            margin: 0 5px;
            border-radius: 20px;
            transition: all 0.25s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff !important;
            font-weight: 600;
        }
        
        .navbar-toggler {
            border: none;
            color: #fffbe9 !important;
            font-size: 1.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 251, 233, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .contact-button {
            background: #4a3621;
            color: #fffbe9 !important;
            border-radius: 25px;
            padding: 8px 20px !important;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
        }
        
        .contact-button:hover {
            background: #593f29;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .contact-button i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        /* WhatsApp icon specific styling */
        .whatsapp-icon {
            color: #25D366;
            background: #fff;
            padding: 3px;
            border-radius: 50%;
            font-size: 1.1rem;
            margin-right: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .contact-button:hover .whatsapp-icon {
            transform: scale(1.1) rotate(10deg);
        }
        
        /* Decorative wood grain element */
        .wood-grain {
            height: 7px;
            background: linear-gradient(90deg, #4a3621 0%, #7a5e3a 25%, #a97436 50%, #7a5e3a 75%, #4a3621 100%);
            opacity: 0.85;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            position: relative;
        }
        
        .wood-grain::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255,255,255,0.2);
        }
        
        /* Wood texture effect for navbar */
        .navbar::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0,0 L100,100 M20,0 L100,80 M40,0 L100,60 M60,0 L100,40 M80,0 L100,20 M0,20 L80,100 M0,40 L60,100 M0,60 L40,100 M0,80 L20,100" stroke="rgba(255,255,255,0.03)" stroke-width="0.5"/></svg>');
            opacity: 0.6;
            z-index: 0;
            pointer-events: none;
        }
        
        .navbar .container {
            position: relative;
            z-index: 1;
        }
        
        /* ESTILOS ESPECÍFICOS PARA LA PÁGINA NOSOTROS */
        .nosotros-section {
            padding: 80px 0;
            position: relative;
        }
        
        .nosotros-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(89, 63, 41, 0.2) 50%, transparent 100%);
        }
        
        .page-title {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            position: relative;
            display: inline-block;
            margin-bottom: 50px;
            font-weight: 700;
        }
        
        .page-title::after {
            content: "";
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #a97436 0%, #d5b06b 100%);
        }
        
        .nosotros-card {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(89, 63, 41, 0.1);
            margin-bottom: 30px;
            position: relative;
            border: 1px solid rgba(89, 63, 41, 0.08);
            transition: all 0.3s ease;
        }
        
        .nosotros-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(89, 63, 41, 0.15);
        }
        
        .card-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 250px;
        }
        
        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .nosotros-card:hover .card-img-wrapper img {
            transform: scale(1.05);
        }
        
        .nosotros-card-body {
            padding: 30px;
            position: relative;
        }
        
        .nosotros-card-title {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .nosotros-card-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #a97436 0%, #d5b06b 100%);
        }
        
        .nosotros-card-text {
            color: #6d5231;
            line-height: 1.8;
            margin-bottom: 0;
            font-size: 1rem;
        }
        
        .team-member {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(89, 63, 41, 0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(89, 63, 41, 0.08);
        }
        
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(89, 63, 41, 0.15);
        }
        
        .team-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 280px;
        }
        
        .team-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .team-member:hover .team-img-wrapper img {
            transform: scale(1.05);
        }
        
        .team-member-info {
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .team-member-info::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #a97436 0%, #d5b06b 100%);
        }
        
        .team-member-name {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .team-member-position {
            color: #a97436;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .team-member-description {
            color: #6d5231;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        .valores-card {
            background: linear-gradient(145deg, #ffffff 0%, #f9f7f3 100%);
            border-radius: 10px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(89, 63, 41, 0.07);
            transition: all 0.3s ease;
            border: 1px solid rgba(89, 63, 41, 0.06);
            display: flex;
            flex-direction: column;
        }
        
        .valores-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(89, 63, 41, 0.12);
        }
        
        .valores-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(145deg, #a97436 0%, #6d5231 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(89, 63, 41, 0.2);
        }
        
        .valores-icon i {
            color: #fffbe9;
            font-size: 1.8rem;
        }
        
        .valores-title {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .valores-text {
            color: #6d5231;
            line-height: 1.7;
            margin-bottom: 0;
            flex-grow: 1;
        }
        
        .highlight-box {
            background: linear-gradient(145deg, #f9f7f3 0%, #fffbe9 100%);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(89, 63, 41, 0.07);
            border: 1px solid rgba(89, 63, 41, 0.08);
            margin-top: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .highlight-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #a97436 0%, #d5b06b 100%);
        }
        
        .highlight-title {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .highlight-text {
            color: #6d5231;
            line-height: 1.8;
            font-style: italic;
            position: relative;
            padding-left: 25px;
        }
        
        .highlight-text::before {
            content: """;
            position: absolute;
            left: 0;
            top: -10px;
            font-size: 3rem;
            color: #d5b06b;
            font-family: 'Playfair Display', serif;
            line-height: 1;
        }
        
        .history-timeline {
            position: relative;
            margin-top: 50px;
            padding-left: 30px;
        }
        
        .history-timeline::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 15px;
            width: 2px;
            background: linear-gradient(180deg, #a97436 0%, #d5b06b 100%);
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }
        
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        
        .timeline-dot {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            background: #a97436;
            border: 3px solid #f9f7f3;
            border-radius: 50%;
            box-shadow: 0 0 0 2px #d5b06b;
        }
        
        .timeline-date {
            color: #a97436;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        
        .timeline-content {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(89, 63, 41, 0.07);
            border: 1px solid rgba(89, 63, 41, 0.05);
        }
        
        .timeline-title {
            font-family: 'Playfair Display', serif;
            color: #4a3621;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .timeline-text {
            color: #6d5231;
            line-height: 1.7;
            margin-bottom: 0;
        }
        
        .cta-section {
            background: linear-gradient(145deg, #6d5231 0%, #4a3621 100%);
            padding: 60px 0;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d5b06b 0%, #eacb8a 50%, #d5b06b 100%);
            opacity: 0.7;
        }
        
        .cta-section::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0,0 L100,100 M20,0 L100,80 M40,0 L100,60 M60,0 L100,40 M80,0 L100,20 M0,20 L80,100 M0,40 L60,100 M0,60 L40,100 M0,80 L20,100" stroke="rgba(255,255,255,0.07)" stroke-width="0.5"/></svg>');
            opacity: 0.4;
            z-index: 0;
        }
        
        .cta-content {
            position: relative;
            z-index: 1;
        }
        
        .cta-title {
            font-family: 'Playfair Display', serif;
            color: #fffbe9;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .cta-text {
            color: rgba(255, 251, 233, 0.9);
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        
        .cta-button {
            background: #fffbe9;
            color: #4a3621 !important;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .cta-button:hover {
            background: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .cta-button i {
            margin-left: 8px;
        }
        
        .footer {
            background: #4a3621;
            color: #fffbe9;
            padding: 60px 0 30px;
            position: relative;
        }
        
        .footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #a97436 0%, #d5b06b 50%, #a97436 100%);
            opacity: 0.7;
        }
        
        .footer-title {
            font-family: 'Playfair Display', serif;
            color: #d5b06b;
            margin-bottom: 20px;
            font-weight: 600;
            position: relative;
            display: inline-block;
        }
        
        .footer-title::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 2px;
            background: #d5b06b;
        }
        
        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .footer-contact-icon {
            color: #d5b06b;
            font-size: 1.2rem;
            margin-right: 15px;
            margin-top: 3px;
        }
        
        .footer-contact-text {
            color: rgba(255, 251, 233, 0.9);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255, 251, 233, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
            display: block;
            position: relative;
            padding-left: 15px;
        }
        
        .footer-links a::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #d5b06b;
            transition: all 0.2s ease;
        }
        
        .footer-links a:hover {
            color: #d5b06b;
            transform: translateX(3px);
        }
        
        .footer-links a:hover::before {
            transform: translateX(3px);
        }
        
        .footer-social {
            margin-top: 20px;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: rgba(255, 251, 233, 0.1);
            color: #d5b06b;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-icon:hover {
            background: #d5b06b;
            color: #4a3621;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 251, 233, 0.1);
            padding-top: 20px;
            margin-top: 40px;
        }
        
        .footer-bottom-text {
            color: rgba(255, 251, 233, 0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(92, 71, 43, 0.97);
                border-radius: 10px;
                padding: 20px;
                margin-top: 15px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(255,255,255,0.1);
            }
            
            .nav-link {
                margin: 7px 0;
                padding: 10px 16px !important;
            }
            
            .contact-button {
                display: inline-flex;
                margin-top: 10px;
                width: fit-content;
            }
            
            .navbar-brand span::after {
                font-size: 0.7rem;
            }
            
            .nosotros-section {
                padding: 60px 0;
            }
            
            .history-timeline {
                padding-left: 25px;
            }
            
            .timeline-dot {
                left: -25px;
            }
        }
        
        @media (max-width: 767px) {
            .team-img-wrapper {
                height: 220px;
            }
            
            .card-img-wrapper {
                height: 200px;
            }
            
            .nosotros-card-body {
                padding: 20px;
            }
            
            .highlight-box {
                padding: 30px;
            }
            
            .cta-section {
                text-align: center;
            }
            
            .cta-img {
                margin-bottom: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-brand img {
                height: 48px;
                width: 48px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .nosotros-section {
                padding: 40px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative wood grain bar -->
    <div class="wood-grain"></div>
    
    <!-- Enhanced Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/img/logo.jpg" alt="Logo Carpintería Esquivel">
                <span>Carpintería Esquivel</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="nosotros.php">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link contact-button" href="https://wa.me/7861009990?text=Necesito%20mas%20informacion%20sobre%20los%20productos" target="_blank">
                            <i class="fab fa-whatsapp whatsapp-icon"></i> Contacto
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Nosotros Main Content -->
    <section class="nosotros-section">
        <div class="container">
            <h1 class="page-title">Nuestra Historia</h1>
            
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card-img-wrapper mb-4 mb-lg-0">
                        <img src="/api/placeholder/600/400" alt="Taller de carpintería" class="rounded-3 shadow">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="nosotros-card-body ps-lg-4">
                        <h2 class="nosotros-card-title">Tradición y artesanía desde 1985</h2>
                        <p class="nosotros-card-text">Carpintería Esquivel nació como un pequeño taller familiar en el corazón de México. Lo que comenzó como un sueño del maestro carpintero Juan Esquivel, se ha convertido en una empresa reconocida por la calidad y belleza de sus creaciones en madera.</p>
                        <p class="nosotros-card-text mt-3">Durante más de tres décadas, hemos perfeccionado nuestro arte, combinando técnicas tradicionales de carpintería con innovaciones modernas para crear piezas únicas que perduran generaciones.</p>
                        <p class="nosotros-card-text mt-3">Nuestra pasión por la madera y el compromiso con la excelencia artesanal nos han permitido crecer y ganarnos la confianza de clientes que aprecian el valor de los muebles elaborados con dedicación y amor por el oficio.</p>
                    </div>
                </div>
            </div>
            
            <!-- Historia Timeline -->
            <div class="history-timeline mt-5">
                <h2 class="nosotros-card-title mb-4">Nuestra Trayectoria</h2>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">1985</span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">Los inicios</h4>
                        <p class="timeline-text">Juan Esquivel funda un pequeño taller de carpintería con solo dos empleados, enfocándose en restauración de muebles antiguos.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">1992</span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">Expansión y crecimiento</h4>
                        <p class="timeline-text">El taller se expande y comienza a producir muebles artesanales para hogares, incorporando nuevos artesanos al equipo.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">2005</span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">Innovación y diversificación</h4>
                        <p class="timeline-text">Carpintería Esquivel moderniza sus instalaciones e incorpora nuevas técnicas, ampliando su catálogo de productos y servicios.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">2015</span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">Era digital</h4>
                        <p class="timeline-text">Lanzamiento de nuestra tienda en línea, permitiendo llevar nuestras creaciones a todo el país y comenzando exportaciones a mercados internacionales.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">2023</span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">Sostenibilidad y futuro</h4>
                        <p class="timeline-text">Implementación de prácticas sostenibles en toda nuestra cadena productiva y establecimiento de programas de reforestación.</p>
                    </div>
                </div>
            </div>
            <script>
// Bloquear clic derecho y selección de texto
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());

// Bloquear copiar, cortar y pegar
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());

// Bloquear teclas para devtools, código fuente, guardar, imprimir, terminal y zoom
document.addEventListener('keydown', function(e) {
    // F12 (DevTools)
    if (e.keyCode === 123) e.preventDefault();

    // Ctrl+Shift+I/J/C/K/L (DevTools/terminal/inspeccionar)
    if (e.ctrlKey && e.shiftKey && ['I','J','C','K','L','i','j','c','k','l'].includes(e.key)) e.preventDefault();

    // Ctrl+U (Ver código fuente)
    if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) e.preventDefault();

    // Ctrl+S (Guardar)
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) e.preventDefault();

    // Ctrl+P (Imprimir)
    if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) e.preventDefault();

    // Ctrl+(+/-/=) (Zoom in/out/reset)
    if (e.ctrlKey && ['+', '-', '=', '_'].includes(e.key)) e.preventDefault();
});

// Bloquear zoom con scroll del mouse (Ctrl + rueda)
window.addEventListener('wheel', function(e) {
    if (e.ctrlKey) e.preventDefault();
}, { passive: false });

// Prevenir arrastrar elementos (por ejemplo imágenes)
document.addEventListener('dragstart', e => e.preventDefault());
</script>