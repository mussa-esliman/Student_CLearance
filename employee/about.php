<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "student_clearance");

//  (Authentication Guard)

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher_users') {
    header("Location: login.php"); 
    exit();
}


if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'title' => 'WDU CLEARANCE', 'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 'logout' => 'Logout',
        'dash' => '📊 Dashboard', 'req' => '📩 View Requests', 'pass' => '🔑 Change Password', 'exit' => '🚪 Logout',
        'dash_title' => 'Dashboard Overview', 'welcome' => 'Welcome back', 
        'req_title' => 'Student Clearance Requests', 'search_place' => 'Search by ID or Name...', 'search_btn' => 'Search',
        'th_name' => 'Student Name', 'th_id' => 'ID Number', 'th_status' => 'Status', 'th_action' => 'Action',
        'approve_btn' => 'Approve Now', 'processed' => '✅ Approved', 'confirm' => 'Are you sure you want to approve?', 'no_data' => 'No students found.',
        'sec_title' => 'Security Settings', 'sec_msg' => 'Update your password below to keep your account secure.',
        'old_p' => 'Old Password', 'new_p' => 'New Password', 'upd_btn' => 'Update Password', 'pending' => 'Pending', 'approved' => 'Approved',
        'abt_h' => 'About the System', 'abt_p' => 'The WDU Student Clearance System is a digital platform designed to streamline the graduation and withdrawal process for students and staff at Woldia University.',
        'con_h' => 'Contact Support', 'con_p' => 'For any technical difficulties or system access issues, please reach out to the ICT support team.',
        'total' => 'Total Requests', 'pnd' => 'Pending', 'appr' => 'Approved', 'pass_succ' => 'Password changed!', 'pass_err' => 'Old password wrong!'
    ],
    'am' => [
        'title' => 'ወልድያ ዩኒቨርሲቲ', 'home' => 'ዋና ገጽ', 'about' => 'ስለ እኛ', 'contact' => 'ያግኙን', 'logout' => 'ውጣ',
        'dash' => '📊 ዳሽቦርድ', 'req' => '📩 ጥያቄዎችን እይ', 'pass' => '🔑 የይለፍ ቃል ቀይር', 'exit' => '🚪 ውጣ',
        'dash_title' => 'ዳሽቦርድ አጠቃላይ እይታ', 'welcome' => 'እንኳን ደህና መጡ', 
        'req_title' => 'የተማሪዎች ክሊራንስ ጥያቄዎች', 'search_place' => 'በመለያ ቁጥር ወይም በስም ይፈልጉ...', 'search_btn' => 'ፈልግ',
        'th_name' => 'የተማሪ ስም', 'th_id' => 'መለያ ቁጥር', 'th_status' => 'ሁኔታ', 'th_action' => 'ድርጊት',
        'approve_btn' => 'አሁን አጽድቅ', 'processed' => '✅ ጸድቋል', 'confirm' => 'ማጽደቅዎን እርግጠኛ ነዎት?', 'no_data' => 'ምንም ተማሪ አልተገኘም።',
        'sec_title' => 'የደህንነት ቅንጅቶች', 'sec_msg' => 'የመለያዎን ደህንነት ለመጠበቅ የይለፍ ቃልዎን ከታች ይቀይሩ።',
        'old_p' => 'የድሮ የይለፍ ቃል', 'new_p' => 'አዲስ የይለፍ ቃል', 'upd_btn' => 'የይለፍ ቃል አድስ', 'pending' => 'በጥበቃ ላይ', 'approved' => 'የጸደቀ',
        'abt_h' => 'ስለ ሲስተሙ', 'abt_p' => 'የወልድያ ዩኒቨርሲቲ የተማሪዎች ክሊራንስ ሲስተም የተማሪዎችን ስንብት እና ምረቃ ሂደት ለማቀላጠፍ የተሰራ ዘመናዊ ዲጂታል መድረክ ነው።',
        'con_h' => 'ድጋፍ ያግኙ', 'con_p' => 'ለማንኛውም ቴክኒካዊ ችግር ወይም የሲስተም አጠቃቀም ጥያቄ የአይሲቲ ድጋፍ ቡድንን ያነጋግሩ።',
        'total' => 'ጠቅላላ ጥያቄ', 'pnd' => 'በጥበቃ ላይ', 'appr' => 'የጸደቁ', 'pass_succ' => 'ተቀይሯል!', 'pass_err' => 'የድሮው ስህተት ነው!'
    ]
];

$t = $words[$lang];

// (User Context)
$office = $_SESSION['office']; 
$dept = $_SESSION['user_dept'];
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];

// 4. (Statistics)
$total_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM clearance_form WHERE department='$dept'"))['c'];
$pending_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM clearance_form WHERE department='$dept' AND $office='pending'"))['c'];
$approved_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM clearance_form WHERE department='$dept' AND $office='approved'"))['c'];

