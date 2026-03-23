<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher_users') {
    header("Location: login.php"); exit();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'title' => 'WDU CLEARANCE SYSTEM', 'home' => 'Home', 'about' => 'About Us', 'contact' => 'Contact Us', 'logout' => 'Logout',
        'dash' => '📊 Dashboard', 'req' => '📩 View Requests', 'pass' => '🔑 Change Password', 'exit' => '🚪 Logout',
        'dash_title' => 'Dashboard Overview', 'welcome' => 'Welcome back', 'dash_msg' => 'You are currently managing clearance requests for the',
        'req_title' => 'Student Clearance Requests', 'search_place' => 'Search by ID or Name...', 'search_btn' => 'Search',
        'th_name' => 'Student Name', 'th_id' => 'ID Number', 'th_dept' => 'Department', 'th_status' => 'Status', 'th_action' => 'Action',
        'approve_btn' => 'Approve Now', 'processed' => '✅ Processed', 'confirm' => 'Are you sure?', 'no_data' => 'No students found.',
        'sec_title' => 'Security Settings', 'sec_msg' => 'Change your account password below.',
        'old_p' => 'Old Password', 'new_p' => 'New Password', 'upd_btn' => 'Update Password', 'pending' => 'Pending', 'approved' => 'Approved',
        'abt_h' => 'About WDU Clearance', 'abt_p' => 'Woldia University Student Clearance System is designed to modernize the traditional paper-based process into an efficient digital workflow.',
        'con_h' => 'Support & Contact', 'con_p' => 'If you face any technical issues, please contact the ICT department.',
        'phone' => 'Phone: +251 333 11 00', 'email' => 'Email: support@wdu.edu.et',
        'pass_err' => 'Old password is incorrect!', 'pass_succ' => 'Password updated successfully!',
        // Home extra
        'total_req' => 'Total Requests', 'pending_req' => 'Pending Approval', 'approved_req' => 'Approved Students',
        'instr_h' => 'Instructions', 'instr_1' => 'Check new requests in the "View Requests" tab.', 'instr_2' => 'Verify student data before clicking "Approve".', 'instr_3' => 'Keep your password secret and update it regularly.'
    ],
    'am' => [
        'title' => 'የወልድያ ዩኒቨርሲቲ ክሊራንስ', 'home' => 'ዋና ገጽ', 'about' => 'ስለ እኛ', 'contact' => 'ያግኙን', 'logout' => 'ውጣ',
        'dash' => '📊 ዳሽቦርድ', 'req' => '📩 ጥያቄዎችን እይ', 'pass' => '🔑 የይለፍ ቃል ቀይር', 'exit' => '🚪 ውጣ',
        'dash_title' => 'ዳሽቦርድ አጠቃላይ እይታ', 'welcome' => 'እንኳን ደህና መጡ', 'dash_msg' => 'እያስተዳደሩ ያሉት የክሊራንስ ጥያቄ ክፍል፡',
        'req_title' => 'የተማሪዎች ክሊራንስ ጥያቄዎች', 'search_place' => 'በመለያ ቁጥር ወይም በስም ይፈልጉ...', 'search_btn' => 'ፈልግ',
        'th_name' => 'የተማሪ ስም', 'th_id' => 'መለያ ቁጥር', 'th_dept' => 'ትምህርት ክፍል', 'th_status' => 'ሁኔታ', 'th_action' => 'ድርጊት',
        'approve_btn' => 'አሁን አጽድቅ', 'processed' => '✅ ጸድቋል', 'confirm' => 'እርግጠኛ ነዎት?', 'no_data' => 'ምንም ተማሪ አልተገኘም።',
        'sec_title' => 'የደህንነት ቅንጅቶች', 'sec_msg' => 'የመለያዎን የይለፍ ቃል ከታች ይቀይሩ።',
        'old_p' => 'የድሮ የይለፍ ቃል', 'new_p' => 'አዲስ የይለፍ ቃል', 'upd_btn' => 'የይለፍ ቃል አድስ', 'pending' => 'በጥበቃ ላይ', 'approved' => 'የጸደቀ',
        'abt_h' => 'ስለ ወልድያ ዩኒቨርሲቲ ክሊራንስ', 'abt_p' => 'የወልድያ ዩኒቨርሲቲ የተማሪዎች ክሊራንስ ሲስተም የተለመደውን የወረቀት ስራ ወደ ዲጂታል አሰራር በመቀየር ቅልጥፍናን ለመጨመር የተሰራ ነው።',
        'con_h' => 'እርዳታ እና ግንኙነት', 'con_p' => 'ማንኛውም ቴክኒካዊ ችግር ካጋጠመዎት እባክዎን የአይሲቲ (ICT) ክፍልን ያነጋግሩ።',
        'phone' => 'ስልክ: +251 333 11 00', 'email' => 'ኢሜል: support@wdu.edu.et',
        'pass_err' => 'የድሮው የይለፍ ቃል የተሳሳተ ነው!', 'pass_succ' => 'የይለፍ ቃልዎ በትክክል ተቀይሯል!',
        // Home extra
        'total_req' => 'ጠቅላላ ጥያቄዎች', 'pending_req' => 'በጥበቃ ላይ ያሉ', 'approved_req' => 'የጸደቁ ተማሪዎች',
        'instr_h' => 'መመሪያዎች', 'instr_1' => 'አዳዲስ ጥያቄዎችን "ጥያቄዎችን እይ" በሚለው ስር ያገኛሉ።', 'instr_2' => 'ተማሪውን ከማጽደቅዎ በፊት መረጃውን በትክክል ያረጋግጡ።', 'instr_3' => 'የይለፍ ቃልዎን በሚስጥር ይያዙ፤ በየጊዜውም ይቀይሩ።'
    ]
];

