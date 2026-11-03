<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurantName }} - Fresh & Crispy Golden Fried Chicken</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Bebas+Neue&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --brand-primary: #f59e0b; /* Golden Amber */
            --brand-primary-hover: #d97706;
            --brand-crimson: #e11d48; /* Fiery Crimson */
            --brand-crimson-hover: #be123c;
            --brand-dark: #0b0d11;
            --brand-card: #14171f;
            --brand-card-hover: #1c212c;
            --brand-border: #262c3b;
            --brand-text: #f8fafc;
            --brand-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--brand-dark);
            color: var(--brand-text);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, .font-display {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        .font-impact {
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 0.04em;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0d11;
        }
        ::-webkit-scrollbar-thumb {
            background: #2a3142;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(90deg, #b91c1c 0%, #d97706 100%);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 7px 0;
            letter-spacing: 0.02em;
        }

        /* Header Navbar */
        .header-navbar {
            background: rgba(14, 17, 24, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            position: sticky;
            top: 0;
            z-index: 1040;
            transition: all 0.3s ease;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-crimson));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #ffffff;
            box-shadow: 0 4px 18px rgba(245, 158, 11, 0.35);
        }

        .brand-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.85rem;
            color: #ffffff;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .brand-name span {
            color: var(--brand-primary);
        }

        .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 3px;
            color: var(--brand-crimson);
            text-transform: uppercase;
        }

        .nav-link-custom {
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        /* Cart Trigger Button */
        .btn-cart-nav {
            background: linear-gradient(135deg, #f59e0b, #e11d48);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.92rem;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.35);
            transition: all 0.25s ease;
        }

        .btn-cart-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
            color: #ffffff;
        }

        .cart-badge {
            background: #ffffff;
            color: #b91c1c;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .btn-staff-login {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #cbd5e1;
            font-weight: 600;
            padding: 7px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-staff-login:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            padding: 90px 0 100px 0;
            background: radial-gradient(circle at 75% 30%, rgba(225, 29, 72, 0.16) 0%, rgba(245, 158, 11, 0.08) 35%, rgba(11, 13, 17, 1) 70%);
            overflow: hidden;
            border-bottom: 1px solid var(--brand-border);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 50px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--brand-primary);
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 22px;
        }

        .hero-title {
            font-size: clamp(2.8rem, 5.5vw, 4.8rem);
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 24px;
            color: #ffffff;
        }

        .hero-title .highlight-gold {
            background: linear-gradient(135deg, #fcd34d, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-title .highlight-red {
            background: linear-gradient(135deg, #fb7185, #e11d48);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-lead {
            font-size: 1.15rem;
            color: #cbd5e1;
            line-height: 1.7;
            max-width: 540px;
            margin-bottom: 35px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 45px;
        }

        .btn-munch-primary {
            background: linear-gradient(135deg, #f59e0b, #e11d48);
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px 34px;
            border-radius: 50px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(225, 29, 72, 0.4);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-munch-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.55);
            color: #ffffff;
        }

        .btn-munch-outline {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 13px 30px;
            border-radius: 50px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-munch-outline:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: #ffffff;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-stat-item h4 {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--brand-primary);
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }

        .hero-stat-item p {
            font-size: 0.85rem;
            color: var(--brand-muted);
            margin: 0;
        }

        /* Hero Image Container */
        .hero-image-wrap {
            position: relative;
            text-align: center;
        }

        .hero-glow-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            height: 440px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.3) 0%, rgba(225, 29, 72, 0.15) 50%, transparent 70%);
            filter: blur(40px);
            border-radius: 50%;
            z-index: 1;
        }

        .hero-main-img {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 520px;
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 40px rgba(245, 158, 11, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.12);
            animation: heroFloat 5s ease-in-out infinite alternate;
        }

        @keyframes heroFloat {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-12px) rotate(1deg); }
        }

        .floating-deal-badge {
            position: absolute;
            bottom: 25px;
            left: 10px;
            z-index: 3;
            background: rgba(20, 23, 31, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(245, 158, 11, 0.4);
            padding: 12px 18px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: pulseGlow 3s infinite alternate;
        }

        @keyframes pulseGlow {
            0% { box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
            100% { box-shadow: 0 10px 35px rgba(245, 158, 11, 0.35); }
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 50px auto;
        }

        .section-tag {
            color: var(--brand-crimson);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .section-subtitle {
            color: var(--brand-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Category Filter Pills */
        .category-nav-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 35px;
        }

        .cat-pill {
            background: var(--brand-card);
            color: #cbd5e1;
            border: 1px solid var(--brand-border);
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cat-pill:hover, .cat-pill.active {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-crimson));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.3);
            transform: translateY(-2px);
        }

        /* Search Bar */
        .menu-search-wrap {
            max-width: 480px;
            margin: 0 auto 40px auto;
            position: relative;
        }

        .menu-search-input {
            width: 100%;
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            color: #ffffff;
            border-radius: 50px;
            padding: 14px 24px 14px 48px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .menu-search-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.25);
            background: #181d27;
        }

        .menu-search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-muted);
            font-size: 1.15rem;
        }

        /* Product Cards */
        .product-card {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-7px);
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45), 0 0 25px rgba(245, 158, 11, 0.15);
            background: var(--brand-card-hover);
        }

        .product-img-wrap {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: #1c212c;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        .product-promo-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: linear-gradient(135deg, var(--brand-crimson), #b91c1c);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            z-index: 2;
        }

        .product-category-badge {
            position: absolute;
            bottom: 12px;
            right: 14px;
            background: rgba(14, 17, 24, 0.85);
            backdrop-filter: blur(8px);
            color: var(--brand-primary);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid rgba(245, 158, 11, 0.3);
            z-index: 2;
        }

        .product-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-title {
            font-size: 1.18rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            line-height: 1.35;
        }

        .product-desc {
            font-size: 0.85rem;
            color: var(--brand-muted);
            line-height: 1.5;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .product-price {
            display: flex;
            flex-direction: column;
        }

        .price-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--brand-muted);
            font-weight: 600;
        }

        .price-val {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--brand-primary);
            font-family: 'Outfit', sans-serif;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-crimson));
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-add-cart:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.4);
            color: #ffffff;
        }

        /* Value Deals Section */
        .deals-section {
            padding: 80px 0;
            background: linear-gradient(180deg, var(--brand-dark) 0%, #11141c 100%);
            border-top: 1px solid var(--brand-border);
            border-bottom: 1px solid var(--brand-border);
        }

        .deal-banner-card {
            background: linear-gradient(135deg, #1c212d, #251821);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 24px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        /* Features / Why Us */
        .features-section {
            padding: 85px 0;
            background: var(--brand-dark);
        }

        .feature-box {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            border-radius: 20px;
            padding: 30px 24px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-6px);
            border-color: var(--brand-primary);
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        }

        .feature-icon-wrap {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--brand-primary);
            margin: 0 auto 20px auto;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .feature-desc {
            color: var(--brand-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Reviews Section */
        .reviews-section {
            padding: 80px 0;
            background: #0e1117;
            border-top: 1px solid var(--brand-border);
        }

        .review-card {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            border-radius: 20px;
            padding: 26px;
            height: 100%;
        }

        .stars-wrap {
            color: #f59e0b;
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .review-text {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.65;
            font-style: italic;
            margin-bottom: 20px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reviewer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-crimson));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #ffffff;
            font-size: 1rem;
        }

        .reviewer-name {
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            font-size: 0.95rem;
        }

        .reviewer-role {
            color: var(--brand-muted);
            font-size: 0.78rem;
            margin: 0;
        }

        /* Cart Offcanvas Drawer */
        .cart-offcanvas {
            background: #12151e;
            color: #ffffff;
            border-left: 1px solid var(--brand-border);
            width: 440px !important;
            max-width: 95vw;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .cart-item-thumb {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cart-qty-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #ffffff;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cart-qty-btn:hover {
            background: var(--brand-primary);
            color: #000;
        }

        .form-control-munch {
            background: #1c212c;
            border: 1px solid var(--brand-border);
            color: #ffffff;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
        }

        .form-control-munch:focus {
            background: #222836;
            border-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.2);
        }

        /* Floating WhatsApp Button */
        .floating-wa-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #25d366;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.45);
            z-index: 1030;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: waPulse 2s infinite;
        }

        .floating-wa-btn:hover {
            transform: scale(1.1);
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.6);
        }

        @keyframes waPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        /* Footer */
        .footer-main {
            background: #08090d;
            border-top: 1px solid var(--brand-border);
            padding: 70px 0 30px 0;
            color: var(--brand-muted);
        }

        .footer-brand-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.2rem;
            color: #ffffff;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
        }

        .footer-brand-title span {
            color: var(--brand-primary);
        }

        .footer-link {
            color: var(--brand-muted);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
            margin-bottom: 8px;
            font-size: 0.92rem;
        }

        .footer-link:hover {
            color: var(--brand-primary);
            transform: translateX(4px);
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-right: 8px;
        }

        .social-btn:hover {
            background: var(--brand-primary);
            color: #000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="bi bi-fire me-1 text-warning"></i>
                <strong>FREE DELIVERY</strong> on all orders above <strong>Rs. 1,500</strong>!
            </div>
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-telephone-fill me-1"></i> Hotline: <a href="tel:{{ $phone }}" class="text-white text-decoration-none fw-bold">{{ $phone }}</a></span>
                <span class="d-none d-md-inline">|</span>
                <span class="d-none d-md-inline"><i class="bi bi-clock-fill me-1"></i> {{ $openingHours }}</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <nav class="header-navbar py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Brand Logo -->
            <a href="{{ route('website') }}" class="brand-logo">
                <div class="brand-logo-icon">🍗</div>
                <div class="brand-logo-text">
                    <span class="brand-name">MUNCH <span>FRIED CHICKEN</span></span>
                    <span class="brand-subtitle">Crispy &bull; Juicy &bull; Halal</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="d-none d-lg-flex align-items-center gap-1">
                <a href="#hero" class="nav-link-custom active">Home</a>
                <a href="#menu" class="nav-link-custom">Our Menu</a>
                <a href="#deals" class="nav-link-custom">Hot Deals</a>
                <a href="#why-us" class="nav-link-custom">Why Munch</a>
                <a href="#reviews" class="nav-link-custom">Reviews</a>

            </div>

            <!-- Action Buttons: Cart & Staff Software Login -->
            <div class="d-flex align-items-center gap-2">
                

                <!-- Cart Button -->
                <button class="btn-cart-nav" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
                    <i class="bi bi-bag-check-fill fs-5"></i>
                    <span class="d-none d-sm-inline">Cart</span>
                    <span class="cart-badge" id="cartNavCount">0</span>
                    <span class="d-none d-md-inline text-warning fw-bold" id="cartNavTotal">Rs. 0</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i> Handcrafted &bull; Secret 12-Spice Blend
                    </div>
                    <h1 class="hero-title">
                        CRUNCHY.<br>
                        <span class="highlight-gold">JUICY.</span><br>
                        <span class="highlight-red">UNFORGETTABLE.</span>
                    </h1>
                    <p class="hero-lead">
                        Indulge in 100% fresh, farm-raised Halal chicken hand-breaded in our proprietary golden recipe. Fried to sizzling crunch outside, bursting with tender flavors inside.
                    </p>
                    <div class="hero-cta-group">
                        <a href="#menu" class="btn-munch-primary">
                            <i class="bi bi-lightning-charge-fill"></i> Order Online Now
                        </a>
                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello Munch Fried Chicken! I want to inquire about menu and delivery.') }}" target="_blank" class="btn-munch-outline">
                            <i class="bi bi-whatsapp text-success"></i> WhatsApp Order
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-item">
                            <h4>4.9 ★</h4>
                            <p>2,800+ Foodies</p>
                        </div>
                        <div class="hero-stat-item">
                            <h4>30 Min</h4>
                            <p>Fast Hot Delivery</p>
                        </div>
                        <div class="hero-stat-item">
                            <h4>100%</h4>
                            <p>Halal & Fresh Daily</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image-wrap">
                        <div class="hero-glow-circle"></div>
                        <img src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85" alt="Munch Fried Chicken Signature Bucket" class="hero-main-img img-fluid">
                        <div class="floating-deal-badge">
                            <span class="fs-1">🔥</span>
                            <div class="text-start">
                                <span class="badge bg-danger text-uppercase fw-bold mb-1">Mega Deal</span>
                                <div class="fw-bold text-white fs-6">Family Bucket Feast</div>
                                <div class="text-warning fw-bold">Starting from Rs. 1,050</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Value Deals Section -->
    @if(isset($featuredDeals) && count($featuredDeals) > 0)
    <section id="deals" class="deals-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Chef's Signature Specials</span>
                <h2 class="section-title">Today's Hot Deals & Combos</h2>
                <p class="section-subtitle">Specially curated value buckets and mouthwatering combos designed for maximum crunch and big savings.</p>
            </div>

            <div class="row g-4">
                @foreach($featuredDeals as $deal)
                <div class="col-md-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="{{ $deal['image'] }}" alt="{{ $deal['name'] }}" class="product-img" loading="lazy">
                            @if(!empty($deal['promo_tag']))
                                <span class="product-promo-badge">{{ $deal['promo_tag'] }}</span>
                            @endif
                            <span class="product-category-badge">{{ $deal['category_name'] }}</span>
                        </div>
                        <div class="product-content">
                            <h3 class="product-title">{{ $deal['name'] }}</h3>
                            <p class="product-desc">{{ $deal['description'] }}</p>
                            <div class="product-footer">
                                <div class="product-price">
                                    <span class="price-label">Price</span>
                                    <span class="price-val">{{ $deal['formatted_price'] }}</span>
                                </div>
                                <button class="btn-add-cart" onclick="addToCart({{ $deal['id'] }}, '{{ addslashes($deal['name']) }}', {{ $deal['price'] }}, '{{ $deal['image'] }}')">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Interactive Live Menu Section -->
    <section id="menu" class="py-5" style="background: var(--brand-dark);">
        <div class="container py-4">
            <div class="section-header">
                <span class="section-tag">Fresh From The Kitchen</span>
                <h2 class="section-title">Explore Our Full Menu</h2>
                <p class="section-subtitle">Every piece is fresh, marinated with authentic seasonings, and fried to golden perfection on order.</p>
            </div>

            <!-- Search Bar -->
            <div class="menu-search-wrap">
                <i class="bi bi-search menu-search-icon"></i>
                <input type="text" id="menuSearchInput" class="menu-search-input" placeholder="Search crispy chicken, burgers, wings, fries..." onkeyup="filterMenuCards()">
            </div>

            <!-- Category Filter Pills -->
            <div class="category-nav-wrap">
                <button class="cat-pill active" onclick="filterCategory('all', this)">
                    <i class="bi bi-grid-fill"></i> All Menu
                </button>
                @foreach($categories as $category)
                <button class="cat-pill" onclick="filterCategory({{ $category->id }}, this)">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="row g-4" id="productsGrid">
                @forelse($products as $product)
                <div class="col-md-6 col-lg-4 product-item-col" data-category="{{ $product['category_id'] }}" data-name="{{ strtolower($product['name']) }}" data-desc="{{ strtolower($product['description']) }}">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-img" loading="lazy">
                            @if(!empty($product['promo_tag']))
                                <span class="product-promo-badge">{{ $product['promo_tag'] }}</span>
                            @endif
                            <span class="product-category-badge">{{ $product['category_name'] }}</span>
                        </div>
                        <div class="product-content">
                            <h3 class="product-title">{{ $product['name'] }}</h3>
                            <p class="product-desc">{{ $product['description'] }}</p>
                            <div class="product-footer">
                                <div class="product-price">
                                    <span class="price-label">Price</span>
                                    <span class="price-val">{{ $product['formatted_price'] }}</span>
                                </div>
                                <button class="btn-add-cart" onclick="addToCart({{ $product['id'] }}, '{{ addslashes($product['name']) }}', {{ $product['price'] }}, '{{ $product['image'] }}')">
                                    <i class="bi bi-plus-lg"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="fs-1 text-warning mb-3">🍗</div>
                    <h3 class="text-white">Menu is being prepared!</h3>
                    <p class="text-muted">Products added in the restaurant software will appear here automatically.</p>
                </div>
                @endforelse
            </div>

            <!-- No Results Message (hidden by default) -->
            <div id="noResultsMsg" class="text-center py-5 d-none">
                <i class="bi bi-search fs-1 text-warning mb-3 d-block"></i>
                <h4 class="text-white">No delicious items match your search</h4>
                <p class="text-muted">Try typing a different item name like "Burger", "Chicken", or "Wings".</p>
                <button class="btn btn-outline-warning rounded-pill px-4 mt-2" onclick="resetSearch()">View All Menu</button>
            </div>
        </div>
    </section>

    <!-- Why Munch Fried Chicken Section -->
    <section id="why-us" class="features-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Quality In Every Bite</span>
                <h2 class="section-title">Why Foodies Love Munch</h2>
                <p class="section-subtitle">We never compromise on freshness, hygiene, or authentic crunch. Here is what makes us extraordinary.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrap">🍗</div>
                        <h4 class="feature-title">100% Fresh Halal</h4>
                        <p class="feature-desc">Never frozen chicken sourced daily from certified farms. Tender, juicy, and naturally delicious.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrap">🌶️</div>
                        <h4 class="feature-title">Secret 12-Spice Blend</h4>
                        <p class="feature-desc">A proprietary master mix of aromatic spices marinated for 12 hours for deep, explosive flavor.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrap">⚡</div>
                        <h4 class="feature-title">Super Fast Delivery</h4>
                        <p class="feature-desc">Piping hot food delivered straight to your doorstep in thermal heat-sealed boxes within 30-40 mins.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrap">🧼</div>
                        <h4 class="feature-title">Ultra Clean Kitchens</h4>
                        <p class="feature-desc">Prepared under stringent food safety and hygienic protocols in modern commercial deep-fryers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews / Testimonials -->
    <section id="reviews" class="reviews-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Real Foodie Experiences</span>
                <h2 class="section-title">Loved by Thousands</h2>
                <p class="section-subtitle">Discover what our happy diners and online orderers say about Munch Fried Chicken.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="stars-wrap">★★★★★</div>
                        <p class="review-text">"Hands down the crispiest fried chicken I've tasted in town! The seasoning hits every note and the Double Crunch Tower Burger is colossal. Fast delivery too!"</p>
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">AZ</div>
                            <div>
                                <h5 class="reviewer-name">Ali Zafar</h5>
                                <p class="reviewer-role">Verified Foodie &bull; 15+ Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="stars-wrap">★★★★★</div>
                        <p class="review-text">"The Family Feast bucket was piping hot when it arrived! The pieces are generous, skin is insanely crunchy without feeling oily. Our kids loved the wings!"</p>
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">SN</div>
                            <div>
                                <h5 class="reviewer-name">Sana Naveed</h5>
                                <p class="reviewer-role">Regular Family Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="review-card">
                        <div class="stars-wrap">★★★★★</div>
                        <p class="review-text">"Ordered online and received instant WhatsApp updates. The loaded fries with melted cheese and chicken bites are addictive. 10/10 highly recommended!"</p>
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">HK</div>
                            <div>
                                <h5 class="reviewer-name">Hamza Khan</h5>
                                <p class="reviewer-role">Food Blogger</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Slide-over Cart Offcanvas Drawer -->
    <div class="offcanvas offcanvas-end cart-offcanvas" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25 pb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bag-heart-fill fs-4 text-warning"></i>
                <h5 class="offcanvas-title fw-bold text-white mb-0" id="cartOffcanvasLabel">Your Munch Basket</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-direction-column flex-column justify-content-between p-3">
            <!-- Free Delivery Progress -->
            <div>
                <div class="p-2 mb-3 rounded-3" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25);">
                    <div class="d-flex justify-content-between text-warning small fw-bold mb-1">
                        <span id="freeDeliveryLabel">Add Rs. 1,500 for FREE Delivery</span>
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="progress" style="height: 6px; background: rgba(255, 255, 255, 0.1);">
                        <div id="freeDeliveryBar" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Cart Items Container -->
                <div id="cartItemsList" style="max-height: 280px; overflow-y: auto;">
                    <!-- Rendered via JS -->
                </div>
            </div>

            <!-- Checkout Details Form -->
            <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
                <div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2 rounded-3" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                    <span class="small fw-bold text-white"><i class="bi bi-bicycle text-warning me-2"></i>Hot Food Delivery</span>
                    <span class="badge bg-warning text-dark fw-bold">Doorstep</span>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="text" id="custName" class="form-control-munch w-100" placeholder="Your Full Name *" required>
                    </div>
                    <div class="col-6">
                        <input type="tel" id="custPhone" class="form-control-munch w-100" placeholder="Phone (03xx-xxxxxxx) *" required>
                    </div>
                </div>

                <div id="deliveryAddressWrap" class="mb-2">
                    <input type="text" id="custAddress" class="form-control-munch w-100 mb-2" placeholder="Full Delivery Address / Street / House #">
                </div>

                <input type="text" id="custNotes" class="form-control-munch w-100 mb-3" placeholder="Special cooking/delivery instructions (optional)">

                <!-- Order Totals -->
                <div class="p-3 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid var(--brand-border);">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Items Subtotal:</span>
                        <strong class="text-white" id="cartSubtotalText">Rs. 0</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-2">
                        <span>Delivery Fee:</span>
                        <strong class="text-white" id="cartDeliveryFeeText">Rs. 0</strong>
                    </div>
                    <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-2">
                        <span class="fw-bold text-white">Grand Total:</span>
                        <strong class="text-warning fs-5 fw-bold" id="cartGrandTotalText">Rs. 0</strong>
                    </div>
                </div>

                <button class="btn btn-munch-primary w-100 py-3 fw-bold fs-6 justify-content-center" id="btnPlaceOrder" onclick="submitOrder()">
                    <i class="bi bi-check2-circle fs-5"></i> Confirm & Place Order
                </button>
            </div>
        </div>
    </div>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello Munch Fried Chicken! I would like to place an order.') }}" target="_blank" class="floating-wa-btn" title="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Footer -->
    <footer class="footer-main">
        <div class="container">
            <div class="row gy-4 pb-5 border-bottom border-secondary border-opacity-25">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand-title">MUNCH <span>FRIED CHICKEN</span></div>
                    <p class="small text-muted mb-4" style="line-height: 1.7;">
                        Premium handcrafted crispy fried chicken seasoned with our authentic secret spice blend. Fast delivery, unbeatable crunch, and 100% Halal goodness.
                    </p>
                    <div class="d-flex">
                        <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-tiktok"></i></a>
                        <a href="https://wa.me/{{ $whatsappNumber }}" class="social-btn"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="text-white fw-bold mb-3 text-uppercase small">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#hero" class="footer-link">Home</a></li>
                        <li><a href="#menu" class="footer-link">Full Menu</a></li>
                        <li><a href="#deals" class="footer-link">Today's Deals</a></li>
                        <li><a href="#why-us" class="footer-link">Our Quality</a></li>

                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="text-white fw-bold mb-3 text-uppercase small">Restaurant Hours</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><strong class="text-white">Monday - Thursday:</strong><br>12:00 PM – 02:00 AM</li>
                        <li class="mb-2"><strong class="text-white">Friday - Sunday:</strong><br>12:00 PM – 03:00 AM</li>
                        <li class="text-warning fw-bold"><i class="bi bi-check-circle-fill"></i> Delivery Active Now</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3 text-uppercase small">Find & Contact Us</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> {{ $address }}</li>
                        <li class="mb-2"><i class="bi bi-telephone-fill text-warning me-2"></i> <a href="tel:{{ $phone }}" class="text-muted text-decoration-none">{{ $phone }}</a></li>
                        <li class="mb-3"><i class="bi bi-whatsapp text-success me-2"></i> <a href="https://wa.me/{{ $whatsappNumber }}" class="text-muted text-decoration-none">WhatsApp Order</a></li>
                        
                    </ul>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap pt-4 small">
                <div>&copy; {{ date('Y') }} {{ $restaurantName }}. All rights reserved.</div>
                <div>Fresh &bull; Crispy &bull; 100% Halal</div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="{{ asset('assets/vendors/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom Restaurant Cart & Dynamic Script -->
    <script>
        // --- Cart State Management ---
        const CART_STORAGE_KEY = 'munch_fried_chicken_cart';
        let cart = [];

        try {
            const saved = localStorage.getItem(CART_STORAGE_KEY);
            if (saved) {
                cart = JSON.parse(saved);
            }
        } catch (e) {
            cart = [];
        }

        function saveCart() {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
            renderCartUI();
        }

        function addToCart(id, name, price, image) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: parseFloat(price) || 0,
                    image: image,
                    quantity: 1
                });
            }
            saveCart();

            // SweetAlert Toast Notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: false,
                background: '#1c212c',
                color: '#ffffff'
            });
            Toast.fire({
                icon: 'success',
                title: `Added "${name}" to basket! 🍗`
            });
        }

        function updateCartQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                saveCart();
            }
        }

        function removeCartItem(id) {
            cart = cart.filter(i => i.id !== id);
            saveCart();
        }

        function renderCartUI() {
            const totalCount = cart.reduce((sum, i) => sum + i.quantity, 0);
            const subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);

            // Update Nav Badges
            document.getElementById('cartNavCount').innerText = totalCount;
            document.getElementById('cartNavTotal').innerText = `Rs. ${subtotal.toLocaleString()}`;

            // Free Delivery threshold = Rs. 1500
            const freeThreshold = 1500;
            const freeBar = document.getElementById('freeDeliveryBar');
            const freeLabel = document.getElementById('freeDeliveryLabel');
            if (subtotal >= freeThreshold) {
                freeBar.style.width = '100%';
                freeBar.className = 'progress-bar bg-success';
                freeLabel.innerHTML = '🎉 You unlocked <strong>FREE Delivery</strong>!';
            } else {
                const diff = freeThreshold - subtotal;
                const pct = Math.min(100, Math.round((subtotal / freeThreshold) * 100));
                freeBar.style.width = pct + '%';
                freeBar.className = 'progress-bar bg-warning';
                freeLabel.innerHTML = `Add <strong>Rs. ${diff.toLocaleString()}</strong> more for FREE Delivery`;
            }

            // Delivery Fee
            const deliveryFee = cart.length === 0 ? 0 : (subtotal >= freeThreshold ? 0 : 150);

            const grandTotal = subtotal + deliveryFee;

            document.getElementById('cartSubtotalText').innerText = `Rs. ${subtotal.toLocaleString()}`;
            document.getElementById('cartDeliveryFeeText').innerText = deliveryFee === 0 ? 'FREE' : `Rs. ${deliveryFee.toLocaleString()}`;
            document.getElementById('cartGrandTotalText').innerText = `Rs. ${grandTotal.toLocaleString()}`;

            // Render Items List
            const listContainer = document.getElementById('cartItemsList');
            if (cart.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cart-x fs-1 text-warning mb-2 d-block"></i>
                        <p class="mb-0">Your basket is currently empty.</p>
                        <small>Explore our crispy menu and add your favorites!</small>
                    </div>
                `;
                document.getElementById('btnPlaceOrder').disabled = true;
            } else {
                document.getElementById('btnPlaceOrder').disabled = false;
                listContainer.innerHTML = cart.map(item => `
                    <div class="cart-item-row">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-thumb">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-white small mb-1">${item.name}</div>
                            <div class="text-warning small fw-bold">Rs. ${item.price.toLocaleString()}</div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button class="cart-qty-btn" onclick="updateCartQty(${item.id}, -1)">-</button>
                            <span class="px-2 small fw-bold text-white">${item.quantity}</span>
                            <button class="cart-qty-btn" onclick="updateCartQty(${item.id}, 1)">+</button>
                            <button class="btn btn-link text-danger p-0 ms-2" onclick="removeCartItem(${item.id})" title="Remove">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }
        }



        // --- Filter Menu By Category ---
        function filterCategory(catId, btn) {
            document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const searchVal = document.getElementById('menuSearchInput').value.toLowerCase().trim();
            const cols = document.querySelectorAll('.product-item-col');
            let visibleCount = 0;

            cols.forEach(col => {
                const itemCat = col.getAttribute('data-category');
                const itemName = col.getAttribute('data-name');
                const itemDesc = col.getAttribute('data-desc');

                const matchesCat = (catId === 'all' || itemCat == catId);
                const matchesSearch = (searchVal === '' || itemName.includes(searchVal) || itemDesc.includes(searchVal));

                if (matchesCat && matchesSearch) {
                    col.classList.remove('d-none');
                    visibleCount++;
                } else {
                    col.classList.add('d-none');
                }
            });

            const noRes = document.getElementById('noResultsMsg');
            if (visibleCount === 0) {
                noRes.classList.remove('d-none');
            } else {
                noRes.classList.add('d-none');
            }
        }

        // --- Live Search on Menu ---
        function filterMenuCards() {
            const activeCatBtn = document.querySelector('.cat-pill.active');
            let catId = 'all';
            if (activeCatBtn) {
                // Find if this is not the first
                const pills = Array.from(document.querySelectorAll('.cat-pill'));
                const idx = pills.indexOf(activeCatBtn);
                if (idx > 0) {
                    // Extract category id from onclick
                    const match = activeCatBtn.getAttribute('onclick').match(/filterCategory\(([^,]+)/);
                    if (match) catId = match[1].replace(/['"]/g, '');
                }
            }

            const searchVal = document.getElementById('menuSearchInput').value.toLowerCase().trim();
            const cols = document.querySelectorAll('.product-item-col');
            let visibleCount = 0;

            cols.forEach(col => {
                const itemCat = col.getAttribute('data-category');
                const itemName = col.getAttribute('data-name');
                const itemDesc = col.getAttribute('data-desc');

                const matchesCat = (catId === 'all' || itemCat == catId);
                const matchesSearch = (searchVal === '' || itemName.includes(searchVal) || itemDesc.includes(searchVal));

                if (matchesCat && matchesSearch) {
                    col.classList.remove('d-none');
                    visibleCount++;
                } else {
                    col.classList.add('d-none');
                }
            });

            const noRes = document.getElementById('noResultsMsg');
            if (visibleCount === 0) {
                noRes.classList.remove('d-none');
            } else {
                noRes.classList.add('d-none');
            }
        }

        function resetSearch() {
            document.getElementById('menuSearchInput').value = '';
            filterCategory('all', document.querySelector('.cat-pill'));
        }

        // --- Place Order Submit Handler ---
        async function submitOrder() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Basket is empty', text: 'Please add dishes to your basket first!' });
                return;
            }

            const custName = document.getElementById('custName').value.trim();
            const custPhone = document.getElementById('custPhone').value.trim();
            const orderType = 'delivery';
            const address = document.getElementById('custAddress').value.trim();
            const notes = document.getElementById('custNotes').value.trim();

            if (!custName) {
                Swal.fire({ icon: 'error', title: 'Name required', text: 'Please enter your full name!' });
                return;
            }
            if (!custPhone) {
                Swal.fire({ icon: 'error', title: 'Phone required', text: 'Please enter your contact phone number!' });
                return;
            }
            if (orderType === 'delivery' && !address) {
                Swal.fire({ icon: 'error', title: 'Address required', text: 'Please enter delivery address for hot food dispatch!' });
                return;
            }

            const btn = document.getElementById('btnPlaceOrder');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Placing Order...';

            try {
                const response = await fetch("{{ route('website.api.order') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_name: custName,
                        customer_phone: custPhone,
                        order_type: orderType,
                        delivery_address: address,
                        order_notes: notes,
                        items: cart.map(i => ({ id: i.id, quantity: i.quantity }))
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Empty Cart
                    cart = [];
                    saveCart();

                    // Close offcanvas
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
                    if (bsOffcanvas) bsOffcanvas.hide();

                    // Show Order Confirmation Dialog with WhatsApp Send Option
                    Swal.fire({
                        icon: 'success',
                        title: '🍗 Order Placed Successfully!',
                        html: `
                            <div class="text-start p-2" style="font-size: 0.95rem;">
                                <p class="mb-1"><strong>Order ID:</strong> <span class="text-warning">#${data.order_number}</span></p>
                                <p class="mb-1"><strong>Grand Total:</strong> <span class="text-success">${data.formatted_total}</span></p>
                                <p class="mb-3 text-muted">Your order is being sent directly to the Munch Kitchen deep fryers!</p>
                                <a href="${data.whatsapp_url}" target="_blank" class="btn btn-success w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp fs-5"></i> Send Confirmation to Restaurant WhatsApp
                                </a>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Done',
                        confirmButtonColor: '#f59e0b',
                        background: '#141822',
                        color: '#ffffff'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops', text: data.message || 'Failed to place order.' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2-circle fs-5"></i> Confirm & Place Order';
            }
        }

        // Initialize UI on load
        document.addEventListener('DOMContentLoaded', () => {
            renderCartUI();
        });
    </script>
</body>
</html>
