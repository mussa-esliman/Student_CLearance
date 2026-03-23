<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_clearance");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $table = $_POST['user_type']; 

    $sql = "SELECT * FROM $table WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_type'] = $table;
        $_SESSION['username'] = $row['username']; 
        $_SESSION['full_name'] = isset($row['full_name']) ? $row['full_name'] : $row['username'];
        $_SESSION['office'] = isset($row['office_name']) ? $row['office_name'] : '';
        $_SESSION['user_dept'] = isset($row['department']) ? $row['department'] : ''; 
        
        if ($table == 'admin_users') {
            header("Location: admin.php");
        } 
        elseif ($table == 'teacher_users') {
            header("Location: teacher_dashboard.php");
        } 
        elseif ($table == 'employee_users') {
            header("Location: employee_dashboard.php");
        }
        elseif ($table == 'student_users') {
            header("Location: student_status.php"); 
        }
        exit();
        
    } else {
        echo "<script>
                alert('Error: Incorrect username or password!');
                window.location.href='login.php'; 
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WDU Clearance Login</title>
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            background-image: url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; 
            background-position: center; 
            height: 100vh; 
            font-family: 'Segoe UI', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
        }
        .box { 
            width: 350px; 
            background-color: rgba(0, 0, 0, 0.85); 
            color: #fff; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 25px rgba(0,0,0,0.5); 
            text-align: center; 
        }
        h1 { 
            margin: 0 0 30px; 
            font-size: 22px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            color: #00ffcc; 
        }
        .box input, .box select { 
            width: 100%; 
            padding: 10px 0; 
            font-size: 16px; 
            color: #fff; 
            margin-bottom: 25px; 
            border: none; 
            border-bottom: 1px solid #fff; 
            background: transparent; 
            outline: none; 
            display: block; 
        }
        .box select option { 
            background-color: #333; 
            color: #fff; 
        }
        .box input[type="submit"] { 
            border: none; 
            outline: none; 
            height: 45px; 
            background: #00ffcc; 
            color: #000; 
            font-weight: bold; 
            font-size: 18px; 
            border-radius: 25px; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 15px; 
        }
        .box input[type="submit"]:hover { 
            background: #ffc107; 
            transform: scale(1.05); 
        }
        ::placeholder { 
            color: rgba(255,255,255,0.7); 
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>WDU Clearance</h1>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Enter ID or Username" required />
            <input type="password" name="password" placeholder="Enter Password" required />
            
            <select name="user_type" required>
                <option value="" disabled selected>Select Your Role</option>
                <option value="student_users">Student (Login with ID)</option>
                <option value="teacher_users">Teachers (Advisor/Head/Dean)</option>
                <option value="employee_users">Employees (Lib/Cafe/Proctor)</option>
                <option value="admin_users">System Admin</option>
            </select>

            <input type="submit" value="LOGIN" name="login" />
        </form>
    </div>
</body>
</html>