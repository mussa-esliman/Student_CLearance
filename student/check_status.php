<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

$student_data = null;

if (isset($_POST['search'])) {
    $id_no = mysqli_real_escape_string($conn, $_POST['id_no']);
    $sql = "SELECT * FROM clearance_form WHERE id_no = '$id_no'";
    $result = mysqli_query($conn, $sql);
    $student_data = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WDU | Clearance Status Tracker</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { background: white; max-width: 900px; margin: auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #1a2a6c; text-align: center; }
        .search-box { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; }
        input { padding: 12px; width: 60%; border: 1px solid #ccc; border-radius: 6px; }
        button { padding: 12px 25px; background: #1a2a6c; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #eee; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; text-transform: uppercase; font-size: 13px; }
        
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #d4edda; color: #155724; }
        
        .approver-name { color: #555; font-size: 13px; font-weight: 600; }
        .approve-date { color: #888; font-size: 12px; display: block; }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Clearance Status Tracker</h2>
    
    <form method="POST" class="search-box">
        <input type="text" name="id_no" placeholder="Enter ID (e.g. WDU160963)" required>
        <button type="submit" name="search">Check Status</button>
    </form>

    <?php if ($student_data): ?>
        <div style="background: #eef2ff; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin:0;">Student: <?php echo $student_data['full_name']; ?></h3>
            <p style="margin:5px 0 0 0; color:#555;">ID Number: <b><?php echo $student_data['id_no']; ?></b></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Department / Office</th>
                    <th>Status</th>
                    <th>Approved By</th>
                    <th>Date Actioned</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Array to handle all departments dynamically
                $departments = [
                    "Academic Advisor" => "advisor",
                    "Department Head" => "department_head",
                    "College/School Dean" => "school_dean",
                    "Library" => "library",
                    "Student Cafeteria" => "student_cafeteria",
                    "Student Proctor" => "student_procter",
                    "Dean of Students" => "dean_of_student",
                    "Campus Security" => "campus_security",
                    "Registrar Office" => "registrar"
                ];

                foreach ($departments as $label => $col_prefix) {
                    $status = $student_data[$col_prefix . '_status'];
                    $name = $student_data[$col_prefix . '_name'] ?? "Not yet assigned";
                    $date = $student_data[$col_prefix . '_date'] ?? "N/A";
                    $css_class = ($status == 'approved') ? 'approved' : 'pending';
                    
                    echo "<tr>
                            <td><strong>$label</strong></td>
                            <td><span class='status-badge $css_class'>$status</span></td>
                            <td><span class='approver-name'>$name</span></td>
                            <td><span class='approve-date'>$date</span></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

    <?php elseif (isset($_POST['search'])): ?>
        <p style="text-align:center; color:red; font-weight:bold; padding:20px; background:#fff5f5; border-radius:8px;">
            ❌ ID Number not found in the clearance records.
        </p>
    <?php endif; ?>
</div>

</body>
</html>