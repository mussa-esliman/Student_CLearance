<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin_users') {
    exit("Access Denied");
}

$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $s = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " WHERE full_name LIKE '%$s%' OR id_no LIKE '%$s%' ";
}

$sql = "SELECT * FROM clearance_form $search_query ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .report-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .search-box { margin-bottom: 20px; text-align: center; }
        .search-box input { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px; }
        
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #eee; padding: 10px; text-align: center; }
        th { background: #1a2a6c; color: white; }
        
        
        .status { padding: 3px 7px; border-radius: 4px; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .approved { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }
        .rejected { background: #f8d7da; color: #721c24; }
        
        @media print { .search-box, .no-print { display: none; } }
    </style>
</head>
<body>

<div class="report-card">
    <h2 style="color: #1a2a6c; text-align: center;">🎓 WDU Clearance - Overall Status Report</h2>
    
    <div class="search-box">
        <form method="GET">
            <input type="text" name="search" placeholder="Search Student Name or ID..." value="<?php echo $_GET['search'] ?? ''; ?>">
            <button type="submit" style="padding: 10px; background: #1a2a6c; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
            <button onclick="window.print()" class="no-print" style="padding: 10px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Print Report</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Dept</th>
                <th>Advisor</th>
                <th>Dept Head</th>
                <th>Dean</th>
                <th>Library</th>
                <th>Cafe</th>
                <th>Proctor</th>
                <th>Student Dean</th>
                <th>Security</th>
                <th>Registrar</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><b><?php echo $row['id_no']; ?></b></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['department']; ?></td>
                
                <?php 
                $offices = ['advisor_status', 'department_head', 'school_dean', 'library', 'student_cafeteria', 'student_procter', 'dean_of_student', 'campus_security', 'registrar'];
                foreach($offices as $off): 
                    $stat = strtolower($row[$off] ?? 'pending');
                ?>
                    <td><span class="status <?php echo $stat; ?>"><?php echo $stat; ?></span></td>
                <?php endforeach; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>