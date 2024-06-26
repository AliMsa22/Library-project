<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : null;
$major_id = isset($_GET['major_id']) ? intval($_GET['major_id']) : null; // Ensure major_id is set

// Validate subject_id
$subject_check_sql = "SELECT name, course_code, description FROM subjects WHERE subject_id = :subject_id AND major_id = :major_id AND is_deleted = 0";
$subject_check_query = $dbh->prepare($subject_check_sql);
$subject_check_query->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
$subject_check_query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
$subject_check_query->execute();
$result = $subject_check_query->fetch(PDO::FETCH_OBJ);

if (!$result) {
    // Redirect to error page or show error message
    $error = "Invalid subject.";
}

// Validate major_id
$major_check_sql = "SELECT COUNT(*) FROM majors WHERE major_id = :major_id AND is_deleted = 0";
$major_check_query = $dbh->prepare($major_check_sql);
$major_check_query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
$major_check_query->execute();
$major_exists = $major_check_query->fetchColumn();

if ($major_exists == 0) {
    // Redirect to error page or show error message
    $error = "Invalid major.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $current_code = $_POST['current_code'];
    $major_id = $_POST['major_id'];
    $name = $_POST['name'];
    $course_code = $_POST['course_code'];
    $description = $_POST['description'];

    $checkCourseCodeSql = "SELECT COUNT(*) FROM subjects WHERE course_code = :course_code AND course_code != :current_code AND is_deleted = 0";
    $checkCourseCodeQuery = $dbh->prepare($checkCourseCodeSql);
    $checkCourseCodeQuery->bindParam(':course_code', $course_code, PDO::PARAM_STR);
    $checkCourseCodeQuery->bindParam(':current_code', $current_code, PDO::PARAM_STR);
    $checkCourseCodeQuery->execute();
    $courseCodeExists = $checkCourseCodeQuery->fetchColumn();

    if ($courseCodeExists > 0) {
        $error = "Course code already exists. Please use a different course code.";
    } else {
        $sql = "UPDATE subject_materials SET course_code = :course_code WHERE course_code = :current_code";
        $query = $dbh->prepare($sql);
        $query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $query->bindParam(':current_code', $current_code, PDO::PARAM_INT);
        $query->execute();

        // Update the subject in the database
        $sql = "UPDATE subjects SET name = :name, course_code = :course_code, description = :description WHERE course_code = :current_code AND is_deleted = 0";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':current_code', $current_code, PDO::PARAM_INT);

        // Execute the query
        if ($query->execute()) {
            echo "<script>alert('Subject edited successfully.');</script>";
            echo "<script>window.location.href='display_subjects.php?major_id=$major_id';</script>";
        } else {
            $error = "Error editing subject.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Edit subject</title>
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
        <h1>Edit subject <?php echo htmlentities($result->name); ?></h1>

        <!-- Display feedback messages -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
        <?php } ?>

        <!-- Form to edit subject -->
        <?php if($major_exists != 0 && $result){ ?>
        <div class="mt-4">
            <form method="post" action="edit-subject.php?subject_id=<?php echo htmlentities($subject_id); ?>&major_id=<?php echo htmlentities($major_id); ?>">
                <br>
                <h3>New Subject</h3>
                <input type="hidden" name="current_code" value="<?php echo htmlentities($result->course_code); ?>">
                <input type="hidden" name="major_id" value="<?php echo htmlentities($major_id); ?>">
                <div class="form-group">
                    <label for="name">Subject Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlentities($result->name); ?>" style="width: 50%;" required>
                </div>
                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" name="course_code" class="form-control" value="<?php echo htmlentities($result->course_code); ?>" style="width: 50%;" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" style="width: 50%;" required><?php echo htmlentities($result->description); ?></textarea>
                </div>
                <button type="submit" class="bt-custom btn btn-info">Update Subject</button>
            </form>
        </div>
        <?php } ?>
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
