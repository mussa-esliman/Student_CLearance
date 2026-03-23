<?php
session_start();

// ---------------------------------------------------------
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_clearance";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("<div style='color:red; text-align:center; padding:50px;'>
            <h2>Critical Error: Database Connection Failed</h2>
            <p>" . mysqli_connect_error() . "</p>
         </div>");
}

// ---------------------------------------------------------
if (!isset($_SESSION['user_id']) || !isset($_SESSION['id_no'])) {
    header("Location: login.php"); 
    exit();
}

// ---------------------------------------------------------
$user_id   = $_SESSION['user_id'];
$id_no     = $_SESSION['id_no'];
$full_name = $_SESSION['full_name'];
$lang      = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$page      = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: student_dashboard.php?page=" . $page);
    exit();
}

// ---------------------------------------------------------
$words = [
    'en' => [
        'title'   => 'WDU | Student Portal',
        'home'    => 'Home Dashboard',
        'about'   => 'About Portal',
        'contact' => 'Support Desk',
        'req'     => 'Clearance Request',
        'stat'    => 'Track Status',
        'appeal'  => 'Student Appeal',
        'pass'    => 'Change Password',
        'logout'  => 'Exit System',
        'welcome' => 'Welcome, Student',
        'footer'  => 'Woldia University Management System'
    ],
    'am' => [
        'title'   => 'ወልድያ ዩኒቨርሲቲ | ተማሪ',
        'home'    => 'ዋና ዳሽቦርድ',
        'about'   => 'ስለ ፖርታሉ',
        'contact' => 'የድጋፍ ማዕከል',
        'req'     => 'ክሊራንስ መጠየቂያ',
        'stat'    => 'ሁኔታ መከታተያ',
        'appeal'  => 'ቅሬታ ማቅረቢያ',
        'pass'    => 'የይለፍ ቃል ቀይር',
        'logout'  => 'ከሲስተሙ ውጣ',
        'welcome' => 'እንኳን ደህና መጡ',
        'footer'  => 'የወልድያ ዩኒቨርሲቲ አስተዳደር ሲስተም'
    ]
];
$t = $words[$lang];

// ---------------------------------------------------------
$clearance_q = mysqli_query($conn, "SELECT * FROM clearance_form WHERE id_no = '$id_no'");
$c_data      = mysqli_fetch_assoc($clearance_q);
$has_request = mysqli_num_rows($clearance_q) > 0;

$is_rejected = false;
if ($has_request) {
    $offices = ['library', 'registrar', 'student_cafeteria', 'student_procter', 'campus_security'];
    foreach ($offices as $off) {
        if (isset($c_data[$off]) && strtolower($c_data[$off]) == 'rejected') {
            $is_rejected = true; break;
        }
    }
}

