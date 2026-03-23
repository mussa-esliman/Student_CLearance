<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Database Connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_clearance";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("<div class='sys-err'>Database Connection Critical Failure: " . mysqli_connect_error() . "</div>");
}

$msg = "";
$status_type = "";

// 2. Form Processing Logic
if (isset($_POST['submit'])) {
    // Collect and Sanitize
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $id_no     = mysqli_real_escape_string($conn, strtoupper(trim($_POST['id_no'])));
    $college   = mysqli_real_escape_string($conn, $_POST['college_institute']);
    $dept      = mysqli_real_escape_string($conn, trim($_POST['department']));
    $program   = mysqli_real_escape_string($conn, $_POST['program']);
    $reason    = mysqli_real_escape_string($conn, $_POST['reason_for_clearance']);
    $year      = mysqli_real_escape_string($conn, trim($_POST['academic_year']));
    $semester  = mysqli_real_escape_string($conn, $_POST['semester']);
    $date_att  = mysqli_real_escape_string($conn, $_POST['last_date_attended']);

    // A. STRICT FULFILLMENT CHECK (Ensures no field is empty)
    if (empty($full_name) || empty($id_no) || empty($college) || empty($dept) || 
        empty($program) || empty($reason) || empty($year) || empty($semester) || empty($date_att)) {
        
        $msg = "❌ Error: All fields are mandatory. Please fill in every section.";
        $status_type = "err";
        
    } else {
        // B. FORMAT VALIDATION
        $name_pattern = "/^[A-Z][a-z]+\s[A-Z][a-z]/"; 
      $id_pattern = "/^WDU[0-9]{4}$/"; 

if (empty($id_no)) {
    $msg = "❌ Error: ID Number is required.";
    $status_type = "err";
} elseif (!preg_match($id_pattern, $id_no)) {
    $msg = "❌ Error: ID must start with 'WDU' and be followed by 4 digits (e.g., WDU1234).";
    $status_type = "err";
}
 else {
            // C. DATABASE MEMBERSHIP & SECURITY CHECKS
            $dept_check = mysqli_query($conn, "SELECT id FROM teacher_users WHERE department = '$dept'");
            $user_check = mysqli_query($conn, "SELECT id FROM student_users WHERE id_no = '$id_no'");
            $dup_check  = mysqli_query($conn, "SELECT id FROM clearance_form WHERE id_no = '$id_no'");

            
            } if (mysqli_num_rows($dept_check) == 0) {
                $msg = "❌ Error: Department '$dept' is not valid or has no registered staff.";
                $status_type = "err";
            } elseif (mysqli_num_rows($dup_check) > 0) {
                $msg = "⚠️ Notice: A clearance request already exists for this ID.";
                $status_type = "err";
            } else {
                // D. INSERT DATA
                $query = "INSERT INTO clearance_form 
                    (full_name, id_no, collage_institute, department, program, reason_for_clearance, academic_year, semester, last_date_attended, status) 
                    VALUES ('$full_name', '$id_no', '$college', '$dept', '$program', '$reason', '$year', '$semester', '$date_att', 'Pending')";

                if (mysqli_query($conn, $query)) {
                    $msg = "✅ Success: All fields verified. Request submitted!";
                    $status_type = "succ";
                } else {
                    $msg = "❌ Database Error: " . mysqli_error($conn);
                    $status_type = "err";
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WDU | Clearance Submission</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e40af; --bg: #f8fafc; --white: #ffffff; --error: #dc2626; --success: #059669; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); padding: 40px 20px; margin:0; }
        .main-wrapper { max-width: 900px; margin: 0 auto; background: var(--white); border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .form-header { background: var(--primary); color: white; padding: 30px; text-align: center; }
        .content-body { padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .input-field { display: flex; flex-direction: column; }
        .input-field label { font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #475569; }
        .input-field input, .input-field select { padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; outline: none; transition: border 0.3s; }
        .input-field input:focus { border-color: var(--primary); }
        .alert-box { padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-weight: 600; }
        .err { background: #fee2e2; color: var(--error); border: 1px solid #fecaca; }
        .succ { background: #dcfce7; color: var(--success); border: 1px solid #bbf7d0; }
        .section-divider { grid-column: span 2; border-bottom: 2px solid #f1f5f9; color: var(--primary); font-weight: 800; text-transform: uppercase; font-size: 13px; padding-bottom: 5px; margin-top: 15px; }
        .btn-large { grid-column: span 2; background: var(--primary); color: white; padding: 18px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 16px; margin-top: 20px; transition: opacity 0.3s; }
        .btn-large:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="form-header">
        <h1>Woldia University</h1>
        <p>Digital Student Clearance Request Portal</p>
    </div>

    <div class="content-body">
        <?php if($msg): ?>
            <div class="alert-box <?php echo $status_type; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST" id="requestForm" onsubmit="return validateForm()">
            <div class="form-grid">
                <div class="section-divider">Academic Identity</div>
                
                <div class="input-field">
                    <label>Full Name (First & Father Name)</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>

                <div class="input-field">
                    <label>Student ID Number</label>
                    <input type="text" name="id_no" id="id_no" placeholder="WDU..." required>
                </div>

                <div class="section-divider">Departmental Details</div>

                <div class="input-field">
                    <label>College / School</label>
                    <select name="college_institute" required>
                        <option value="">-- Select College --</option>
                        <option>College of Engineering</option>
                        <option>College of Computing</option>
                        <option>College of Business & Economics</option>
                        <option>College of Health Sciences</option>
                        <option>College of Agriculture</option>
                    </select>
                </div>

                <div class="input-field">
                    <label>Department</label>
                    <input type="text" name="department" id="department" required>
                </div>

                <div class="input-field">
                    <label>Enrollment Program</label>
                    <select name="program" required>
                        <option value="">-- Select Program --</option>
                        <option>Regular Undergraduate</option>
                        <option>Extension Undergraduate</option>
                        <option>Postgraduate (Masters)</option>
                    </select>
                </div>

                <div class="input-field">
                    <label>Reason for Clearance</label>
                    <select name="reason_for_clearance" required>
                        <option value="">-- Select Reason --</option>
                        <option value="End of Academic Year">I. End of Academic Year</option>
                        <option value="Withdrawal">II. Withdrawal</option>
                        <option value="Academic Dismissal">III. Academic Dismissal</option>
                        <option value="Graduation">IV. Graduation</option>
                    </select>
                </div>

                <div class="section-divider">Timeframe & Academic Year</div>

                <div class="input-field">
                    <label>Academic Year</label>
                    <input type="text" name="academic_year" placeholder="2016 E.C" required>
                </div>

                <div class="input-field">
                    <label>Current Semester</label>
                    <select name="semester" required>
                        <option value="">-- Select Semester --</option>
                        <option>Semester I</option>
                        <option>Semester II</option>
                    </select>
                </div>

                <div class="input-field" style="grid-column: span 2;">
                    <label>Last Date of Attendance</label>
                    <input type="date" name="last_date_attended" required>
                </div>

                <button type="submit" name="submit" class="btn-large">SUBMIT APPLICATION</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateForm() {
    // Client-side strict check
    const form = document.getElementById('requestForm');
    const inputs = form.querySelectorAll('input, select');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            alert("Mandatory: Please fill out all fields.");
            input.style.borderColor = "red";
            return false;
        }
        input.style.borderColor = "#e2e8f0";
    }

    const name = document.getElementById('full_name').value;
    const id = document.getElementById('id_no').value.toUpperCase();
    const nameRegex = /^[A-Z][a-z]+\s[A-Z][a-z]+$/;
    const idRegex = /^WDU[0-9]{1,5}$/;

    if (!nameRegex.test(name)) {
        alert("Invalid Name! Must be 'First Father' with first letters capitalized.");
        return false;
    }
    if (!idRegex.test(id)) {
        alert("Invalid ID! Must start with 'WDU' and have 4-8 characters total.");
        return false;
    }
    return true;
}
</script>
</body>
</html>