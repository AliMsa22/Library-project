<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) && empty($_SESSION['alogin'])) { 
    header('location:index.php');
} else {
    if(isset($_GET['subject_id'])) {
        $subject_id = intval($_GET['subject_id']);
        $isAdmin = isset($_SESSION['alogin']) && strlen($_SESSION['alogin']) > 0;

        // Fetch subject details
        $sql_subject = "SELECT name, course_code, description FROM subjects WHERE subject_id = :subject_id AND is_deleted = 0";
        $query_subject = $dbh->prepare($sql_subject);
        $query_subject->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $query_subject->execute();
        $subject = $query_subject->fetch(PDO::FETCH_ASSOC);

        if($subject) {
            $subject_name = $subject['name'];
            $course_code = $subject['course_code'];
            $description = $subject['description'];

            // Fetch materials related to the selected subject
            $sql_materials = "SELECT material_id,title, file_path, description FROM subject_materials WHERE course_code = :course_code AND is_deleted = 0";
            $query_materials = $dbh->prepare($sql_materials);
            $query_materials->bindParam(':course_code', $course_code, PDO::PARAM_INT);
            $query_materials->execute();
            $materials = $query_materials->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Subject not found.";
            exit;
        }
    } else {
        echo "Subject ID not provided.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Materials for <?php echo $subject_name; ?></title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <style>
        
        table {
            width: 75%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>

    <div class="container mt-4">
        <h1>Materials for <?php echo $subject_name; ?></h1>
        <p><strong>Course Code:</strong> <?php echo $course_code; ?></p>
        <p><strong>Description:</strong> <?php echo $description; ?></p>
        <div style="display: flex;">
            <?php if($materials): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($materials as $material): ?>
                            <tr>
                                <td><?php echo $material['title']; ?></td>
                                <td><?php echo $material['description']; ?></td>
                                <td>
                                    <a href="<?php echo $material['file_path']; ?>" target="_blank">Download</a>
                                    <?php if ($isAdmin): ?>|
                                    <a href="edit-material.php?subject_id=<?php echo htmlentities($subject_id); ?>&material_id=<?php echo htmlentities($material['material_id']); ?>&course_code=<?php echo htmlentities($course_code);?>" >Edit</a>|
                                    <a href="delete-material.php?subject_id=<?php echo htmlentities($subject_id); ?>&material_id=<?php echo htmlentities($material['material_id']); ?>" >Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No materials found for this subject.</p>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <div class="col-md-4 col-sm-4 col-xs-6" style="margin-left: 10%;">
                    <!-- Add Material button -->
                    <div class="alert alert-success back-widget-set text-center">
                        <a href="add-materials.php?name=<?php echo htmlentities($subject_name); ?>&subject_id=<?php echo htmlentities($subject_id); ?>&course_code=<?php echo htmlentities($course_code);?>" class="bt-custom btn btn-info btn-lg" data-toggle="tooltip" title="Add Material">
                        <i class="fa fa-plus"></i><br>Add New Material
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            
        </div>
        <br><br><br>
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
