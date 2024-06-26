<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login']) == 0 && strlen($_SESSION['alogin']) == 0) { 
    header('location:index.php');
} else {
    // Check if faculty ID is provided in the URL
    if(isset($_GET['id'])) {
        // Fetch the faculty to check if it exists and is not deleted
        $facultyId = $_GET['id'];
        $sql = "SELECT id, is_deleted FROM faculties WHERE id = :facultyId";
        $query = $dbh->prepare($sql);
        $query->bindParam(':facultyId', $facultyId, PDO::PARAM_INT);
        $query->execute();
        $faculty = $query->fetch(PDO::FETCH_OBJ);

        if(!$faculty || $faculty->is_deleted == 1) {
            echo "<div>Invalid or deleted faculty ID.</div><br><br>";
        } else {
            // Fetch majors for the valid and non-deleted faculty
            $sql = "SELECT major_id, major, faculty_id, photos FROM majors WHERE faculty_id = :facultyId AND is_deleted = 0";
            $query = $dbh->prepare($sql);
            $query->bindParam(':facultyId', $facultyId, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $numRows = $query->rowCount();
?>
            <!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
                <meta name="description" content="" />
                <meta name="author" content="" />
                <title>Online Library Management System | User Dashboard</title>
                <!-- BOOTSTRAP CORE STYLE  -->
                <link href="assets/css/bootstrap.css" rel="stylesheet" />
                <!-- FONT AWESOME STYLE  -->
                <link href="assets/css/font-awesome.css" rel="stylesheet" />
                <!-- CUSTOM STYLE  -->
                <link href="assets/css/style.css" rel="stylesheet" />
                <!-- GOOGLE FONT -->
                <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
            </head>
            <body>
                <!------MENU SECTION START-->
                <?php include('includes/header.php');?>
                <!-- MENU SECTION END-->
                <div class="content-wrapper">
                    <div class="container">
                        <div class="row pad-botm">
                            <div class="col-md-12">
                                <?php if($facultyId == 0) { ?>
                                    <h4 class="header-line">DASHBOARD</h4>
                                <?php } else { ?>
                                    <h4 class="header-line">MAJORS</h4>
                                <?php } ?>    
                            </div>
                        </div> 
                        <div class="row">
                            <?php if ($numRows > 0) {
                                foreach ($results as $faculty) {
                            ?>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="alert alert-success back-widget-set text-center">
                                            <a href="display_subjects.php?major_id=<?php echo $faculty->major_id; ?>">
                                                <img src="<?php echo $faculty->photos; ?>" alt="photo">
                                                <?php echo "<p> " . $faculty->major . "</p>";?>
                                            </a>
                                        </div>
                                    </div>
                            <?php 
                                }
                            } else {
                                echo "<div>No data found.</div><br><br>";
                            }
                            ?>
                            
                            <?php $isAdmin = isset($_SESSION['alogin']) && strlen($_SESSION['alogin']) > 0; ?>
                            <?php if ($isAdmin) { ?>                   
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <!-- Add Major button -->
                                    <div class="alert alert-success back-widget-set text-center">
                                        <a href="add-major.php?faculty_id=<?php echo htmlentities($facultyId); ?>" class="bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="Add Major">
                                            <i class=" fa fa-plus"></i><br>Add Major
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <!-- Drop Major button -->
                                    <div class="alert alert-success back-widget-set text-center">
                                        <a href="drop-major.php?faculty_id=<?php echo htmlentities($facultyId); ?>" class="bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="Drop Major">
                                            <i class="fa fa-minus"></i><br>Drop Major
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div> 
                    </div> 
                </div>
                <?php include('includes/footer.php');?>
                <!-- FOOTER SECTION END-->
                <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
                <!-- CORE JQUERY  -->
                <script src="assets/js/jquery-1.10.2.js"></script>
                <!-- BOOTSTRAP SCRIPTS  -->
                <script src="assets/js/bootstrap.js"></script>
                <!-- CUSTOM SCRIPTS  -->
                <script src="assets/js/custom.js"></script>
            </body>
            </html>
<?php
        }
    } else {
        // If faculty ID is not provided, display all faculties
        $isAdmin = isset($_SESSION['alogin']) && strlen($_SESSION['alogin']) > 0;
        $sql = "SELECT id, name, photos FROM faculties WHERE is_deleted = 0";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $numRows = $query->rowCount();
?>
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
            <meta name="description" content="" />
            <meta name="author" content="" />
            <title>Online Library Management System | User Dashboard</title>
            <!-- BOOTSTRAP CORE STYLE  -->
            <link href="assets/css/bootstrap.css" rel="stylesheet" />
            <!-- FONT AWESOME STYLE  -->
            <link href="assets/css/font-awesome.css" rel="stylesheet" />
            <!-- CUSTOM STYLE  -->
            <link href="assets/css/style.css" rel="stylesheet" />
            <!-- GOOGLE FONT -->
            <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        </head>
        <body>
            <!------MENU SECTION START-->
            <?php include('includes/header.php');?>
            <!-- MENU SECTION END-->
            <div class="content-wrapper">
                <div class="container">
                    <div class="row pad-botm">
                        <div class="col-md-12">
                            <h4 class="header-line">DASHBOARD</h4>
                        </div>
                    </div> 
                    <div class="row">
                        <?php if ($numRows > 0) {
                            foreach ($results as $faculty) {
                        ?>
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="alert alert-success back-widget-set text-center">
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $faculty->id; ?>">
                                            <img src="<?php echo $faculty->photos; ?>" alt="">
                                            <?php echo "<p> " . $faculty->name . "</p>";?>
                                        </a>
                                    </div>
                                </div>
                        <?php 
                            }
                        } else {
                            echo "<div>No data found.</div><br><br>";
                        }
                        ?>

                        <!-- Display "Add Faculty" div for admins -->
                        <?php if ($isAdmin) { ?>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="alert alert-success back-widget-set text-center">
                                    <!-- Add Faculty button -->
                                    <a href="add-faculty.php" class=" bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="Add Faculty">
                                        <i class="fa fa-plus"></i>
                                        <br>Add New faculty
                                    </a>
                                </div>
                                <div class="alert alert-success back-widget-set text-center">
                                    <!-- drop Faculty button -->
                                    <a href="drop-faculty.php" class=" bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="drop Faculty">
                                        <i class="fa fa-minus"></i>
                                        <br>Drop faculty
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div> 
                </div> 
            </div>
            <?php include('includes/footer.php');?>
            <!-- FOOTER SECTION END-->
            <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
            <!-- CORE JQUERY  -->
            <script src="assets/js/jquery-1.10.2.js"></script>
            <!-- BOOTSTRAP SCRIPTS  -->
            <script src="assets/js/bootstrap.js"></script>
            <!-- CUSTOM SCRIPTS  -->
            <script src="assets/js/custom.js"></script>
        </body>
        </html>
<?php
    }
}
?>
