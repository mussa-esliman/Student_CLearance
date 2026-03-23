<?php
/**
 * Woldia University Student Clearance System
 * Login Portal - Line-by-line optimized version
 */
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

        //   (Student Users) 
        if ($table == 'student_users') {
            
            $_SESSION['id_no'] = isset($row['id_no']) ? $row['id_no'] : $row['username'];
            header("Location: student_dashboard.php");
            exit();
        }

        //  (Admin, Teacher, Employee)
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
    <title>WDU Clearance | Secure Login</title>
    <style>
        body { 
            margin: 0; padding: 0; 
            background-image: url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; background-position: center; 
            height: 100vh; font-family: 'Segoe UI', Tahoma, sans-serif; 
            display: flex; justify-content: center; align-items: center; 
            overflow: hidden;
        }
        .box { 
            width: 380px; background-color: rgba(0, 0, 0, 0.88); 
            color: #fff; padding: 45px; border-radius: 25px; 
            box-shadow: 0 20px 35px rgba(0,0,0,0.6); text-align: center; 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        h1 { margin: 0 0 10px; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; color: #00ffcc; }
        p { color: #bdc3c7; font-size: 13px; margin-bottom: 30px; }
        .box input, .box select { 
            width: 100%; padding: 12px 0; font-size: 16px; 
            color: #fff; margin-bottom: 25px; border: none; 
            border-bottom: 2px solid #00ffcc; background: transparent; 
            outline: none; transition: 0.3s;
        }
        .box input:focus { border-bottom: 2px solid #ffc107; }
        .box select option { background-color: #1a1a1a; color: #fff; }
        .box input[type="submit"] { 
            border: none; outline: none; height: 50px; 
            background: #00ffcc; color: #000; font-weight: 700; 
            font-size: 18px; border-radius: 30px; cursor: pointer; 
            transition: 0.4s; margin-top: 10px; text-transform: uppercase;
        }
        .box input[type="submit"]:hover { 
            background: #ffc107; transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        ::placeholder { color: rgba(255,255,255,0.5); }
        .footer-text { margin-top: 25px; font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="box">
        <h1>WDU CLEARANCE</h1>
        <p>Student & Staff Digital Portal</p>
        
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Enter ID or Username" required autocomplete="off" />
            <input type="password" name="password" placeholder="Enter Password" required />
            
            <select name="user_type" required>
                <option value="" disabled selected>-- Select Your Role --</option>
                <option value="student_users">Student (Login with ID)</option>
                <option value="teacher_users">Teachers (Advisor/Head/Dean)</option>
                <option value="employee_users">Employees (Lib/Cafe/Proctor)</option>
                <option value="admin_users">System Admin</option>
            </select>

            <input type="submit" value="Sign In" name="login" />
        </form>

        <div class="footer-text">
            &copy; <?php echo date('Y'); ?> Woldia University ICT Support
        </div>
    </div>
</body>
</html>