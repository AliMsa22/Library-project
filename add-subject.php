<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}
$successMessage = isset($_GET['success']) ? urldecode($_GET['success']) : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $major_id = intval($_POST['major_id']);
    $name = $_POST['name'];
    $course_code = $_POST['course_code'];
    $description = $_POST['description'];

    // Add the new subject to the database
    $sql = "INSERT INTO subjects (major_id, name, course_code, description) VALUES (:major_id, :name, :course_code, :description)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);

    // Execute the query
    if ($query->execute()) {
        echo "<script>alert('Subject added successfully.');</script>";
        echo "<script>window.location.href='display_subjects.php?major_id=$major_id';</script>";
    } else {
        echo "<script>alert('Error adding subject.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Add new subject</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container mt-4">
        <h1>Add Subjects for <?php echo htmlentities($_GET['name']); ?></h1>

        <!-- Display success message -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <strong>SUCCESS:</strong> <?php echo htmlentities($successMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Form to add a new subject -->
        <div class="mt-4">
            <form method="post" action="add-subject.php">
                <br>
                <h3>New Subject</h3>
                <input type="hidden" name="major_id" value="<?php echo htmlentities($_GET['major_id']); ?>">
                <div class="form-group">
                    <label for="name">Subject Name</label>
                    <input type="text" name="name" class="form-control" style="width: 50%;" required>
                </div>
                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" name="course_code" class="form-control" style="width: 50%;" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" style="width: 50%;" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Subject</button>
            </form>
        </div>

    </div>
    <br>
    <br>



    <?php include('includes/footer.php'); ?>
    <!-- JavaScript files -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>

</body>
</html>