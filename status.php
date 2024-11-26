<?php

include 'admin/db_connect.php';

// Check if the user is logged in and determine their type
if (isset($_SESSION['login_id'])) {
    $user_id = $_SESSION['login_id']; // The logged-in user's ID
    $user_type = $_SESSION['login_type']; // The logged-in user's type (patient, doctor, etc.)
} else {
    // If no user is logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Fetch doctors and patients data
$doctor = $conn->query("SELECT * FROM therapists_list ");
while ($row = $doctor->fetch_assoc()) {
    $doc_arr[$row['id']] = $row;
}

$patient = $conn->query("SELECT * FROM users");
while ($row = $patient->fetch_assoc()) {
    $p_arr[$row['id']] = $row;
}

// Adjust the query to fetch appointments for the logged-in user
$where = '';
if ($user_type == 3) {  // Assuming 3 is for patients
    $where = " WHERE customer_id = " . $user_id; // Show only appointments for the logged-in patient
} elseif ($user_type == 2) {  // Assuming 2 is for doctors
    $where = " WHERE therapists_id = " . $_SESSION['login_therapists_id']; // Show only appointments for the logged-in doctor
}

$qry = $conn->query("SELECT * FROM appointment_list " . $where . " ORDER BY id DESC");
?>

<div class="container-fluid d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Schedule</th>
                            <th>Therapists</th>
                            <th>Customer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <?php while ($row = $qry->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("l M d, Y h:i A", strtotime($row['schedule'])) ?></td>
                        <td><?php echo $doc_arr[$row['therapists_id']]['name'] ?></td>
                        <td><?php echo $p_arr[$row['customer_id']]['name'] ?></td>
                        <td>
                            <?php if ($row['status'] == 0): ?>
                                <span class="badge badge-warning">Pending Request</span>
                            <?php elseif ($row['status'] == 1): ?>
                                <span class="badge badge-success">Confirmed</span>
                            <?php elseif ($row['status'] == 2): ?>
                                <span class="badge badge-info">Rescheduled</span>
                            <?php elseif ($row['status'] == 3): ?>
                                <span class="badge badge-success">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</div>
