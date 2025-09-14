<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carpintería Esquivel Oficial</title>
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
            padding: 10px 0;
            position: relative;
        }
        
        .navbar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
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
            height: 50px;
            width: 50px;
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
            font-weight: 400;
        }
        
        .navbar-toggler {
            border: none;
            color: #fffbe9 !important;
            font-size: 1rem;
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
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-brand img {
                height: 48px;
                width: 48px;
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
                <span>Carpintería Esquivel Oficial</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link " href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/productos">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carrito">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""></a>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>