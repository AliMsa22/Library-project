<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to the login page if the user is not logged in as an admin
if (!isset($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

$major_id = isset($_GET['major_id']) ? intval($_GET['major_id']) : null;

// Validate major_id
$major_check_sql = "SELECT COUNT(*) FROM majors WHERE major_id = :major_id AND is_deleted = 0";
$major_check_query = $dbh->prepare($major_check_sql);
$major_check_query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
$major_check_query->execute();
$major_exists = $major_check_query->fetchColumn();

if ($major_exists == 0) {
    // Redirect to an error page or show an error message
    $error = "Invalid major.";
}

// Handle form submission for adding a new subject
if (isset($_POST['submit'])) {
    // Retrieve input values from the form
    $subjectName = $_POST['subject_name'];
    $courseCode = $_POST['course_code'];
    $description = $_POST['description'];
    $selectedMajors = isset($_POST['majors']) ? $_POST['majors'] : [];

    $checkCourseCodeSql = "SELECT COUNT(*) FROM subjects WHERE course_code = :course_code AND is_deleted = 0";
    $checkCourseCodeQuery = $dbh->prepare($checkCourseCodeSql);
    $checkCourseCodeQuery->bindParam(':course_code', $courseCode, PDO::PARAM_STR);
    $checkCourseCodeQuery->execute();
    $courseCodeExists = $checkCourseCodeQuery->fetchColumn();

    if ($courseCodeExists > 0) {
        $error = "Course code already exists. Please use a different course code.";
    } else {
        // Insert the new subject into the subjects table
        try {
            $dbh->beginTransaction();
            $subjectId = $dbh->lastInsertId();

            foreach ($selectedMajors as $majorId) {
                $insertMajorSubjectSql = "INSERT INTO subjects (major_id, subject_id,name, course_code, description) VALUES (:major_id, :subject_id, :name, :course_code, :description)";
                $insertMajorSubjectQuery = $dbh->prepare($insertMajorSubjectSql);
                $insertMajorSubjectQuery->bindParam(':major_id', $majorId, PDO::PARAM_INT);
                $insertMajorSubjectQuery->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
                $insertMajorSubjectQuery->bindParam(':name', $subjectName, PDO::PARAM_STR);
                $insertMajorSubjectQuery->bindParam(':course_code', $courseCode, PDO::PARAM_STR);
                $insertMajorSubjectQuery->bindParam(':description', $description, PDO::PARAM_STR);
                $insertMajorSubjectQuery->execute();
            }

            $dbh->commit();
            echo "<script>alert('Subject added successfully.');</script>";
            echo "<script>window.location.href='display_subjects.php?major_id=" . htmlentities($_GET['major_id']) . "';</script>";
        } catch (PDOException $e) {
            $dbh->rollBack();
            $error = "Error while adding subject: " . $e->getMessage();
        }
    }
}

// Fetch majors from the database
$majorsSql = "SELECT major_id, major FROM majors WHERE is_deleted = 0";
$majorsQuery = $dbh->prepare($majorsSql);
$majorsQuery->execute();
$majors = $majorsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Online Library Management System | Add Subject</title>
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
                    <h4 class="header-line">Add Subject</h4>
                </div>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
            <?php } ?>

            <!-- Add Common Subject Form -->
            <?php if($major_exists != 0){ ?>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Add Subject
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label for="subject_name">Subject Name</label>
                                    <input class="form-control" type="text" name="subject_name" id="subject_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="course_code">Course Code</label>
                                    <input class="form-control" type="text" name="course_code" id="course_code" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Common Majors</label><br>
                                    <div class="checkbox-inline">
                                        <?php foreach ($majors as $major) { 
                                            if ($major['major_id'] != $major_id) { ?>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="majors[]" value="<?php echo htmlentities($major['major_id']); ?>" style="cursor: pointer;">
                                                    <?php echo htmlentities($major['major']); ?>
                                                </label>
                                            <?php } else { ?>
                                                <label class="checkbox-inline" style="display: none; cursor:none;">
                                                    <input type="hidden" name="majors[]" value="<?php echo htmlentities($major['major_id']); ?>" style="cursor: none; display:none;" checked readonly>
                                                </label>
                                            <?php }
                                        } ?>
                                    </div>
                                </div>
                                <button type="submit" name="submit" class="bt-custom btn btn-info">Submit</button>
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
