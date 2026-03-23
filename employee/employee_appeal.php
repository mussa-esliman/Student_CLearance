<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: login.php");
    exit();
}

$employee_office = $_SESSION['office']; 
$msg = "";

// 2. Process the Decision
if (isset($_POST['update_appeal'])) {
    $appeal_id = mysqli_real_escape_string($conn, $_POST['appeal_id']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $admin_response = mysqli_real_escape_string($conn, $_POST['admin_response']);

    mysqli_begin_transaction($conn);
    try {
        // Update the appeal with the status and the REASON
        $update_appeal = "UPDATE appeals SET status = '$status', admin_response = '$admin_response' 
                          WHERE id = '$appeal_id' AND office_name = '$employee_office'";
        mysqli_query($conn, $update_appeal);

        // If status is Approved, update the main clearance table automatically
        if ($status == 'Approved') {
            $update_clearance = "UPDATE clearance_form SET $employee_office = 'Cleared' 
                                 WHERE id_no = '$student_id'";
            mysqli_query($conn, $update_clearance);
        }

        mysqli_commit($conn);
        $msg = "<div class='alert success'>✅ Decision submitted. Student notified of your reason.</div>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $msg = "<div class='alert danger'>Error: System could not update record.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .box { background: white; padding: 30px; border-radius: 12px; max-width: 1000px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .office-badge { background: #1a2a6c; color: white; padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        
        /* Active Appeal Card */
        .appeal-card { background: #fff; border: 1px solid #ddd; border-left: 5px solid #1a2a6c; padding: 20px; border-radius: 8px; margin-bottom: 40px; }
        .message-box { background: #f9f9f9; padding: 15px; border: 1px dashed #ccc; border-radius: 6px; margin: 10px 0; color: #444; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-top: 15px; }
        select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;}
        textarea { height: 100px; }
        
        .btn-submit { background: #1a2a6c; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; grid-column: span 2; }
        
        /* History Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8f9fa; color: #555; text-transform: uppercase; font-size: 11px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .Approved { background: #d4edda; color: #155724; }
        .Rejected { background: #f8d7da; color: #721c24; }
        .alert { padding: 15px; border-radius: 6px; text-align: center; margin-bottom: 20px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<div class="box">
    <div class="header-flex">
        <h2>Process Pending Appeals</h2>
        <span class="office-badge">Your Office: <?php echo strtoupper(str_replace('_', ' ', $employee_office)); ?></span>
    </div>

    <?php echo $msg; ?>

    <?php
    $res = mysqli_query($conn, "SELECT * FROM appeals WHERE office_name = '$employee_office' AND status = 'Pending' ORDER BY id ASC LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
    ?>
        <div class="appeal-card">
            <p><strong>Student:</strong> <?php echo $row['full_name']; ?> (<?php echo $row['id_no']; ?>)</p>
            <h3>Subject: <?php echo $row['subject']; ?></h3>
            <div class="message-box">"<?php echo $row['message']; ?>"</div>

            <form method="POST">
                <input type="hidden" name="appeal_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="student_id" value="<?php echo $row['id_no']; ?>">
                <div class="form-grid">
                    <div>
                        <label>Decision</label>
                        <select name="status" required>
                            <option value="Approved">Approve Appeal</option>
                            <option value="Rejected">Reject Appeal</option>
                        </select>
                    </div>
                    <div>
                        <label>Reason for Decision (Sent to Student)</label>
                        <textarea name="admin_response" placeholder="Explain your decision..." required></textarea>
                    </div>
                    <button type="submit" name="update_appeal" class="btn-submit">Submit Decision & Load Next</button>
                </div>
            </form>
        </div>
    <?php } else { ?>
        <p style="text-align:center; color:#888; padding: 30px; border: 2px dashed #eee; border-radius: 8px;">🎉 No pending appeals for your office right now.</p>
    <?php } ?>

    <h3 style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 20px;">Your Decision History</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Reason Sent to Student</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $history = mysqli_query($conn, "SELECT * FROM appeals WHERE office_name = '$employee_office' AND status != 'Pending' ORDER BY id DESC");
            while ($h_row = mysqli_fetch_assoc($history)) {
                echo "<tr>
                        <td><strong>{$h_row['id_no']}</strong></td>
                        <td>{$h_row['subject']}</td>
                        <td><span class='badge {$h_row['status']}'>{$h_row['status']}</span></td>
                        <td style='color:#666;'><em>" . htmlspecialchars($h_row['admin_response']) . "</em></td>
                      </tr>";
            }
            if(mysqli_num_rows($history) == 0) {
                echo "<tr><td colspan='4' style='text-align:center;'>No decisions recorded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>