// 5.  (Password Logic)
$pass_status = "";
if (isset($_POST['update_pass'])) {
    $old_p = mysqli_real_escape_string($conn, $_POST['old_p']);
    $new_p = mysqli_real_escape_string($conn, $_POST['new_p']);
    $chk = mysqli_query($conn, "SELECT id FROM teacher_users WHERE id='$user_id' AND password='$old_p'");
    if (mysqli_num_rows($chk) > 0) {
        mysqli_query($conn, "UPDATE teacher_users SET password='$new_p' WHERE id='$user_id'");
        $pass_status = "<div style='color:green; padding:10px; background:#d4edda; border-radius:5px;'>".$t['pass_succ']."</div>";
    } else {
        $pass_status = "<div style='color:red; padding:10px; background:#f8d7da; border-radius:5px;'>".$t['pass_err']."</div>";
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $t['title']; ?> | Panel</title>
    <style>
        :root { --primary: #2c3e50; --accent: #1abc9c; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; background: var(--bg); display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        
        .nav { background: var(--primary); color: white; padding: 0 25px; display: flex; justify-content: space-between; align-items: center; height: 60px; flex-shrink: 0; }
        .nav a { color: white; text-decoration: none; margin-left: 15px; font-size: 14px; }
        
        .main-wrapper { display: flex; flex: 1; overflow: hidden; }
        
        .sidebar { width: 250px; background: #34495e; color: white; padding-top: 20px; flex-shrink: 0; }
        .sidebar a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; border-bottom: 1px solid #3e4f5f; transition: 0.3s; cursor: pointer; }
        .sidebar a:hover, .active { background: var(--accent); color: white; }
        
        .content { flex: 1; padding: 30px; overflow-y: auto; display: flex; flex-direction: column; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); flex: 1; display: flex; flex-direction: column; }
        
        .stat-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { padding: 20px; border-radius: 8px; color: white; text-align: center; }
        
        .iframe-container { flex: 1; width: 100%; border: none; min-height: 500px; }

        .btn { padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; color: white; font-size: 13px; }
        .btn-prime { background: var(--accent); }
        .btn-lang { background: #555; font-size: 12px; }
        .search-input { padding: 8px; width: 200px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>

<div class="nav">
    <div style="font-weight: bold; font-size: 18px;"><?php echo $t['title']; ?></div>
    <div>
        <a href="?lang=<?php echo $lang=='en'?'am':'en'; ?>&page=<?php echo $page; ?>" class="btn-lang">🌍 <?php echo $lang=='en'?'Amharic':'English'; ?></a>
        <a href="?page=dashboard"><?php echo $t['home']; ?></a>
        <a href="?page=about"><?php echo $t['about']; ?></a>
        <a href="?page=contact"><?php echo $t['contact']; ?></a>
        <a href="logout.php" style="color: #ff7675;"><?php echo $t['logout']; ?></a>
    </div>
</div>

<div class="main-wrapper">
    <div class="sidebar">
        <div style="text-align: center; padding: 10px 0;">
            <div style="width: 50px; height: 50px; background: var(--accent); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 20px;">
                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
            </div>
            <p style="margin: 10px 0 5px; font-weight: bold;"><?php echo $full_name; ?></p>
            <small style="color: var(--accent);"><?php echo strtoupper($office); ?></small>
        </div>
        <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active':''; ?>"><?php echo $t['dash']; ?></a>
        <a href="?page=requests" class="<?php echo $page=='requests'?'active':''; ?>"><?php echo $t['req']; ?></a>
        <a href="?page=password" class="<?php echo $page=='password'?'active':''; ?>"><?php echo $t['pass']; ?></a>
    </div>

    <div class="content">
        <div class="card">
            <?php if($page == 'dashboard'): ?>
                <h2><?php echo $t['dash_title']; ?></h2>
                <p><?php echo $t['welcome']; ?>, <strong><?php echo $full_name; ?></strong></p>
                
                <div class="stat-container">
                    <div class="stat-box" style="background: #3498db;"><h3><?php echo $total_q; ?></h3><p><?php echo $t['total']; ?></p></div>
                    <div class="stat-box" style="background: #e67e22;"><h3><?php echo $pending_q; ?></h3><p><?php echo $t['pnd']; ?></p></div>
                    <div class="stat-box" style="background: #2ecc71;"><h3><?php echo $approved_q; ?></h3><p><?php echo $t['appr']; ?></p></div>
                </div>

            <?php elseif($page == 'requests'): ?>
                <iframe src="officeviw.php" class="iframe-container"></iframe>

            <?php elseif($page == 'about'): ?>
                <h2><?php echo $t['abt_h']; ?></h2>
                <p><?php echo $t['abt_p']; ?></p>

            <?php elseif($page == 'contact'): ?>
                <h2><?php echo $t['con_h']; ?></h2>
                <p><?php echo $t['con_p']; ?></p>
                <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid var(--accent);">
                    <p>📧 support@wdu.edu.et</p>
                    <p>📞 +251 333 11 00</p>
                </div>

            <?php elseif($page == 'password'): ?>
                <h2><?php echo $t['sec_title']; ?></h2>
                <p><?php echo $t['sec_msg']; ?></p>
                <div style="max-width: 400px; margin-top: 20px;">
                    <?php echo $pass_status; ?>
                    <form method="POST">
                        <label><?php echo $t['old_p']; ?></label>
                        <input type="password" name="old_p" class="search-input" style="width: 100%; margin: 10px 0;" required>
                        <label><?php echo $t['new_p']; ?></label>
                        <input type="password" name="new_p" class="search-input" style="width: 100%; margin: 10px 0;" required>
                        <button name="update_pass" class="btn btn-prime" style="width: 100%; margin-top: 10px;"><?php echo $t['upd_btn']; ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>