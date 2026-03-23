<?php include('admin_header.php'); ?>
<?php include('admin_sidebar.php'); ?>

<?php
$total_students = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM student_users"));
$total_requests = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM clearance_form"));
$completed = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM clearance_form WHERE registrar = 'approved'"));
?>

<div id="dashboard-view" class="view-section">
    <div class="stat-card card-1">
        <h3>TOTAL STUDENTS</h3>
        <h2><?php echo $total_students; ?></h2>
    </div>
    <div class="stat-card card-2">
        <h3>ACTIVE REQUESTS</h3>
        <h2><?php echo $total_requests; ?></h2>
    </div>
    <div class="stat-card card-3">
        <h3>COMPLETED</h3>
        <h2><?php echo $completed; ?></h2>
    </div>
</div>

<div id="about-view" class="view-section">
    <div class="info-panel">
        <h2>About the System</h2>
        <p>The Woldia University Online Clearance System (WUOCS) is designed to modernize the graduation and withdrawal processes for students. By digitizing office approvals, we eliminate long queues and physical paperwork.</p>
        <p>The system ensures transparency, allowing students to see exactly which office has cleared them and which office still has pending issues in real-time.</p>
    </div>
</div>

<div id="contact-view" class="view-section">
    <div class="info-panel">
        <h2>Contact Support</h2>
        <p>For technical assistance, system errors, or administrative access, please contact the ICT Support Team:</p>
        <p>📧 <b>Email:</b> ict.support@wdu.edu.et</p>
        <p>📞 <b>Extension:</b> 8080</p>
        <p>📍 <b>Office:</b> ICT Directorate, Block 12, Main Campus</p>
    </div>
</div>

<div id="iframe-view" class="view-section">
    <iframe name="admin_frame" id="admin_frame"></iframe>
</div>

<?php include('admin_footer.php'); ?>