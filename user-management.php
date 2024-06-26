<?php
session_start();
error_reporting(0);

include('includes/config.php');

// Redirect to the login page if the user is not logged in as an admin
if (!isset($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

// Handle enabling/disabling student account
if (isset($_GET['action']) && isset($_GET['studentId'])) {
    $action = $_GET['action'];
    $studentId = $_GET['studentId'];

    // Update student status based on action
    if ($action == 'enable') {
        $status = 1;
    } elseif ($action == 'disable') {
        $status = 0;
    }

    // Update student status in the database
    $updateSql = "UPDATE tblstudents SET Status = :status WHERE id = :studentId";
    $updateQuery = $dbh->prepare($updateSql);
    $updateQuery->bindParam(':status', $status, PDO::PARAM_INT);
    $updateQuery->bindParam(':studentId', $studentId, PDO::PARAM_INT);
    $updateQuery->execute();
}

// Fetch all students from the database or search by id
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $studentsSql = "SELECT * FROM tblstudents WHERE StudentId LIKE :search";
    $studentsQuery = $dbh->prepare($studentsSql);
    $searchParam = "%$searchQuery%";
    $studentsQuery->bindParam(':search', $searchParam, PDO::PARAM_STR);
} else {
    $studentsSql = "SELECT * FROM tblstudents";
    $studentsQuery = $dbh->prepare($studentsSql);
}

$studentsQuery->execute();
$students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Student Management</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Menu Section -->
    <?php include('includes/header.php'); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Student Management</h4>
                </div>
            </div>

            <!-- Search Box -->
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="user-management.php">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" placeholder="Search by ID" value="<?php echo htmlentities($searchQuery); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="user-management.php" class="btn btn-default">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Display students -->
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student) { ?>
                            <tr>
                                <td><?php echo htmlentities($student['id']); ?></td>
                                <td><?php echo htmlentities($student['StudentId']); ?></td>
                                <td><?php echo htmlentities($student['FullName']); ?></td>
                                <td><?php echo htmlentities($student['EmailId']); ?></td>
                                <td><?php echo htmlentities($student['MobileNumber']); ?></td>
                                <td><?php echo $student['Status'] == 1 ? 'Enabled' : 'Disabled'; ?></td>
                                <td>
                                    <?php if ($student['Status'] == 1) { ?>
                                        <a href="user-management.php?action=disable&studentId=<?php echo $student['id']; ?>" class="btn btn-danger">Disable</a>
                                    <?php } else { ?>
                                        <a href="user-management.php?action=enable&studentId=<?php echo $student['id']; ?>" class="btn btn-success">Enable</a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    <!-- JavaScript files -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
