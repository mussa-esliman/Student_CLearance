<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher_users') {
    header("Location: login.php"); exit();
}


if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'title' => 'WDU CLEARANCE SYSTEM', 'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 'logout' => 'Logout',
        'dash' => '📊 Dashboard', 'req' => '📩 View Requests', 'pass' => '🔑 Password', 'exit' => '🚪 Logout',
        'dash_title' => 'Dashboard Overview', 'welcome' => 'Welcome back', 'dash_msg' => 'You are managing requests for',
        'sec_title' => 'Security Settings', 'old_p' => 'Old Password', 'new_p' => 'New Password', 'upd_btn' => 'Update Password',
        'abt_h' => 'About WDU Clearance', 'abt_p' => 'Woldia University Student Clearance System is an efficient digital workflow designed to automate and simplify the graduation process for students and staff.',
        'con_h' => 'Contact Support', 'phone' => 'Phone: +251 333 11 00', 'email' => 'Email: support@wdu.edu.et',
        'pass_err' => 'Old password incorrect!', 'pass_succ' => 'Password updated!',
        'gen_rep' => '📄 Generate Report', 'appeals' => '📩 Student Appeals'
    ],
    'am' => [
        'title' => 'የወልድያ ዩኒቨርሲቲ ክሊራንስ', 'home' => 'ዋና ገጽ', 'about' => 'ስለ እኛ', 'contact' => 'ያግኙን', 'logout' => 'ውጣ',
        'dash' => '📊 ዳሽቦርድ', 'req' => '📩 ጥያቄዎችን እይ', 'pass' => '🔑 የይለፍ ቃል', 'exit' => '🚪 ውጣ',
        'dash_title' => 'ዳሽቦርድ አጠቃላይ እይታ', 'welcome' => 'እንኳን ደህና መጡ', 'dash_msg' => 'እያስተዳደሩ ያሉት ክፍል፡',
        'sec_title' => 'የደህንነት ቅንጅቶች', 'old_p' => 'የድሮ የይለፍ ቃል', 'new_p' => 'አዲስ የይለፍ ቃል', 'upd_btn' => 'የይለፍ ቃል አድስ',
        'abt_h' => 'ስለ ወልድያ ዩኒቨርሲቲ ክሊራንስ', 'abt_p' => 'የወልድያ ዩኒቨርሲቲ የተማሪዎች ክሊራንስ ሲስተም የተማሪዎችን እና የሰራተኞችን የስራ ሂደት ለማቀላጠፍ የተሰራ ዲጂታል ሲስተም ነው።',
        'con_h' => 'እርዳታ ያግኙ', 'phone' => 'ስልክ: +251 333 11 00', 'email' => 'ኢሜል: support@wdu.edu.et',
        'pass_err' => 'የድሮው የይለፍ ቃል ስህተት ነው!', 'pass_succ' => 'የይለፍ ቃልዎ ተቀይሯል!',
        'gen_rep' => '📄 ሪፖርት አውጣ', 'appeals' => '📩 የተማሪዎች ቅሬታ'
    ]
];

$t = $words[$lang];
$office = $_SESSION['office']; 
$dept = $_SESSION['user_dept'];
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];

// --- Notification Logic ---
$notif_q = mysqli_query($conn, "SELECT COUNT(*) as pending FROM clearance_form WHERE $office = 'pending'");
$pending_count = mysqli_fetch_assoc($notif_q)['pending'];

$appeal_q = mysqli_query($conn, "SELECT COUNT(*) as apps FROM appeals WHERE office = '$office' AND status = 'pending'");
$appeal_count = mysqli_fetch_assoc($appeal_q)['apps'];

