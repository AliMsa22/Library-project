<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_path = "assets/img/faculty/" . $image_name;
        
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
            // Resize the uploaded image to 200x200 pixels
            resizeImage($image_path, 200, 200);

            $sql = "INSERT INTO faculties (name, photos) VALUES (:name, :photos)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':photos', $image_path, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $msg = "Faculty added successfully.";
            } else {
                $error = "Error while adding faculty.";
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
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Add Faculty</title>
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
                    <h4 class="header-line">Add Faculty</h4>
                </div>
            </div>
            
            <!-- Display feedback messages -->
            <?php if (isset($error)) { ?>
                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
            <?php } else if (isset($msg)) { ?>
                <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?> </div>
            <?php } ?>

            <!-- Add Faculty Form -->
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Faculty Information
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input class="form-control" type="text" name="name" required />
                                </div>
                                
                                    <div class="input-container form-group">
                                        <label for="image" class=" custom-file-upload">ADD IMAGE</label>
                                        <input type="file" id="image" name="image" accept="image/*" required>
                                    </div>
                                    <div id="file-name-display" class="file-name" >No file chosen</div>

                                <center>
                                <div class="form-group">
                                <button type="submit" name="submit" class="bt-custom btn btn-info ">Submit</button>
                                </div>
                                </center>
                            </form>
                        </div>
                    </div>
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
