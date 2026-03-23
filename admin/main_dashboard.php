<?php
session_start();
if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 
        'login' => 'Access Portal', 'dash' => 'Main Dashboard', 'profile' => 'User Profile',
        'settings' => 'System Settings', 'welcome_title' => 'WDU Clearance Management'
    ],
    'am' => [
        'home' => 'ዋና ገጽ', 'about' => 'ስለ እኛ', 'contact' => 'ያግኙን', 
        'login' => 'መግቢያ ገጽ', 'dash' => 'ዳሽቦርድ', 'profile' => 'መገለጫ',
        'settings' => 'ማስተካከያ', 'welcome_title' => 'የወልድያ ዩኒቨርሲቲ ክሊራንስ'
    ]
];
$t = $words[$lang];
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDU | Centralized Digital Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-navy: #0f172a; 
            --sidebar-dark: #020617;
            --gold-accent: #fbbf24; 
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --card-glass: rgba(30, 41, 59, 0.7);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; display: flex; height: 100vh; 
            background-color: var(--deep-navy);
            background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            overflow: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 280px;
            background: var(--sidebar-dark);
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar-header img {
            width: 85px;
            border-radius: 20px;
            padding: 5px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(251, 191, 36, 0.3);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .nav-menu { flex: 1; }
        .nav-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 10px;
            border-radius: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-menu a:hover {
            background: rgba(251, 191, 36, 0.1);
            color: var(--gold-accent);
            transform: translateX(5px);
        }

        .nav-menu a.active {
            background: var(--gold-accent);
            color: var(--sidebar-dark);
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.2);
        }

        .login-special {
            margin-top: 20px;
            border: 1.5px dashed rgba(251, 191, 36, 0.4);
            color: var(--gold-accent) !important;
        }

        /* --- Main Content --- */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }

        .top-bar {
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 50px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .brand-text {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #fff, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .top-nav-links { display: flex; gap: 15px; }
        .top-nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .top-nav-links a:hover, .top-nav-links a.active {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .content-container {
            flex: 1;
            padding: 35px;
            display: flex;
        }

        .main-card {
            background: var(--card-glass);
            backdrop-filter: blur(15px);
            flex: 1;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 20px;
            background: transparent;
        }

        /* Hero Styling */
        .welcome-hero {
            max-width: 700px;
            margin: 60px auto;
            text-align: center;
        }

        .welcome-hero h1 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
        }

        .welcome-hero p {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: 50px;
        }

        .stat-item {
            background: rgba(255,255,255,0.03);
            padding: 25px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
        }

        .stat-item:hover {
            background: rgba(255,255,255,0.05);
            border-color: var(--gold-accent);
            transform: translateY(-5px);
        }

        .stat-icon { font-size: 30px; margin-bottom: 15px; }
        .stat-label { font-weight: 700; color: var(--gold-accent); }
        .stat-desc { font-size: 12px; color: var(--text-muted); margin-top: 5px; }

    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <img src="photo_2025-12-11_16-23-22.jpg" alt="WDU Logo">
        <div style="margin-top: 20px; font-weight: 800; font-size: 14px; color: white; letter-spacing: 1px;">WDU PORTAL</div>
    </div>

    <nav class="nav-menu">
        <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">
            <span style="margin-right:15px;">🏛️</span> <?= $t['dash'] ?>
        </a>
        <a href="?page=profile" class="<?= $page=='profile'?'active':'' ?>">
            <span style="margin-right:15px;">🛡️</span> <?= $t['profile'] ?>
        </a>
        <a href="?page=settings" class="<?= $page=='settings'?'active':'' ?>">
            <span style="margin-right:15px;">🔧</span> <?= $t['settings'] ?>
        </a>
        
        <a href="?page=login" class="login-special <?= $page=='login'?'active':'' ?>">
            <span style="margin-right:15px;">🔐</span> <?= $t['login'] ?>
        </a>
    </nav>

    <div style="margin-top: auto; text-align: center;">
        <a href="?lang=<?= $lang=='en'?'am':'en' ?>" style="color: var(--gold-accent); text-decoration: none; font-size: 12px; font-weight: 800; letter-spacing: 1px; opacity: 0.8;">
            <?= $lang=='en'?'አማርኛ ስሪት':'ENGLISH VERSION' ?>
        </a>
    </div>
</aside>

<div class="main-wrapper">
    <header class="top-bar">
        <div class="brand-text">WOLDIA UNIVERSITY DIGITAL SYSTEM</div>
        <nav class="top-nav-links">
            <a href="?page=home" class="<?= $page=='home'?'active':'' ?>"><?= $t['home'] ?></a>
            <a href="?page=about" class="<?= $page=='about'?'active':'' ?>"><?= $t['about'] ?></a>
            <a href="?page=contact" class="<?= $page=='contact'?'active':'' ?>"><?= $t['contact'] ?></a>
        </nav>
    </header>

    <main class="content-container">
        <div class="main-card">
            <?php if($page == 'dashboard' || $page == 'home'): ?>
                <div class="welcome-hero">
                    <h1><?= $t['welcome_title'] ?></h1>
                    <p>Experience a unified, secure, and lightning-fast clearance process. All your administrative needs managed in one professional environment.</p>
                    
                    <div class="grid-stats">
                        <div class="stat-item">
                            <div class="stat-icon">🎓</div>
                            <div class="stat-label">Students</div>
                            <div class="stat-desc">Self-Service Portal</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">🏢</div>
                            <div class="stat-label">Offices</div>
                            <div class="stat-desc">Central Verification</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">📑</div>
                            <div class="stat-label">Records</div>
                            <div class="stat-desc">Automated Archives</div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php 
                    $target = "dashboard.php";
                    if($page == 'login') $target = "login.php";
                    if($page == 'profile') $target = "profile_view.php";
                    if($page == 'settings') $target = "settings_page.php";
                    if($page == 'about') $target = "about_us.php";
                    if($page == 'contact') $target = "contact_us.php";
                ?>
                <iframe src="<?= $target ?>"></iframe>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>