<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee_users') {
    header("Location: login.php"); exit();
}

if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'title' => 'WDU EMPLOYEE PANEL', 'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 'logout' => 'Logout',
        'dash' => '📊 Dashboard', 'req' => '📩 View Requests', 'pass' => '🔑 Password', 'exit' => '🚪 Logout',
        'abt_p' => 'WDU Clearance system digitizes the manual process for employees and students. It helps to track clearance status online without physical presence.',
        'con_p' => 'Contact ICT Support: +251 333 11 00', 'welcome' => 'Welcome back', 'office_msg' => 'You are managing',
        'pass_succ' => 'Password Updated!', 'pass_err' => 'Old password wrong!',
        'gen_rep' => '📄 Generate Report',
        'appeals' => '📩 Student Appeals' 
    ],
    'am' => [
        'title' => 'የሰራተኛ ገጽ', 'home' => 'ዋና ገጽ', 'about' => 'ስለ እኛ', 'contact' => 'ያግኙን', 'logout' => 'ውጣ',
        'dash' => '📊 ዳሽቦርድ', 'req' => '📩 ጥያቄዎችን እይ', 'pass' => '🔑 የይለፍ ቃል', 'exit' => '🚪 ውጣ',
        'abt_p' => 'የወልድያ ዩኒቨርሲቲ ክሊራንስ ሲስተም የሰራተኞችን እና የተማሪዎችን ስራ ለማቅለል የተሰራ ነው። ተማሪዎች ባሉበት ሆነው ክሊራንስ እንዲጨርሱ ይረዳል።',
        'con_p' => 'አይሲቲ ድጋፍን ያግኙ: +251 333 11 00', 'welcome' => 'እንኳን ደህና መጡ', 'office_msg' => 'እያስተዳደሩ ያሉት ክፍል፡',
        'pass_succ' => 'የይለፍ ቃል ተቀይሯል!', 'pass_err' => 'የድሮው ይለፍ ቃል ስህተት ነው!',
        'gen_rep' => '📄 ሪፖርት አውጣ',
        'appeals' => '📩 የተማሪዎች ቅሬታ' 
    ]
];

$t = $words[$lang];
$office = $_SESSION['office']; 
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];

$notif_q = mysqli_query($conn, "SELECT COUNT(*) as pending FROM clearance_form WHERE $office = 'pending'");
$notif_res = mysqli_fetch_assoc($notif_q);
$pending_count = $notif_res['pending'];

$app_q = mysqli_query($conn, "SELECT COUNT(*) as apps FROM appeals WHERE office = '$office' AND status = 'pending'");
$app_res = mysqli_fetch_assoc($app_q);
$appeal_count = $app_res['apps'];