// --- Password Update Logic ---
$pass_status = "";
if (isset($_POST['update_pass'])) {
    $old_p = mysqli_real_escape_string($conn, $_POST['old_p']);
    $new_p = mysqli_real_escape_string($conn, $_POST['new_p']);
    $chk = mysqli_query($conn, "SELECT id FROM teacher_users WHERE id='$user_id' AND password='$old_p'");
    if (mysqli_num_rows($chk) > 0) {
        mysqli_query($conn, "UPDATE teacher_users SET password='$new_p' WHERE id='$user_id'");
        $pass_status = "<p style='color:green; background:#d4edda; padding:10px; border-radius:5px;'>".$t['pass_succ']."</p>";
    } else {
        $pass_status = "<p style='color:red; background:#f8d7da; padding:10px; border-radius:5px;'>".$t['pass_err']."</p>";
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Panel | WDU</title>
    <style>
        :root { --nav-bg: #1a252f; --side-bg: #2c3e50; --accent: #00ffcc; --danger: #e74c3c; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f7f6; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Navigation */
        .top-nav { background: var(--nav-bg); color: white; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; height: 60px; z-index: 1000; }
        .top-nav a { color: white; text-decoration: none; margin-left: 15px; font-size: 14px; }

        /* Sidebar */
        .wrapper { display: flex; flex: 1; overflow: hidden; }
        .sidebar { width: 260px; background: var(--side-bg); color: white; padding-top: 10px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; padding: 15px 25px; transition: 0.3s; border-bottom: 1px solid #34495e; position: relative; cursor: pointer; }
        .sidebar a:hover, .active-link { background: #34495e; color: var(--accent); border-left: 5px solid var(--accent); }
        
        /* Badges */
        .notif-badge { background: var(--danger); color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; position: absolute; right: 15px; font-weight: bold; }
        
        /* Content Area */
        .main-content { flex: 1; padding: 25px; overflow-y: auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); min-height: 80vh; }
        
        .status-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .status-table th, .status-table td { border: 1px solid #eee; padding: 12px; text-align: left; }
        .btn-approve { background: #27ae60; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
        .btn-reject { background: var(--danger); color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-top: 5px; }
        
        #viewFrame { width: 100%; height: 85vh; border: none; display: none; }
        
        @media print { 
            .top-nav, .sidebar, .btn-print { display: none !important; } 
            .main-content { padding: 0; margin: 0; width: 100%; } 
            .card { box-shadow: none; border: none; padding: 0; } 
            body { overflow: visible; }
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="logo"><strong>WDU</strong> TEACHER PANEL</div>
    <div class="links">
        <a href="?page=dashboard"><?php echo $t['home']; ?></a>
        <a href="?page=about"><?php echo $t['about']; ?></a>
        <a href="?page=contact"><?php echo $t['contact']; ?></a>
        <a href="?lang=<?php echo $lang=='en'?'am':'en'; ?>&page=<?php echo $page; ?>" style="background:#34495e; padding:5px 10px; border-radius:4px;">🌍 <?php echo $lang=='en'?'አማርኛ':'English'; ?></a>
        <a href="logout.php" style="color:#ff7675; font-weight:bold;"><?php echo $t['logout']; ?></a>
    </div>
</nav>

<div class="wrapper">
    <aside class="sidebar">
        <div style="text-align:center; padding: 20px; border-bottom: 1px solid #3e4f5f;">
            <div style="width:60px; height:60px; background:var(--accent); border-radius:50%; margin:auto; display:flex; align-items:center; justify-content:center; color:#1a252f; font-weight:bold; font-size:22px;">
                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
            </div>
            <p style="margin: 10px 0 0; font-weight:bold;"><?php echo $full_name; ?></p>
            <small style="color:var(--accent);"><?php echo strtoupper($office); ?></small>
        </div>

        <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active-link':''; ?>"><?php echo $t['dash']; ?></a>
        
        <a onclick="toggleView('requests')" class="<?php echo $page=='requests'?'active-link':''; ?>">
            <?php echo $t['req']; ?>
            <?php if($pending_count > 0): ?><span class="notif-badge"><?php echo $pending_count; ?></span><?php endif; ?>
        </a>

        <a href="?page=appeals" class="<?php echo $page=='appeals'?'active-link':''; ?>">
            <?php echo $t['appeals']; ?>
            <?php if($appeal_count > 0): ?><span class="notif-badge" style="background:#f39c12;"><?php echo $appeal_count; ?></span><?php endif; ?>
        </a>

        <a href="?page=report" class="<?php echo $page=='report'?'active-link':''; ?>"><?php echo $t['gen_rep']; ?></a>
        <a href="?page=password" class="<?php echo $page=='password'?'active-link':''; ?>"><?php echo $t['pass']; ?></a>
    </aside>

    <main class="main-content">
        <iframe id="viewFrame" src="officeviw.php"></iframe>

        <div id="contentSection" class="card">
            <?php if($page == 'dashboard'): ?>
                <h2><?php echo $t['dash_title']; ?></h2>
                <hr>
                <div style="background: #e8f4fd; padding: 20px; border-left: 5px solid #3498db; margin-top:20px;">
                    <h3><?php echo $t['welcome']; ?>, <?php echo $full_name; ?>!</h3>
                    <p><?php echo $t['dash_msg']; ?> <strong><?php echo $dept; ?> / <?php echo $office; ?></strong></p>
                </div>
                
                <div style="display:flex; gap:20px; margin-top:20px;">
                    <div style="flex:1; background:#fff; padding:20px; border-radius:8px; border:1px solid #ddd; text-align:center;">
                        <h4 style="margin:0; color:#666;">Pending Requests</h4>
                        <h1 style="color:var(--danger); font-size:40px;"><?php echo $pending_count; ?></h1>
                    </div>
                    <div style="flex:1; background:#fff; padding:20px; border-radius:8px; border:1px solid #ddd; text-align:center;">
                        <h4 style="margin:0; color:#666;">Student Appeals</h4>
                        <h1 style="color:#f39c12; font-size:40px;"><?php echo $appeal_count; ?></h1>
                    </div>
                </div>

            <?php elseif($page == 'about'): ?>
                <h2><?php echo $t['abt_h']; ?></h2>
                <hr>
                <p style="font-size: 1.1em; line-height: 1.6; color: #34495e;"><?php echo $t['abt_p']; ?></p>

            <?php elseif($page == 'contact'): ?>
                <h2><?php echo $t['con_h']; ?></h2>
                <hr>
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <p><b><?php echo $t['phone']; ?></b></p>
                    <p><b><?php echo $t['email']; ?></b></p>
                </div>

            <?php elseif($page == 'appeals'): ?>
                <h2><?php echo $t['appeals']; ?></h2>
                <hr>
                <?php
                $apps = mysqli_query($conn, "SELECT * FROM appeals WHERE office = '$office' AND status = 'pending'");
                if(mysqli_num_rows($apps) > 0): ?>
                <table class="status-table">
                    <tr style="background:#f8f9fa;"><th>Student ID</th><th>Reason</th><th>Your Response</th><th>Action</th></tr>
                    <?php while($ap = mysqli_fetch_assoc($apps)): ?>
                    <tr>
                        <form action="handle_appeal.php" method="POST">
                            <td width="15%"><b><?php echo $ap['id_no']; ?></b></td>
                            <td width="35%"><?php echo $ap['reason']; ?></td>
                            <td width="30%"><textarea name="reply_msg" style="width:100%; border-radius:4px; border:1px solid #ccc;" placeholder="ምላሽ ይጻፉ..." required></textarea></td>
                            <td width="20%">
                                <input type="hidden" name="id_no" value="<?php echo $ap['id_no']; ?>">
                                <button name="action" value="approve" class="btn-approve">Approve</button>
                                <button name="action" value="reject" class="btn-reject">Reject</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <?php else: echo "<p style='text-align:center; padding:30px; color:#7f8c8d;'>No pending appeals found.</p>"; endif; ?>

            <?php elseif($page == 'report'): ?>
                <h2 style="text-align:center;">WOLDIA UNIVERSITY CLEARANCE REPORT</h2>
                <p style="text-align:center;">Office: <?php echo strtoupper($office); ?> | Date: <?php echo date('Y-m-d'); ?></p>
                <hr>
                <div style="text-align:center; padding:40px;">
                    <div style="display:inline-block; text-align:left; border:1px solid #ddd; padding:20px; border-radius:10px; margin-bottom:20px;">
                        <p>Total Pending Requests: <b><?php echo $pending_count; ?></b></p>
                        <p>Total Student Appeals: <b><?php echo $appeal_count; ?></b></p>
                    </div>
                    <br>
                    <button onclick="window.print()" class="btn-print" style="background:var(--nav-bg); color:white; padding:15px 30px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">🖨️ PRINT SUMMARY REPORT</button>
                </div>

            <?php elseif($page == 'password'): ?>
                <h2><?php echo $t['sec_title']; ?></h2>
                <hr>
                <div style="max-width: 450px; margin-top: 20px;">
                    <?php echo $pass_status; ?>
                    <form method="POST">
                        <label><b><?php echo $t['old_p']; ?></b></label>
                        <input type="password" name="old_p" style="width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:6px;" required>
                        <label><b><?php echo $t['new_p']; ?></b></label>
                        <input type="password" name="new_p" style="width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:6px;" required>
                        <button name="update_pass" style="background:#3498db; color:white; border:none; padding:15px; width:100%; border-radius:6px; cursor:pointer; font-weight:bold;"><?php echo $t['upd_btn']; ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function toggleView(view) {
        var frame = document.getElementById('viewFrame');
        var content = document.getElementById('contentSection');
        if(view === 'requests') {
            frame.style.display = 'block';
            content.style.display = 'none';
        } else {
            frame.style.display = 'none';
            content.style.display = 'block';
        }
    }
    <?php if($page == 'requests') echo "toggleView('requests');"; ?>
</script>

</body>
</html>