<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Assumes the office name is stored in the teacher's session (e.g., 'library', 'registrar')
$teacher_office = $_SESSION['office']; 
$msg = "";

if (isset($_POST['update_status'])) {
    $appeal_id = $_POST['appeal_id'];
    $status = $_POST['status'];
    $reason = mysqli_real_escape_string($conn, $_POST['admin_response']);

    $sql = "UPDATE appeals SET status='$status', admin_response='$reason' WHERE id='$appeal_id'";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert success'>✅ Appeal response submitted successfully.</div>";
    } else {
        $msg = "<div class='alert danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appeal Response Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 12px; max-width: 1000px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #1a2a6c; border-bottom: 2px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background-color: #f8f9fa; }
        .Pending { color: orange; font-weight: bold; }
        .Approved { color: green; font-weight: bold; }
        .Rejected { color: red; font-weight: bold; }
        textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        .update-btn { background: #1a2a6c; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px; margin-top: 5px; font-weight: bold; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .success { background: #d4edda; color: #155724; }
        .danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="box">
    <h2>📩 Appeal List (Office: <?php echo strtoupper(str_replace('_', ' ', $teacher_office)); ?>)</h2>
    <?php echo $msg; ?>

    <table>
        <thead>
            <tr>
                <th>Student Information</th>
                <th>Subject & Message</th>
                <th>Current Status</th>
                <th>Action & Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM appeals WHERE office_name = '$teacher_office' ORDER BY id DESC";
            $res = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                            <td>
                                <b>" . htmlspecialchars($row['full_name']) . "</b><br>
                                <small>ID: " . htmlspecialchars($row['id_no']) . "</small>
                            </td>
                            <td>
                                <b>" . htmlspecialchars($row['subject']) . "</b><br>
                                " . htmlspecialchars($row['message']) . "
                            </td>
                            <td class='{$row['status']}'>{$row['status']}</td>
                            <td>
                                <form method='POST'>
                                    <input type='hidden' name='appeal_id' value='{$row['id']}'>
                                    <select name='status' required>
                                        <option value='Approved' " . ($row['status'] == 'Approved' ? 'selected' : '') . ">Approve</option>
                                        <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Reject</option>
                                    </select><br>
                                    <textarea name='admin_response' placeholder='Write reason for rejection or comments here...' required>" . htmlspecialchars($row['admin_response']) . "</textarea><br>
                                    <button type='submit' name='update_status' class='update-btn'>Submit Response</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No pending appeals for this office.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>