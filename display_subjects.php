<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect if not logged in as an admin or student
if (strlen($_SESSION['login']) == 0 && strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Function to fetch major details
function fetchMajorDetails($dbh, $major_id) {
    $sql = "SELECT major FROM majors WHERE major_id = :major_id AND is_deleted = 0";
    $query = $dbh->prepare($sql);
    $query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Function to fetch subjects for a major
function fetchSubjectsForMajor($dbh, $major_id) {
    $sql = "SELECT subject_id, name, course_code, description FROM subjects WHERE major_id = :major_id AND is_deleted = 0";
    $query = $dbh->prepare($sql);
    $query->bindParam(':major_id', $major_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Initialize variables
$major_id = isset($_GET['major_id']) ? intval($_GET['major_id']) : null;
$major_name = "";
$subjects = [];
$isAdmin = isset($_SESSION['alogin']) && strlen($_SESSION['alogin']) > 0;

// Fetch data if major_id is provided
if ($major_id) {
    $majorDetails = fetchMajorDetails($dbh, $major_id);
    if ($majorDetails) {
        $major_name = $majorDetails['major'];
        $subjects = fetchSubjectsForMajor($dbh, $major_id);
    } else {
        echo "Major not found.";
        exit;
    }
} else {
    echo "Major ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Subjects for <?php echo htmlentities($major_name); ?></title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 75%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: blue;
            color: white;
        }
        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-4">
        <h1>Subjects for <?php echo htmlentities($major_name); ?></h1>

        <div style="display: flex;">
            <!-- Display the subjects -->
            <?php if ($subjects): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th>Materials</th>
                            <?php if ($isAdmin): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlentities($subject['name']); ?></td>
                                <td><?php echo htmlentities($subject['course_code']); ?></td>
                                <td><?php echo htmlentities($subject['description']); ?></td>
                                <td>
                                    <a href="materials.php?subject_id=<?php echo htmlentities($subject['subject_id']); ?>">View Materials</a>
                                </td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <!-- Link to delete a subject -->
                                        <a href="delete-subject.php?subject_id=<?php echo htmlentities($subject['subject_id']); ?>&major_id=<?php echo htmlentities($major_id); ?>&course_code=<?php echo htmlentities($subject['course_code']); ?>" class="btn btn-danger">Delete</a>
                                        <a href="edit-subject.php?subject_id=<?php echo htmlentities($subject['subject_id']); ?>&major_id=<?php echo htmlentities($major_id); ?>" class="btn btn-danger">Edit</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No subjects found for this major.</p>
            <?php endif; ?>


            <?php if ($isAdmin): ?>
                <div class="col-md-4 col-sm-4 col-xs-6" style="margin-left: 2%;">
                    <!-- Add Subject button -->
                    <div class="alert alert-success back-widget-set text-center">
                        <a href="add-common-subject.php?major_id=<?php echo htmlentities($major_id); ?>" class="bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="Add Common Subject">
                        <i class="fa fa-plus"></i><br>Add Subject
                        </a>
                    </div>

                </div>

            <?php endif; ?>
        </div>
            <br>
            <br>
            <br>
    </div>

    <?php include('includes/footer.php'); ?>
    <!-- JavaScript files -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
