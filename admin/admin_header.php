<?php
session_start();
include('db_conn.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin_users') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WDU | Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        header { background: #1a2a6c; color: white; padding: 0 25px; height: 70px; display: flex; justify-content: space-between; align-items: center; z-index: 100; border-bottom: 3px solid #38bdf8; }
        .logo-area h2 { font-size: 18px; letter-spacing: 1px; }
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-links a { color: #cbd5e1; text-decoration: none; font-size: 15px; font-weight: 500; cursor: pointer; transition: 0.3s; padding: 5px 10px; border-radius: 4px; }
        .nav-links a:hover, .nav-links a.active-nav { color: white; background: rgba(255,255,255,0.1); }
        .wrapper { display: flex; flex: 1; overflow: hidden; }
        .sidebar { width: 240px; background: #1e293b; color: #cbd5e1; display: flex; flex-direction: column; }
        .sidebar-label { padding: 20px 20px 5px; font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a { padding: 13px 20px; color: #94a3b8; text-decoration: none; border-left: 4px solid transparent; transition: 0.3s; cursor: pointer; display: block; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active-side { background: #334155; color: white; border-left: 4px solid #38bdf8; }
        .main { flex: 1; display: flex; flex-direction: column; background: #f8fafc; position: relative; overflow-y: auto; }
        .view-section { display: none; width: 100%; height: 60%; }
        #dashboard-view { padding: 40px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; }
        .stat-card { padding: 40px 20px; border-radius: 15px; color: white; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-1 { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
        .card-2 { background: linear-gradient(135deg, #9a3412, #ea580c); }
        .card-3 { background: linear-gradient(135deg, #065f46, #10b981); }
        .stat-card h2 { font-size: 45px; margin-top: 10px; }
        .info-panel { padding: 50px; background: white; margin: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body onload="showView('dashboard-view', document.getElementById('default-nav'))">
    <header>
        <div class="logo-area">
            <h2>WDU <span style="font-weight: 300; color: #38bdf8;">CLEARANCE ADMIN</span></h2>
        </div>
        <nav class="nav-links">
            <a onclick="showView('dashboard-view', this)" id="default-nav">🏠 Home</a>
            <a onclick="showView('about-view', this)">ℹ️ About Us</a>
            <a onclick="showView('contact-view', this)">📞 Contact Us</a>
            <span style="color: #64748b; margin: 0 10px;">|</span>
            <div style="font-size: 13px; color: #38bdf8;">Admin: <b>Active</b></div>
        </nav>
    </header>