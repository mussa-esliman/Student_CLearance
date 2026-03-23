
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!isset($_SESSION['office'])) {
    header("Location: login.php");
    exit();
}

$my_office = $_SESSION['office'];
$my_dept = $_SESSION['user_dept'] ?? '';

$central_offices = ['registrar', 'library', 'student_cafeteria', 'campus_security', 'dean_of_student', 'student_procter'];

if (in_array($my_office, $central_offices)) {
    $sql = "SELECT * FROM clearance_form ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM clearance_form WHERE department = '$my_dept' ORDER BY id DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a2a6c; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #eee; text-align: left; }
        th { background: #1a2a6c; color: white; }
        tr:hover { background: #f9f9f9; }
        .btn-view { background: #27ae60; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .badge { padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<div class="card">
    <h2>Student Clearance Requests - <?php echo strtoupper($my_office); ?> Office</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID No</th>
                <th>Full Name</th>
                <th>Department</th>
                <th>Reason</th>
                <th>Date Attended</th>
                <th>Your Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $status = strtolower($row[$my_office] ?? 'pending');
            ?>
            <tr>
                <td><b><?php echo $row['id_no']; ?></b></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td><small><?php echo $row['reason_for_clearance']; ?></small></td>
                <td><?php echo $row['last_date_attended']; ?></td>
                <td>
                    <span class="badge <?php echo $status; ?>">
                        <?php echo strtoupper($status); ?>
                    </span>
                </td>
                <td>
                    <a href="appp.php?search=<?php echo $row['id_no']; ?>" class="btn-view">Process</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>