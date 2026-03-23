<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_POST['submit'])) {
    $full_name = $_POST['full_name'];
    $id_no = $_POST['id_no'];
    $college = $_POST['college_institute'];
    $dept = $_POST['department'];
    $program = $_POST['program'];
    $reason = $_POST['reason_for_clearance'];
    $year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $date = $_POST['last_date_attended'];
    $sql = "INSERT INTO clearance_form (full_name, id_no, collage_institute, department, program, reason_for_clearance, academic_year, semester, last_date_attended) 
            VALUES ('$full_name', '$id_no', '$college', '$dept', '$program', '$reason', '$year', '$semester', '$date')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Clearance request is sent to all approval successfully!!!'); window.location='student_dashboard.php';</script>";
    } else {
        echo "Error: Please fix the fault!!!: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="am">
    <head>
        <meta charset="UTF-8" />
        <title>WDU Clearance - Student Request Form</title>
        <style>
            body {
                font-family: "Segoe UI", Arial, sans-serif;
                background-color: #f4f7f6;
                margin: 0;
                padding: 20px;
            }
            .container {
                background: white;
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #2c3e50;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
            .header h2 {
                margin: 0;
                color: #2c3e50;
                text-transform: uppercase;
            }
            .section-title {
                background: #e9ecef;
                padding: 10px;
                font-weight: bold;
                margin-top: 20px;
                border-left: 5px solid #2c3e50;
            }
            .row {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-top: 15px;
            }
            .input-group {
                flex: 1;
                min-width: 250px;
                display: flex;
                flex-direction: column;
            }
            label {
                font-size: 14px;
                margin-bottom: 5px;
                font-weight: bold;
                color: #444;
            }
            input,
            select {
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                outline: none;
            }
            input:focus {
                border-color: #3498db;
            }
            .btn-submit {
                width: 100%;
                padding: 15px;
                background-color: #2c3e50;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                margin-top: 25px;
            }
            .btn-submit:hover {
                background-color: #1a252f;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Woldia University</h2>
                <p>Office of the Registrar | Clearance Form for Regular Program (Form 01)</p>
            </div>

            <form action="" method="POST">
                <div class="section-title">Part 1: Personal & Academic Information</div>

                <div class="row">
                    <div class="input-group">
                        <label>1.1 Full Name (Block Letters)</label>
                        <input type="text" name="full_name" required placeholder="E.g. ABEBE KEBEDE" />
                    </div>
                    <div class="input-group">
                        <label>ID No.</label>
                        <input type="text" name="id_no" required placeholder="E.g. WDU123456" />
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label>1.2 College/School/Institute</label>
                        <input type="text" name="college_institute" required />
                    </div>
                    <div class="input-group">
                        <label>1.3 Department</label>
                        <input type="text" name="department" required />
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label>1.4 Program</label>
                        <select name="program">
                            <option value="Undergraduate">Undergraduate Degree</option>
                            <option value="Postgraduate">Postgraduate Degree</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>1.5 Reason for Clearance</label>
                        <select name="reason_for_clearance">
                            <option value="End of Academic Year">I. End of Academic Year</option>
                            <option value="Withdrawal">II. Withdrawal</option>
                            <option value="Academic Dismissal">III. Academic Dismissal</option>
                            <option value="Graduation">IV. Graduation</option>
                            <option value="Forced Withdrawal">V. Forced Withdrawal</option>
                            <option value="Disciplinary Case">VI. Disciplinary Case</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" placeholder="E.g. 2025/26" />
                    </div>
                    <div class="input-group">
                        <label>Semester</label>
                        <input type="text" name="semester" placeholder="E.g. Semester II" />
                    </div>
                    <div class="input-group">
                        <label>1.6 Last Date Class Attend</label>
                        <input type="date" name="last_date_attended" />
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit">Send Clearance Request</button>
            </form>
        </div>
    </body>
</html>