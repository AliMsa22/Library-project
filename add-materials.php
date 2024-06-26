<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$course_code = isset($_GET['course_code']) ? $_GET['course_code'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';

// Check if subject_id and course_code are valid
$sql = "SELECT * FROM subjects WHERE subject_id = :subject_id AND course_code = :course_code AND name = :course_name AND is_deleted = 0";
$query = $dbh->prepare($sql);
$query->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
$query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
$query->bindParam(':course_name', $name, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    $error = "Invalid subject.";
}

$successMessage = isset($_GET['success']) ? urldecode($_GET['success']) : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject_id = intval($_POST['subject_id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $course_code = $_POST['course_code'];

    if(isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp_name = $_FILES['file']['tmp_name'];
        $file_path = "assets/adminUploads/" . $file_name;
        
        // Define allowed extensions for files
        $allowed_extensions = array('doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv' , 'zip');
    
        // Get file extension
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    
        // Check if the file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            $error = "Invalid file format. Only DOC, DOCX, PDF, XLS, XLSX, PPT, PPTX, TXT, RTF, ZIP, and CSV files are allowed.";
        } elseif ($_FILES['file']['size'] > 5242880) { // 5MB
            $error = "File size exceeds maximum limit (5MB).";
        } elseif (!move_uploaded_file($file_tmp_name, $file_path)) {
            $error = "Error uploading file.";
        } else {
            // Handle successful file upload
            // You can perform additional actions here if needed
            // Add the new subject to the database
            $sql = "INSERT INTO subject_materials (course_code, title, file_path, description) VALUES (:course_code, :title, :file_path, :description)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':file_path', $file_path, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);

            // Execute the query
            if ($query->execute()) {
                echo "<script>alert('Material added successfully.');</script>";
                echo "<script>window.location.href='materials.php?subject_id=$subject_id';</script>";
            } else {
                echo "<script>alert('Error adding material.');</script>";
            }
    
        }
    } else {
        $error = "Please select a file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Add new material</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .input-container {
            position: relative;
            display: inline-block;
            margin-bottom: 2px;
            cursor: pointer;
            padding: 5px;
        }

        .input-container input[type="file"] {
            position: absolute;
            opacity: 0;
            width: calc(100% - 10px); 
            height: calc(100% - 10px);
            top: 0;
            left: 0;
            cursor: pointer;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 2px 8px;
            cursor: pointer;
            background-color: blue;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container mt-4">
        <h1>Add Materials for <?php echo htmlentities($_GET['name']); ?></h1>

        <!-- Display success message -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <strong>SUCCESS:</strong> <?php echo htmlentities($successMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Display feedback messages -->
        <?php if (isset($error)) { ?>
                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
            <?php }?>

        <!-- Form to add a new material -->
        <?php if($result){ ?>
        <div class="mt-4">
            <form method="post" action="add-materials.php" enctype="multipart/form-data">
                <br>
                <h3>New Material</h3>
                <input type="hidden" name="subject_id" value="<?php echo htmlentities($_GET['subject_id']); ?>">
                <div class="form-group">
                    <label for="title">Material Title</label>
                    <input type="text" name="title" class="form-control" style="width: 50%;" required>
                </div>
                <!-- <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" name="course_code" class="form-control" style="width: 50%;" required>
                </div> -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" style="width: 50%;" required></textarea>
                </div>
                <div class="input-container form-group">
                    <label for="file" class="custom-file-upload">ADD File</label>
                    <input type="file" id="file" name="file" required>
                </div>
                <div id="file-name-display" class="file-name">No file chosen</div>

                <div class="form-group">
                    <button type="submit" class="bt-custom btn btn-info">Add Material</button>
                </div>
                <input type="hidden" name="course_code" value="<?php echo htmlentities($_GET['course_code']); ?>">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>
</html>
