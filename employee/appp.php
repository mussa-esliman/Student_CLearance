<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $column = $_POST['column_name']; 
    $status = $_POST['status'];      
    mysqli_query($conn, "UPDATE clearance_form SET $column = '$status' WHERE id = '$id'");
}

$result = null; 
$is_searching = false;

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $is_searching = true;
    $search_text = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM clearance_form WHERE full_name LIKE '%$search_text%' OR id_no LIKE '%$search_text%'";
    $result = mysqli_query($conn, $query);
}

function is_previous_step_done($row, $current_col) {
    $order = [
        'advisor_status', 
        'department_head', 
        'school_dean', 
        'library', 
        'student_cafeteria', 
        'student_procter', 
        'dean_of_student', 
        'campus_security', 
        'registrar'
    ];
    
    $index = array_search($current_col, $order);
    if ($index === 0) return true;
    
    $prev_col = $order[$index - 1];
    return (isset($row[$prev_col]) && $row[$prev_col] == 'approved');
}

// New helper function to check if rejection is allowed for Ayseram Esu
function can_reject_ayseram_esu($row, $current_col) {
    // Change these values if the exact name or ID is different in your database
    $target_name = "Ayseram Esu";  
    $target_id   = ""; // leave empty if you match only by name, or fill ID if needed
    
    if (!empty($target_id)) {
        return ($row['full_name'] === $target_name || $row['id_no'] === $target_id);
    }
    return ($row['full_name'] === $target_name);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>WDU Approval Panel</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; }
        .search-container { margin-bottom: 30px; text-align: center; }
        .search-input { padding: 12px; width: 350px; border: 2px solid #1a2a6c; border-radius: 5px; }
        .search-btn { padding: 12px 25px; background: #1a2a6c; color: white; border: none; cursor: pointer; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #eee; padding: 12px; }
        th { background-color: #1a2a6c; color: white; }
        
        .status-badge { padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: bold; }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #d4edda; color: #155724; }
        .rejected { background: #f8d7da; color: #721c24; }
        .locked { background: #e9ecef; color: #adb5bd; cursor: not-allowed; opacity: 0.6; }

        .btn { border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-approve { background: #27ae60; }
        .btn-reject { background: #e74c3c; }
        .btn-disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Woldia University Clearance Management</h2>

    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" class="search-input" placeholder="Search by ID or Name..." required>
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>

    <?php if ($is_searching && $result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <table>
                <tr>
                    <th width="30%">Student Info</th>
                    <th>Approval Workflow (Step-by-Step)</th>
                </tr>
                <tr>
                    <td style="vertical-align: top;">
                        <strong>Name:</strong> <?php echo $row['full_name']; ?><br>
                        <strong>ID:</strong> <?php echo $row['id_no']; ?>
                    </td>
                    <td style="padding: 0;">
                        <table style="width:100%; border:none;">
                            <?php 
                            $offices = [
                                'advisor_status' => '1. Academic Advisor', 
                                'department_head' => '2. Department Head', 
                                'school_dean' => '3. College/School Dean', 
                                'library' => '4. Library', 
                                'student_cafeteria' => '5. Cafeteria', 
                                'student_procter' => '6. Proctor', 
                                'dean_of_student' => '7. Dean of Students', 
                                'campus_security' => '8. Security', 
                                'registrar' => '9. Registrar (Final)'
                            ];

                            foreach ($offices as $col => $office_name): 
                                $status = (!empty($row[$col])) ? $row[$col] : 'pending';
                                $can_approve = is_previous_step_done($row, $col);
                                $can_reject_special = can_reject_ayseram_esu($row, $col);
                            ?>
                            <tr style="background: <?php echo $can_approve ? '#fff' : '#f9f9f9'; ?>;">
                                <td style="border:none;">
                                    <?php echo $office_name; ?>
                                    <span class="status-badge <?php echo $status; ?>"><?php echo $status; ?></span>
                                    <?php if (!$can_approve && !$can_reject_special): ?>
                                        <br><small style="color:red;">⚠️ Waiting for previous step</small>
                                    <?php endif; ?>
                                </td>
                                <td style="border:none; text-align:right;">
                                    <?php if ($status == 'pending'): ?>
                                        <?php if ($can_approve || $can_reject_special): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="column_name" value="<?php echo $col; ?>">
                                                <input type="hidden" name="status" id="st_<?php echo $row['id'].$col; ?>" value="">
                                                <?php if ($can_approve): ?>
                                                    <button type="submit" name="update_status" class="btn btn-approve" onclick="document.getElementById('st_<?php echo $row['id'].$col; ?>').value='approved'">Approve</button>
                                                <?php endif; ?>
                                                <button type="submit" name="update_status" class="btn btn-reject" onclick="document.getElementById('st_<?php echo $row['id'].$col; ?>').value='rejected'">Deny</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-disabled" title="Previous step not completed">Locked</button>
                                        <?php endif; ?>
                                    <?php elseif ($status != 'pending'): ?>
                                        <span class="status-badge <?php echo $status; ?>"><?php echo $status; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>
            </table>
            <br><br>
        <?php endwhile; ?>
    <?php elseif ($is_searching): ?>
        <p style="text-align:center; color:red;">No student found.</p>
    <?php else: ?>
        <p style="text-align:center; color:#666;">Enter Student ID to begin clearance approval.</p>
    <?php endif; ?>
</div>

</body>
</html>