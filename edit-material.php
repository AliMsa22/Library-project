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
$material_id = isset($_GET['material_id']) ? intval($_GET['material_id']) : null; 
$course_code = isset($_GET['course_code']) ? $_GET['course_code'] : '';

$sqlSubject = "SELECT * FROM subjects WHERE subject_id = :subject_id AND course_code = :course_code AND is_deleted = 0";
$querySubject = $dbh->prepare($sqlSubject);
$querySubject->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
$querySubject->bindParam(':course_code', $course_code, PDO::PARAM_STR);
$querySubject->execute();
$resultSub = $querySubject->fetch(PDO::FETCH_OBJ);
if (!$resultSub) {
    $error = "Invalid Material.";
}


$sql = "SELECT title, file_path, description FROM subject_materials WHERE material_id = :material_id AND course_code = :course_code AND is_deleted = 0";
$query = $dbh->prepare($sql);
$query->bindParam(':material_id', $material_id, PDO::PARAM_INT);
$query->bindParam(':course_code', $course_code, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
if (!$result) {
    $error = "Invalid Material.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $subject_id = $_POST['subject_id'];
    $material_id = $_POST['material_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

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
            $sql = "UPDATE subject_materials SET title = :title, file_path = :file_path, description = :description WHERE material_id = :material_id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':file_path', $file_path, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':material_id', $material_id, PDO::PARAM_INT);


            // Execute the query
            if ($query->execute()) {
                echo "<script>alert('Material edited successfully.');</script>";
                echo "<script>window.location.href='materials.php?subject_id=$subject_id';</script>";
            } else {
                echo "<script>alert('Error editing material.');</script>";
            }
    
        }
    } else {
        $curr = $_POST['curr'];
        $sql = "UPDATE subject_materials SET title = :title, file_path = :curr, description = :description WHERE material_id = :material_id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':curr', $curr, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':material_id', $material_id, PDO::PARAM_INT);


            // Execute the query
            if ($query->execute()) {
                echo "<script>alert('Material edited successfully.');</script>";
                echo "<script>window.location.href='materials.php?subject_id=$subject_id';</script>";
            } else {
                echo "<script>alert('Error editing material.');</script>";
            }
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
    <title>Edit material</title>
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
            width: calc(100% - 10px); /* Adjust width */
            height: calc(100% - 10px); /* Adjust height */
            top: 0;
            left: 0;
            cursor: pointer;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 2px 8px;
            cursor: pointer;
            background-color: #5bc0de;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container mt-4">
        <h1>Edit Material <?php echo htmlentities($result-> title); ?></h1>

        <!-- Display feedback messages -->
        <?php if (isset($error)) { ?>
                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
            <?php }?>

        <!-- Form to edit a material -->
        <?php if($result && $resultSub) { ?>
        <div class="mt-4">
            <form method="post" action="edit-material.php" enctype="multipart/form-data">
                <br>
                <h3>Edit Material</h3>
                <div class="form-group">
                    <label for="title">Material Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlentities($result->title) ?>" style="width: 50%;" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" style="width: 50%;" required><?php echo htmlentities($result->description) ?></textarea>
                </div>
                <div class="input-container form-group">
                    <label for="file" class="custom-file-upload">New File</label>
                    <input type="file" id="file" name="file">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Material</button>
                </div>
                <input type="hidden" name="subject_id" value="<?php echo htmlentities($subject_id); ?>">
                <input type="hidden" name="material_id" value="<?php echo htmlentities($material_id); ?>">
                <input type="hidden" name="curr" value="<?php echo htmlentities($result->file_path); ?>">
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