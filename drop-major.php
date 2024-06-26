<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Retrieve the faculty ID from the URL parameter
$facultyId = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : 0;

// Check if the faculty ID exists and is valid
$isValidFacultyId = false;
if ($facultyId > 0) {
    $facultyCheckSql = "SELECT id FROM faculties WHERE id = :facultyId AND is_deleted = 0";
    $facultyCheckQuery = $dbh->prepare($facultyCheckSql);
    $facultyCheckQuery->bindParam(':facultyId', $facultyId, PDO::PARAM_INT);
    $facultyCheckQuery->execute();

    if ($facultyCheckQuery->rowCount() > 0) {
        $isValidFacultyId = true;
    }
}

// Fetch majors associated with the specified faculty for the dropdown menu
if ($isValidFacultyId) {
    $sql = "SELECT major_id, major FROM majors WHERE faculty_id = :facultyId AND is_deleted = 0";
    $query = $dbh->prepare($sql);
    $query->bindParam(':facultyId', $facultyId, PDO::PARAM_INT);
    $query->execute();
    $majors = $query->fetchAll(PDO::FETCH_OBJ);
} else {
    $error = "Invalid faculty ID or the faculty does not exist.";
}

// Handle form submission to delete a major
if (isset($_POST['submit']) && $isValidFacultyId) {
    $majorId = intval($_POST['major_id']);

    // Delete the selected major from the majors table
    $deleteSql = "UPDATE majors SET is_deleted = 1 WHERE major_id = :major_id";
    $deleteQuery = $dbh->prepare($deleteSql);
    $deleteQuery->bindParam(':major_id', $majorId, PDO::PARAM_INT);
    $deleteQuery->execute();

    // Check if the deletion was successful
    if ($deleteQuery->rowCount() > 0) {
        // Update majors for the dropdown menu
        $sql = "SELECT major_id, major FROM majors WHERE faculty_id = :facultyId AND is_deleted = 0";
        $query = $dbh->prepare($sql);
        $query->bindParam(':facultyId', $facultyId, PDO::PARAM_INT);
        $query->execute();
        $majors = $query->fetchAll(PDO::FETCH_OBJ);
        //Delete subjects of major deleted
        $delete = "UPDATE subjects SET is_deleted = 1 WHERE major_id = :major_id";
        $deleteMajor = $dbh->prepare($delete);
        $deleteMajor->bindParam(':major_id', $majorId, PDO::PARAM_INT);
        $deleteMajor->execute();

        // Step 1: Select all unique course codes from the subject_materials table
        $sql_course_codes = "SELECT DISTINCT course_code FROM subject_materials WHERE is_deleted = 0";
        $query_course_codes = $dbh->prepare($sql_course_codes);
        $query_course_codes->execute();
        $course_codes = $query_course_codes->fetchAll(PDO::FETCH_COLUMN);

        foreach ($course_codes as $course) {
            // Step 2: Check if all subjects with this course code are marked as deleted
            $sql_subject_check = "SELECT COUNT(*) AS total, SUM(CASE WHEN is_deleted = 1 THEN 1 ELSE 0 END) AS deleted FROM subjects WHERE course_code = :course_code";
            $query_subject_check = $dbh->prepare($sql_subject_check);
            $query_subject_check->bindParam(':course_code', $course, PDO::PARAM_STR);
            $query_subject_check->execute();
            $result = $query_subject_check->fetch(PDO::FETCH_ASSOC);

            $total_subjects = intval($result['total']);
            $deleted_subjects = intval($result['deleted']);

            if ($total_subjects > 0 && $total_subjects == $deleted_subjects) {
                // Step 3: Update is_deleted to 1 for this course code in subject_materials table
                $sql_update_materials = "UPDATE subject_materials SET is_deleted = 1 WHERE course_code = :course_code";
                $query_update_materials = $dbh->prepare($sql_update_materials);
                $query_update_materials->bindParam(':course_code', $course, PDO::PARAM_STR);
                $query_update_materials->execute();
            }
        }
        echo "<script>alert('Major deleted successfully.');</script>";
        echo "<script>window.location.href='dashboard.php?id=$facultyId';</script>";

    } else {
        $error = "Error while deleting major.";
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Drop Major</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <!-- Menu Section -->
    <?php include('includes/header.php'); ?>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Drop Major</h4>
                </div>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($error)) { ?>
                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
            <?php } ?>


            <!-- Drop Major Form -->
            <?php if ($isValidFacultyId) { ?>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Drop Major
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post">
                                    <div class="form-group">
                                        <label for="major_id">Select Major</label>
                                        <select class="form-control" id="major_id" name="major_id" required>
                                            <option value="" disabled selected>Select a major...</option>
                                            <?php foreach ($majors as $major) { ?>
                                                <option value="<?php echo htmlentities($major->major_id); ?>">
                                                    <?php echo htmlentities($major->major); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
