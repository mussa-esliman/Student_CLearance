<?php
$conn = mysqli_connect("localhost", "root", "", "student_clearance");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>