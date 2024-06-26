<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to the login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Retrieve the faculty ID from the URL parameter
$facultyIdFromUrl = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : 0;

// Check if the faculty ID exists and is valid
$isValidFacultyId = false;
if ($facultyIdFromUrl > 0) {
    $facultyCheckSql = "SELECT id FROM faculties WHERE id = :facultyId AND is_deleted = 0";
    $facultyCheckQuery = $dbh->prepare($facultyCheckSql);
    $facultyCheckQuery->bindParam(':facultyId', $facultyIdFromUrl, PDO::PARAM_INT);
    $facultyCheckQuery->execute();

    if ($facultyCheckQuery->rowCount() > 0) {
        $isValidFacultyId = true;
    }
}

if (!$isValidFacultyId) {
    $error = "Invalid faculty ID or the faculty does not exist.";
}


// Handle form submission for adding a new major
if (isset($_POST['submit']) && $isValidFacultyId) {
    // Retrieve input values from the form
    $majorName = $_POST['major_name'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_path = "assets/img/major/" . $image_name;
        
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $file_extension = pathinfo($image_name, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            $error = "Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($_FILES['image']['size'] > 5242880) { // 5MB
            $error = "File size exceeds maximum limit (5MB).";
        } elseif (!move_uploaded_file($image_tmp_name, $image_path)) {
            $error = "Error uploading image.";
        } else {
            // Resize the uploaded image to 225x225 pixels
            resizeImage($image_path, 225, 225);

            //   the new major into the majors table
            $insertSql = "INSERT INTO majors (faculty_id, major, photos) VALUES (:faculty_id, :major_name, :photos)";
            $insertQuery = $dbh->prepare($insertSql);
            $insertQuery->bindParam(':faculty_id', $facultyIdFromUrl, PDO::PARAM_INT);
            $insertQuery->bindParam(':major_name', $majorName, PDO::PARAM_STR);
            $insertQuery->bindParam(':photos', $image_path, PDO::PARAM_STR);
            $insertQuery->execute();

            if ($insertQuery->rowCount() > 0) {
                echo "<script>alert('Major added successfully.');</script>";
                echo "<script>window.location.href='dashboard.php?id=$facultyIdFromUrl';</script>";
            } else {
                $error = "Error while adding major.";
            }
        }
    } else {
        $error = "Please select an image.";
    }
}


// Function to resize image with white background while preserving transparency
function resizeImage($image_path, $width, $height) {
    // Get image type
    $image_info = getimagesize($image_path);
    $image_mime = $image_info['mime'];

    // Create new image with white background
    if ($image_mime == 'image/jpeg') {
        $image = imagecreatefromjpeg($image_path);
    } elseif ($image_mime == 'image/png') {
        $image = imagecreatefrompng($image_path);
    } elseif ($image_mime == 'image/gif') {
        $image = imagecreatefromgif($image_path);
    } else {
        // Unsupported image format
        return false;
    }

    $original_width = imagesx($image);
    $original_height = imagesy($image);

    // Create new image with white background
    $image_resized = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image_resized, 255, 255, 255);
    imagefill($image_resized, 0, 0, $white);

    // Preserve transparency if any
    if ($image_mime == 'image/png' || $image_mime == 'image/gif') {
        imagecolortransparent($image_resized, $white);
        imagealphablending($image_resized, false);
        imagesavealpha($image_resized, true);
    }

    // Resize original image
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $width, $height, $original_width, $original_height);

    // Save resized image to the original path (overwrite the original image)
    if ($image_mime == 'image/jpeg') {
        imagejpeg($image_resized, $image_path);
    } elseif ($image_mime == 'image/png') {
        imagepng($image_resized, $image_path);
    } elseif ($image_mime == 'image/gif') {
        imagegif($image_resized, $image_path);
    }

    // Free memory
    imagedestroy($image_resized);
    imagedestroy($image);

    return true;
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Add Major</title>
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
            background-color: blue;
            color: #fff;
            border: none;
            border-radius: 4px;
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
                    <h4 class="header-line">Add Major</h4>
                </div>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($error)) { ?>
                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
            <?php }  ?>


            <!-- Add Major Form -->
            <?php if ($isValidFacultyId){ ?>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Add Major
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="major_name">Major Name</label>
                                    <input class="form-control" type="text" name="major_name" id="major_name" required>
                                </div>
                                <div class="input-container form-group">
                                   <label for="image" class="bt-custom custom-file-upload">ADD IMAGE</label>
                                   <input type="file" id="image" name="image" accept="image/*" required>
                                </div>
                                <div id="file-name-display" class="file-name" >No file chosen</div>

                                <center>
                                <div class="form-group">
                                <button type="submit" name="submit" class="bt-custom btn btn-info">Submit</button>
                                </div>
                                </center>
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