// ---------------------------------------------------------
$pass_msg = "";
if (isset($_POST['update_pw'])) {
    $old_p  = mysqli_real_escape_string($conn, $_POST['old_p']);
    $new_p  = mysqli_real_escape_string($conn, $_POST['new_p']);
    $conf_p = mysqli_real_escape_string($conn, $_POST['conf_p']);

    $check_q   = mysqli_query($conn, "SELECT password FROM student_users WHERE id='$user_id'");
    $user_row  = mysqli_fetch_assoc($check_q);

    if ($old_p !== $user_row['password']) {
        $pass_msg = "<div class='alert err'>❌ Current password is incorrect!</div>";
    } elseif ($new_p !== $conf_p) {
        $pass_msg = "<div class='alert err'>❌ Password confirmation mismatch!</div>";
    } else {
        mysqli_query($conn, "UPDATE student_users SET password='$new_p' WHERE id='$user_id'");
        $pass_msg = "<div class='alert succ'>✅ Password updated successfully!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --accent-color: #3b82f6;
            --text-light: #f8fafc;
            --text-dim: #94a3b8;
            --body-bg: #f1f5f9;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--body-bg); display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar Styling */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); color: white;
            display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar-header { padding: 40px 20px; text-align: center; border-bottom: 1px solid #1e293b; }
        .avatar {
            width: 80px; height: 80px; background: var(--accent-color); border-radius: 50%;
            margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;
            font-size: 32px; border: 4px solid #334155; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        .nav-menu { flex: 1; padding: 25px 0; overflow-y: auto; }
        .nav-item {
            display: flex; align-items: center; padding: 15px 30px; color: var(--text-dim);
            text-decoration: none; border-left: 5px solid transparent; transition: 0.3s all;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--sidebar-hover); color: white; border-left-color: var(--accent-color);
        }
        .nav-item.alert-link { color: #fca5a5; font-weight: 600; }
        .nav-item i { margin-right: 15px; width: 20px; text-align: center; }

        /* Header Styling */
        .content-wrapper { flex: 1; display: flex; flex-direction: column; }
        .top-navbar {
            height: 75px; background: white; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between; padding: 0 45px;
        }
        .top-navbar nav a {
            text-decoration: none; color: #475569; margin-left: 30px;
            font-size: 14px; font-weight: 500; transition: 0.3s;
        }
        .top-navbar nav a:hover, .top-navbar nav a.active-top { color: var(--accent-color); }

        /* Main Content Styling */
        .main-view { padding: 40px; overflow-y: auto; flex: 1; position: relative; }
        
        /* Iframe-specific full view logic */
        .main-view.iframe-active { padding: 0 !important; overflow: hidden; }

        .content-card {
            background: white; padding: 40px; border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 1100px; margin: 0 auto;
        }

        /* Logic to expand card to full size when iframe is inside */
        .content-card.iframe-card { 
            max-width: 100% !important; 
            height: 100% !important; 
            padding: 0 !important; 
            border-radius: 0 !important; 
            box-shadow: none !important; 
            margin: 0 !important;
        }

        /* Form Controls */
        .form-box { max-width: 500px; margin: 30px 0; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 10px; color: #1e293b; font-weight: 500; }
        .input-group input {
            width: 100%; padding: 14px; border: 1.5px solid #cbd5e1;
            border-radius: 10px; outline: none; transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--accent-color); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .submit-btn {
            background: var(--accent-color); color: white; padding: 14px 35px;
            border: none; border-radius: 10px; cursor: pointer; font-weight: 600;
        }

        /* Alerts & Utils */
        .alert { padding: 18px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; }
        .err { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .succ { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        
        iframe { width: 100%; height: 100%; border: none; display: block; }
        #live-clock { font-size: 14px; color: #38bdf8; margin-top: 12px; font-weight: 500; }

        @media (max-width: 768px) { .sidebar { width: 80px; } .sidebar span { display: none; } }
    </style>
</head>
<body onload="startClock()">

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="avatar">👨‍🎓</div>
            <h4 title="<?php echo $full_name; ?>"><?php echo substr($full_name, 0, 16); ?></h4>
            <div id="live-clock"></div>
        </div>

        <nav class="nav-menu">
            <a href="?page=dashboard" class="nav-item <?php echo $page=='dashboard'?'active':''; ?>">
                <i>🏠</i> <span><?php echo $t['home']; ?></span>
            </a>
            <a href="?page=request" class="nav-item <?php echo $page=='request'?'active':''; ?>">
                <i>📩</i> <span><?php echo $t['req']; ?></span>
            </a>
            <a href="?page=status" class="nav-item <?php echo $page=='status'?'active':''; ?>">
                <i>🔍</i> <span><?php echo $t['stat']; ?></span>
            </a>
            
            <a href="?page=appeal" class="nav-item <?php echo $page=='appeal'?'active':''; ?> <?php echo $is_rejected?'alert-link':''; ?>">
                <i>⚖️</i> <span><?php echo $t['appeal']; ?></span>
                <?php if($is_rejected): ?> <span style="color:var(--danger); margin-left:10px;">●</span> <?php endif; ?>
            </a>

            <a href="?page=password" class="nav-item <?php echo $page=='password'?'active':''; ?>">
                <i>🔑</i> <span><?php echo $t['pass']; ?></span>
            </a>
        </nav>

        <div style="padding: 25px; border-top: 1px solid #1e293b;">
            <a href="?lang=<?php echo $lang=='en'?'am':'en'; ?>&page=<?php echo $page; ?>" style="color: #38bdf8; text-decoration:none; font-size:13px; font-weight:600;">
                🌐 <?php echo $lang=='en'?'አማርኛ - Amharic':'English Version'; ?>
            </a>
            <a href="logout.php" style="display:block; margin-top:20px; color: var(--danger); text-decoration:none; font-size:14px; font-weight:700;">
                🚪 <?php echo $t['logout']; ?>
            </a>
        </div>
    </aside>

    <div class="content-wrapper">
        <header class="top-navbar">
            <div style="font-weight: 800; font-size: 22px; color: var(--sidebar-bg);">WDU <span style="color:var(--accent-color)">PORTAL</span></div>
            <nav>
                <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active-top':''; ?>"><?php echo $t['home']; ?></a>
                <a href="?page=about" class="<?php echo $page=='about'?'active-top':''; ?>"><?php echo $t['about']; ?></a>
                <a href="?page=contact" class="<?php echo $page=='contact'?'active-top':''; ?>"><?php echo $t['contact']; ?></a>
            </nav>
        </header>

        <?php 
            $is_iframe_page = ($page == 'request' || $page == 'status' || $page == 'appeal');
            $main_class = $is_iframe_page ? 'iframe-active' : '';
            $card_class = $is_iframe_page ? 'iframe-card' : '';
        ?>

        <main class="main-view <?php echo $main_class; ?>">
            <div class="content-card <?php echo $card_class; ?>">
                
                <?php if($page == 'dashboard'): ?>
                    <h2 style="color: var(--sidebar-bg); margin-bottom: 15px;"><?php echo $t['welcome']; ?>, <?php echo $full_name; ?>!</h2>
                    <p style="color: #64748b; line-height: 1.6;">Your digital student dashboard provides a seamless way to handle clearance requests and academic exits at Woldia University.</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 35px;">
                        <div style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <h5 style="color: #94a3b8; font-size: 12px; text-transform: uppercase;">ID Number</h5>
                            <p style="font-weight: 600; font-size: 18px; color: var(--sidebar-bg);"><?php echo $id_no; ?></p>
                        </div>
                        <div style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <h5 style="color: #94a3b8; font-size: 12px; text-transform: uppercase;">Request Status</h5>
                            <p style="font-weight: 600; font-size: 18px;">
                                <?php 
                                    if(!$has_request) echo "<span style='color:#64748b'>Not Requested</span>";
                                    elseif($is_rejected) echo "<span style='color:var(--danger)'>Rejected</span>";
                                    else echo "<span style='color:var(--success)'>In Progress</span>";
                                ?>
                            </p>
                        </div>
                    </div>

                <?php elseif($page == 'about'): ?>
                    <h2>About the Portal</h2>
                    <p style="margin-top:20px; color:#475569;">The Woldia University Student Digital Clearance System is a modern web solution built to eliminate physical paperwork during the student exit process.</p>

                <?php elseif($page == 'contact'): ?>
                    <h2>Support Center</h2>
                    <div style="margin-top:25px;">
                        <p>📍 Location: Main Campus, Technology Directorate</p>
                        <p>📞 Office: +2519 29 24 00 43</p>
                        <p>📧 Support Email: support@wdu.edu.et</p>
                    </div>

                <?php elseif($page == 'password'): ?>
                    <h2>Security Settings</h2>
                    <div class="form-box">
                        <?php echo $pass_msg; ?>
                        <form method="POST">
                            <div class="input-group">
                                <label>Current Password</label>
                                <input type="password" name="old_p" required>
                            </div>
                            <div class="input-group">
                                <label>New Password</label>
                                <input type="password" name="new_p" required minlength="6">
                            </div>
                            <div class="input-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="conf_p" required>
                            </div>
                            <button name="update_pw" class="submit-btn">Update Security Key</button>
                        </form>
                    </div>

                <?php else: ?>
                    <?php 
                        $source = "stud_requst1.php";
                        if($page == 'status') $source = "check_status.php";
                        if($page == 'appeal') $source = "student_appeal.php";
                        // The height is now handled by the iframe-card class
                        echo "<iframe src='$source'></iframe>";
                    ?>
                <?php endif; ?>

            </div>
            
            <?php if(!$is_iframe_page): ?>
            <footer style="margin-top: 45px; text-align: center; color: #94a3b8; font-size: 12px;">
                <p><?php echo $t['footer']; ?> &copy; <?php echo date('Y'); ?> | Built by WDU ICT</p>
            </footer>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function startClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerHTML = "🕒 " + timeStr;
            setTimeout(startClock, 1000);
        }
    </script>
</body>
</html>