<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (isset($_POST['register'])) {
    $role = $_POST['role']; 
    $un = mysqli_real_escape_string($conn, $_POST['uname']);
    $ps = mysqli_real_escape_string($conn, $_POST['pass']);
    $fn = mysqli_real_escape_string($conn, $_POST['fullname']);
    $off = $_POST['office'];
    $dept = mysqli_real_escape_string($conn, $_POST['dept'] ?? '');


    $check_sql = "SELECT username FROM student_users WHERE username = '$un' 
                  UNION SELECT username FROM teacher_users WHERE username = '$un' 
                  UNION SELECT username FROM employee_users WHERE username = '$un'";
    $check_res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_res) > 0) {
        echo "<script>alert('Error: This Username is already registered!'); window.history.back();</script>";
        exit();
    }

    if ($role == 'student_users') {
        $sql = "INSERT INTO student_users (username, password, full_name, department) 
                VALUES ('$un', '$ps', '$fn', '$dept')";
    } elseif ($role == 'employee_users') {
        $sql = "INSERT INTO employee_users (username, password, full_name, office_name) 
                VALUES ('$un', '$ps', '$fn', '$off')";
    } else {
        $sql = "INSERT INTO teacher_users (username, password, full_name, office_name, department) 
                VALUES ('$un', '$ps', '$fn', '$off', '$dept')";
    }
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('The user has been successfully registered!!!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .form-container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h3 { text-align: center; color: #1a2a6c; margin-bottom: 20px; }
        input, select { width: 100%; padding: 12px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1a2a6c; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button:hover { background: #2c3e50; }
        .error-msg { color: red; font-size: 11px; margin-bottom: 10px; display: block; font-weight: bold; min-height: 15px; }
        .invalid { border: 2px solid red !important; }
        .valid { border: 2px solid green !important; }
        input:disabled { background: #eee; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="form-container">
    <h3>➕ Add New System User </h3>
    <form method="POST" onsubmit="return validateForm()">
        <label>Select User Type:</label>
        <select name="role" id="role" onchange="toggleDept()" required>
            <option value="">-- Select --</option>
            <option value="student_users">Student </option>
            <option value="teacher_users">Teacher (Advisor/Dean/Head)</option>
            <option value="employee_users">Employee (Staff)</option>
        </select>

        <input type="text" name="uname" id="uname" placeholder="Username (3-6 chars, start w/ letter, incl digit)" oninput="liveCheck('uname')" required>
        <span id="un_err" class="error-msg"></span>

        <input type="password" name="pass" id="pass" placeholder="Password (4-8 chars, starts @ or letter)" oninput="liveCheck('pass')" required>
        <span id="ps_err" class="error-msg"></span>

        <input type="text" name="fullname" id="fullname" placeholder="Full Name" oninput="liveCheck('fullname')" required>
        <span id="fn_err" class="error-msg"></span>
        
        <label>Assign to Office:</label>
        <select name="office" id="office" required>
            <option value="">-- Select Office --</option>
            <option value="none">No Office (Students)</option>
            <option value="advisor_status">Academic Advisor</option>
            <option value="department_head">Department Head</option>
            <option value="school_dean">College/School Dean</option>
            <option value="library">Library Office</option>
            <option value="student_cafeteria">Student Cafeteria</option>
            <option value="student_procter">Student Proctor</option>
            <option value="dean_of_student">Dean of Students</option>
            <option value="campus_security">Campus Security</option>
            <option value="registrar">Registrar Office</option>
        </select>
         <label>Department:</label>
        <input type="text" name="dept" id="dept" placeholder="Department" oninput="liveCheck('dept')" required>
        <span id="dept_err" class="error-msg"></span>
        
        <button type="submit" name="register">Register User</button>
    </form>
</div>

<script>
function toggleDept() {
    const role = document.getElementById('role').value;
    const deptInput = document.getElementById('dept');
    if (role === 'employee_users') {
        deptInput.value = "";
        deptInput.disabled = true;
        deptInput.required = false;
        deptInput.classList.remove('invalid', 'valid');
        document.getElementById('dept_err').innerHTML = "";
    } else {
        deptInput.disabled = false;
        deptInput.required = true;
    }
}

function liveCheck(id) {
    const input = document.getElementById(id);
    const errPrefix = id === 'uname' ? 'un' : id === 'pass' ? 'ps' : id === 'fullname' ? 'fn' : 'dept';
    const err = document.getElementById(errPrefix + "_err");
    const val = input.value;

    if (id === 'uname') {
        const unRegex = /^[A-Za-z](?=.*[0-9])[A-Za-z0-9]{2,5}$/;
        if (!unRegex.test(val)) {
            err.innerHTML = "3-6 chars, start with letter, must include digit!";
            input.classList.add('invalid'); input.classList.remove('valid');
        } else {
            err.innerHTML = ""; input.classList.remove('invalid'); input.classList.add('valid');
        }
    }

    if (id === 'pass') {
        const psRegex = /^[A-Za-z](?=.*[0-9])(?=.*[a-zA-Z]).{3,7}$/;
        if (!psRegex.test(val)) {
            err.innerHTML = "4-8 chars, include special char & digit!";
            input.classList.add('invalid'); input.classList.remove('valid');
        } else if (val === document.getElementById('uname').value) {
            err.innerHTML = "Password cannot be same as Username!";
            input.classList.add('invalid');
        } else {
            err.innerHTML = ""; input.classList.remove('invalid'); input.classList.add('valid');
        }
    }

    if (id === 'fullname') {
        const fnRegex = /^[A-Z][a-z])/;
        if (!fnRegex.test(val) || val.length < 3) {
            err.innerHTML = "First letter Capital, letters only!";
            input.classList.add('invalid'); input.classList.remove('valid');
        } else {
            err.innerHTML = ""; input.classList.remove('invalid'); input.classList.add('valid');
        }
    }

    if (id === 'dept') {
        const deptRegex = /^[A-Za-z\s]{2,}$/;
        if (!input.disabled) {
            if (!deptRegex.test(val)) {
                err.innerHTML = "Min 2 letters only!";
                input.classList.add('invalid'); input.classList.remove('valid');
            } else {
                err.innerHTML = ""; input.classList.remove('invalid'); input.classList.add('valid');
            }
        }
    }
}

function validateForm() {
    const role = document.getElementById('role').value;
    if(!role) { alert("Please select User Type"); return false; }
    
    liveCheck('uname'); 
    liveCheck('pass'); 
    liveCheck('fullname');
    if (role !== 'employee_users') liveCheck('dept');

    const errors = document.querySelectorAll('.error-msg');
    for (let e of errors) {
        if (e.innerHTML !== "") {
            alert("Please fix the errors in red before submitting!");
            return false;
        }
    }
    return true;
}
</script>

</body>
</html>