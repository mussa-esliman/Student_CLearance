<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");


if (isset($_POST['add_dept'])) {
    $dept = mysqli_real_escape_string($conn, $_POST['dept_name']);
    $sql = "INSERT INTO departments (dept_name) VALUES ('$dept')";
    mysqli_query($conn, $sql);
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM departments WHERE id = $id");
    header("Location: manage_departments.php");
}

$result = mysqli_query($conn, "SELECT * FROM departments ORDER BY dept_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        input { width: 70%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #1a2a6c; color: white; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .del-btn { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="box">
    <h3>🏢 Manage University Departments</h3>
    
    <form method="POST" style="margin-bottom: 30px;">
        <input type="text" name="dept_name" placeholder="Enter Department Name (e.g. Computer Science)" required>
        <button type="submit" name="add_dept">Add Dept</button>
    </form>

    <table>
        <tr>
            <th>No.</th>
            <th>Department Name</th>
            <th>Action</th>
        </tr>
        <?php 
        $i = 1;
        while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $row['dept_name']; ?></td>
            <td><a href="?delete=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>