$t = $words[$lang];
$office = $_SESSION['office']; 
$dept = $_SESSION['user_dept'];
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];


$total_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM clearance_form WHERE department='$dept'"))['cnt'];
$pending_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM clearance_form WHERE department='$dept' AND $office='pending'"))['cnt'];
$approved_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM clearance_form WHERE department='$dept' AND $office='approved'"))['cnt'];

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

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE clearance_form SET $office = 'approved' WHERE id = $id AND department = '$dept'");
    header("Location: officeviw.php?page=requests&msg=success"); exit();
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
        :root { --nav-bg: #1a252f; --side-bg: #2c3e50; --accent: #00ffcc; --text: #ecf0f1; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f7f6; display: flex; flex-direction: column; height: 100vh; }
        .top-nav { background: var(--nav-bg); color: white; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; height: 65px; }
        .top-nav .links a { color: white; text-decoration: none; margin-left: 20px; font-size: 14px; }
        .wrapper { display: flex; flex: 1; overflow: hidden; }
        .sidebar { width: 260px; background: var(--side-bg); color: white; padding-top: 20px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu a { color: white; text-decoration: none; display: block; padding: 15px 25px; border-bottom: 0.5px solid #3e4f5f; }
        .sidebar-menu a:hover, .active-link { background: #34495e; border-left: 5px solid var(--accent); color: var(--accent); }
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 25px 0; }
        .stat-card { padding: 20px; border-radius: 8px; color: white; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; border: none; cursor: pointer; }
        .btn-app { background: #27ae60; }
        .lang-switch { background: #34495e; color: var(--accent); padding: 5px 10px; border-radius: 4px; border: 1px solid var(--accent); text-decoration: none; }
        .search-box { padding: 10px; width: 250px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="logo"><strong>WDU</strong> <?php echo $t['title']; ?></div>
    <div class="links">
        <a href="?lang=<?php echo $lang == 'en' ? 'am' : 'en'; ?>&page=<?php echo $page; ?>" class="lang-switch"><?php echo $lang == 'en' ? 'አማርኛ' : 'English'; ?></a>
        <a href="?page=dashboard"><?php echo $t['home']; ?></a>
        <a href="logout.php" style="color:#ff7675;"><?php echo $t['logout']; ?></a>
    </div>
</nav>

<div class="wrapper">
    <aside class="sidebar">
        <div style="text-align:center; padding-bottom: 20px;">
            <div style="width:60px; height:60px; background:var(--accent); border-radius:50%; margin: 20px auto; display:flex; align-items:center; justify-content:center; color:#1a252f; font-weight:bold; font-size:24px;">
                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
            </div>
            <h4><?php echo $full_name; ?></h4>
            <small><?php echo strtoupper($office); ?></small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active-link':''; ?>"><?php echo $t['dash']; ?></a></li>
            <li><a href="?page=requests" class="<?php echo $page=='requests'?'active-link':''; ?>"><?php echo $t['req']; ?></a></li>
            <li><a href="?page=password" class="<?php echo $page=='password'?'active-link':''; ?>"><?php echo $t['pass']; ?></a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="card">
            <?php if($page == 'dashboard'): ?>
                <h2><?php echo $t['dash_title']; ?></h2>
                <p><?php echo $t['welcome']; ?>, <strong><?php echo $full_name; ?></strong>!</p>
                
                <div class="stats-grid">
                    <div class="stat-card" style="background: #3498db;">
                        <h3><?php echo $total_q; ?></h3>
                        <p><?php echo $t['total_req']; ?></p>
                    </div>
                    <div class="stat-card" style="background: #f39c12;">
                        <h3><?php echo $pending_q; ?></h3>
                        <p><?php echo $t['pending_req']; ?></p>
                    </div>
                    <div class="stat-card" style="background: #27ae60;">
                        <h3><?php echo $approved_q; ?></h3>
                        <p><?php echo $t['approved_req']; ?></p>
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                    <h4><?php echo $t['instr_h']; ?></h4>
                    <ul style="line-height: 2;">
                        <li><?php echo $t['instr_1']; ?></li>
                        <li><?php echo $t['instr_2']; ?></li>
                        <li><?php echo $t['instr_3']; ?></li>
                    </ul>
                </div>

            <?php elseif($page == 'requests'): ?>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h2><?php echo $t['req_title']; ?></h2>
                    <form method="POST">
                        <input type="text" name="search_val" class="search-box" placeholder="<?php echo $t['search_place']; ?>">
                        <button name="btn_search" class="btn" style="background:var(--side-bg);"><?php echo $t['search_btn']; ?></button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th><?php echo $t['th_name']; ?></th>
                            <th><?php echo $t['th_id']; ?></th>
                            <th><?php echo $t['th_status']; ?></th>
                            <th><?php echo $t['th_action']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = "SELECT * FROM clearance_form WHERE department='$dept'";
                        if(isset($_POST['btn_search'])){
                            $v = mysqli_real_escape_string($conn, $_POST['search_val']);
                            $q .= " AND (id_no LIKE '%$v%' OR full_name LIKE '%$v%')";
                        }
                        $res = mysqli_query($conn, $q . " ORDER BY id DESC");
                        while($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?php echo $r['full_name']; ?></td>
                            <td><?php echo $r['id_no']; ?></td>
                            <td><?php echo $r[$office]=='approved'?$t['approved']:$t['pending']; ?></td>
                            <td>
                                <?php if($r[$office] == 'pending'): ?>
                                    <a href="?approve=<?php echo $r['id']; ?>" class="btn btn-app" onclick="return confirm('<?php echo $t['confirm']; ?>')"><?php echo $t['approve_btn']; ?></a>
                                <?php else: ?>
                                    <span style="color:green;"><?php echo $t['processed']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            
            <?php elseif($page == 'password'): ?>
                <h2><?php echo $t['sec_title']; ?></h2>
                <div style="max-width: 400px; margin-top: 20px;">
                    <?php echo isset($pass_status)?$pass_status:''; ?>
                    <form method="POST">
                        <label><?php echo $t['old_p']; ?></label>
                        <input type="password" name="old_p" class="search-box" style="width:100%; margin-bottom:15px;" required>
                        <label><?php echo $t['new_p']; ?></label>
                        <input type="password" name="new_p" class="search-box" style="width:100%; margin-bottom:15px;" required>
                        <button name="update_pass" class="btn btn-app" style="width:100%;"><?php echo $t['upd_btn']; ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>