$pass_msg = "";
if(isset($_POST['update_pass'])){
    $old = mysqli_real_escape_string($conn, $_POST['old_p']);
    $new = mysqli_real_escape_string($conn, $_POST['new_p']);
    $check = mysqli_query($conn, "SELECT * FROM employee_users WHERE id='$user_id' AND password='$old'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn, "UPDATE employee_users SET password='$new' WHERE id='$user_id'");
        $pass_msg = "<p style='color:green; font-weight:bold;'>".$t['pass_succ']."</p>";
    } else {
        $pass_msg = "<p style='color:red; font-weight:bold;'>".$t['pass_err']."</p>";
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $t['title']; ?></title>
    <style>
        :root { --primary: #1a2a6c; --accent: #27ae60; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: var(--bg); display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        .nav { background: var(--primary); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 14px; transition: 0.3s; }
        .nav a:hover { color: #00ffcc; }
        .wrapper { display: flex; flex: 1; overflow: hidden; }
        .sidebar { width: 260px; background: #2c3e50; color: white; padding-top: 20px; flex-shrink: 0; display: flex; flex-direction: column; }
        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid #34495e; margin-bottom: 10px; }
        .user-avatar { width: 65px; height: 65px; background: var(--accent); border-radius: 50%; margin: auto; display: flex; align-items: center; justify-content: center; font-size: 26px; color: white; border: 3px solid #34495e; }
        .sidebar a { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; cursor: pointer; position: relative; }
        .sidebar a:hover, .sidebar a.active { background: var(--accent); color: white; padding-left: 35px; }
        
        .notif-badge { background: #e74c3c; color: white; padding: 2px 7px; border-radius: 50%; font-size: 11px; position: absolute; right: 15px; top: 18px; font-weight: bold; }

        .content { flex: 1; display: flex; flex-direction: column; position: relative; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); max-width: 900px; margin: 20px auto; overflow-y: auto; flex: 1; }
        .info-box { background: #e8f4fd; padding: 25px; border-left: 6px solid #3498db; margin-top: 25px; border-radius: 4px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #34495e; }
        input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background: var(--accent); color: white; padding: 14px; border: none; border-radius: 6px; width: 100%; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s; }
        
        /* Appeals Table Style */
        .appeal-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .appeal-table th, .appeal-table td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .btn-act { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }

        #viewFrame { width: 100%; height: 100%; border: none; display: none; }
        .content-inner { flex: 1; overflow-y: auto; padding: 20px; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 20px; border-radius: 10px; width: 400px; text-align: center; position: relative; }
        .close { position: absolute; right: 20px; top: 10px; font-size: 24px; cursor: pointer; }
    </style>
</head>
<body>

<div id="studentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalBody"></div>
    </div>
</div>

<div class="nav">
    <div style="font-weight: bold; font-size: 20px; letter-spacing: 1px;">WDU - <span style="color: #00ffcc;"><?php echo strtoupper($office); ?></span></div>
    <div>
        <a href="?lang=<?php echo $lang=='en'?'am':'en'; ?>&page=<?php echo $page; ?>">🌍 <?php echo $lang=='en'?'አማርኛ':'English'; ?></a>
        <a href="?page=dashboard"><?php echo $t['home']; ?></a>
        <a href="?page=about"><?php echo $t['about']; ?></a>
        <a href="?page=contact"><?php echo $t['contact']; ?></a>
        <a href="logout.php" style="color: #ff7675; font-weight: bold;"><?php echo $t['logout']; ?></a>
    </div>
</div>

<div class="wrapper">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="user-avatar"><?php echo strtoupper(substr($full_name, 0, 1)); ?></div>
            <p style="margin: 15px 0 5px; font-size: 18px; font-weight: bold;"><?php echo $full_name; ?></p>
            <small style="color: #27ae60; letter-spacing: 1px; font-weight: bold;"><?php echo strtoupper($office); ?> OFFICE</small>
        </div>
        
        <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active':''; ?>"><?php echo $t['dash']; ?></a>
        
        <a onclick="showRequests()">
            <?php echo $t['req']; ?>
            <?php if($pending_count > 0): ?>
                <span class="notif-badge"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>

        <a href="?page=appeals" class="<?php echo $page=='appeals'?'active':''; ?>">
            <?php echo $t['appeals']; ?>
            <?php if($appeal_count > 0): ?>
                <span class="notif-badge" style="background:#f39c12;"><?php echo $appeal_count; ?></span>
            <?php endif; ?>
        </a>

        <a href="?page=report" class="<?php echo $page=='report'?'active':''; ?>"><?php echo $t['gen_rep']; ?></a>
        
        <a href="?page=password" class="<?php echo $page=='password'?'active':''; ?>"><?php echo $t['pass']; ?></a>
        <a href="logout.php"><?php echo $t['exit']; ?></a>
    </div>

    <div class="content">
        <iframe id="viewFrame" src="officeviw.php"></iframe>

        <div id="mainContent" class="content-inner">
            <div class="card">
                <?php if($page == 'dashboard'): ?>
                    <h1 style="color: var(--primary); margin-top: 0;"><?php echo $t['welcome']; ?>, <?php echo $full_name; ?>!</h1>
                    <div class="info-box">
                        <h3 style="margin-top: 0; color: #2980b9;"><?php echo $t['office_msg']; ?></h3>
                        <p style="font-size: 20px; margin-bottom: 0;"><strong><?php echo strtoupper($office); ?> SECTION</strong></p>
                    </div>
                    <div style="margin-top: 40px; display: flex; gap: 20px;">
                        <div style="flex:1; padding: 20px; background: #f9f9f9; border-radius: 10px; border: 1px solid #eee;">
                            <h4>Pending Requests</h4>
                            <p style="font-size: 30px; font-weight: bold; color: #e74c3c;"><?php echo $pending_count; ?></p>
                        </div>
                    </div>

                <?php elseif($page == 'appeals'): ?>
                    <h2><?php echo $t['appeals']; ?></h2>
                    <hr>
                    <?php
                    $appeals = mysqli_query($conn, "SELECT * FROM appeals WHERE office = '$office' AND status = 'pending'");
                    if(mysqli_num_rows($appeals) > 0): ?>
                    <table class="appeal-table">
                        <tr style="background:#f4f7f6;">
                            <th>Student ID</th>
                            <th>Reason</th>
                            <th>Your Reply</th>
                            <th>Action</th>
                        </tr>
                        <?php while($ap = mysqli_fetch_assoc($appeals)): ?>
                        <tr>
                            <form action="handle_appeal.php" method="POST">
                                <td><?php echo $ap['id_no']; ?></td>
                                <td><?php echo $ap['reason']; ?></td>
                                <td><textarea name="reply_msg" style="width:100%; border-radius:4px; border:1px solid #ddd;"></textarea></td>
                                <td>
                                    <input type="hidden" name="id_no" value="<?php echo $ap['id_no']; ?>">
                                    <button name="action" value="approve" class="btn-act" style="background:#27ae60;">Approve</button>
                                    <button name="action" value="reject" class="btn-act" style="background:#e74c3c;">Reject</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                    <?php else: ?>
                        <p>No student appeals found.</p>
                    <?php endif; ?>

                <?php elseif($page == 'report'): ?>
                    <h2><?php echo $t['gen_rep']; ?></h2>
                    <hr>
                    <button onclick="window.print()" class="btn-submit" style="width: 200px; background: var(--primary);">🖨️ Print Report</button>

                <?php elseif($page == 'about'): ?>
                    <h2><?php echo $t['about']; ?></h2>
                    <hr>
                    <p><?php echo $t['abt_p']; ?></p>

                <?php elseif($page == 'contact'): ?>
                    <h2><?php echo $t['contact']; ?></h2>
                    <hr>
                    <p>📧 Email: support@wdu.edu.et</p>
                    <p>📞 Phone: <?php echo $t['con_p']; ?></p>

                <?php elseif($page == 'password'): ?>
                    <h2>Update Password</h2>
                    <form method="POST" style="max-width: 450px;">
                        <?php echo $pass_msg; ?>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="old_p" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_p" required>
                        </div>
                        <button name="update_pass" class="btn-submit">Update Password Now</button>
                    </form>
                <?php endif; ?>
                
                <div class="footer-text"><center>&copy; <?php echo date("Y"); ?> WDU Clearance</center></div>
            </div>
        </div>
    </div>
</div>

<script>
    function showRequests() {
        document.getElementById('mainContent').style.display = 'none';
        document.getElementById('viewFrame').style.display = 'block';
        document.querySelectorAll('.sidebar a').forEach(el => el.classList.remove('active'));
    }

    function closeModal() {
        document.getElementById('studentModal').style.display = 'none';
    }
</script>

</body>
</html>