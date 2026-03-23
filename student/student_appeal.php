<?php
session_start();
// 1. Database Connection
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

// 2. Security Check: Only allow logged-in students
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student_users') {
    header("Location: login.php");
    exit();
}

// 3. Define the current student's ID from session to use as a filter
// Note: Using $_SESSION['user_id'] as the owner identifier
$logged_in_student = $_SESSION['user_id']; 

$msg = "";

// 4. Handle Appeal Submission
if (isset($_POST['send_appeal'])) {
    // We force the ID to be the logged-in user's ID for security
    $id_no = mysqli_real_escape_string($conn, $logged_in_student);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $office = mysqli_real_escape_string($conn, $_POST['office']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO appeals (id_no, full_name, office_name, status, subject, message) 
              VALUES ('$id_no', '$full_name', '$office', 'Pending', '$subject', '$message')";

    if (mysqli_query($conn, $query)) {
        $msg = "<div class='alert success'>✅ Appeal submitted successfully.</div>";
    } else {
        $msg = "<div class='alert danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// 5. Secure Delete: Only delete if the appeal belongs to the logged-in student
if (isset($_GET['delete_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // The WHERE clause ensures Student A cannot delete Student B's appeal
    $delete_query = "DELETE FROM appeals WHERE id='$del_id' AND id_no='$logged_in_student'";
    
    if (mysqli_query($conn, $delete_query)) {
        header("Location: student_appeal.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appeals | WDU Clearance</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .box { background: white; padding: 30px; border-radius: 12px; max-width: 950px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #1a2a6c; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        .btn { background: #1a2a6c; color: white; border: none; padding: 14px; width: 100%; cursor: pointer; font-weight: bold; border-radius: 6px; margin-top: 15px; font-size: 16px; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #d4edda; color: #155724; }
        .danger { background: #f8d7da; color: #721c24; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        th, td { border-bottom: 1px solid #eee; padding: 15px; text-align: left; }
        th { background: #f8f9fa; color: #555; text-transform: uppercase; font-size: 12px; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .Pending { background: #fff3cd; color: #856404; }
        .Approved { background: #d4edda; color: #155724; }
        .Rejected { background: #f8d7da; color: #721c24; }
        .del-btn { color: #d9534f; text-decoration: none; font-size: 13px; font-weight: bold; }
        .user-info { float: right; font-size: 12px; color: #777; font-weight: normal; }
    </style>
</head>
<body>

<div class="box">
    <h2>📩 Student Clearance Appeal <span class="user-info">Logged in as: <?php echo $logged_in_student; ?></span></h2>
    
    <?php echo $msg; ?>
    
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_no" value="<?php echo $logged_in_student; ?>" readonly>
            </div>
        </div>

        <label>Select Office to Appeal</label>
        <select name="office" required>
            <option value="" disabled selected>-- Choose the Department --</option>
            <option value="advisor_status">Academic Advisor</option>
            <option value="department_head">Department Head</option>
            <option value="school_dean">School Dean</option>
            <option value="library">Library</option>
            <option value="student_cafeteria">Student Cafeteria</option>
            <option value="student_procter">Student Proctor</option>
            <option value="dean_of_student">Dean of Students</option>
            <option value="campus_security">Campus Security</option>
            <option value="registrar">Registrar</option>
        </select>
        
        <label style="margin-top:10px;">Subject</label>
        <input type="text" name="subject" placeholder="Title of your issue" required>

        <label style="margin-top:10px;">Message / Reason</label>
        <textarea name="message" placeholder="Explain why you are appealing..." required></textarea>

        <button type="submit" name="send_appeal" class="btn">Send Appeal Request</button>
    </form>

    <h3 style="margin-top: 40px;">My Appeal History</h3>
    <table>
        <thead>
            <tr>
                <th>Office</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
          
            $res = mysqli_query($conn, "SELECT * FROM appeals WHERE id_no = '$logged_in_student' ORDER BY id DESC");
            
            while($row = mysqli_fetch_assoc($res)) {
                $office_fmt = strtoupper(str_replace('_', ' ', $row['office_name']));
                echo "<tr>
                        <td>$office_fmt</td>
                        <td>{$row['subject']}</td>
                        <td><span class='status-badge {$row['status']}'>{$row['status']}</span></td>
                        <td>
                            <a href='?delete_id={$row['id']}' class='del-btn' onclick='return confirm(\"Delete this appeal?\")'>Delete</a>
                        </td>
                      </tr>";
            }
            if(mysqli_num_rows($res) == 0) {
                echo "<tr><td colspan='4' style='text-align:center; color:#999;'>You have no submitted appeals.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>