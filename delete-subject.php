<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to the login page if the user is not logged in as an admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Check if subject ID is provided in the URL
if (isset($_GET['subject_id']) && isset($_GET['major_id']) && isset($_GET['course_code'])) {
    $subject_id = intval($_GET['subject_id']);
    $major_id = intval($_GET['major_id']);
    $course_code = $_GET['course_code'];

    // Fetch subject details
    $sql_subject = "SELECT COUNT(*) AS count FROM subjects WHERE course_code = :course_code AND is_deleted = 0";
    $query_subject = $dbh->prepare($sql_subject);
    $query_subject->bindParam(':course_code', $course_code, PDO::PARAM_STR);
    $query_subject->execute();
    $result = $query_subject->fetch(PDO::FETCH_ASSOC);
    $sub_count = intval($result['count']);
    if ($sub_count == 1) {
        $sql_delete2 = "UPDATE subject_materials SET is_deleted = 1 WHERE course_code = :course_code";
        $query_delete2 = $dbh->prepare($sql_delete2);
        $query_delete2->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $query_delete2->execute();
    }

        // Delete the subject from the database
        $sql_delete = "UPDATE subjects SET is_deleted = 1 WHERE subject_id = :subject_id";
        $query_delete = $dbh->prepare($sql_delete);
        $query_delete->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $query_delete->execute();

        // Check if the deletion was successful
        if ($query_delete->rowCount() > 0) {

            echo "<script>alert('Subject deleted successfully.');</script>";
            // Redirect to the subjects page and pass the major_id as a URL parameter
            echo "<script>window.location.href='display_subjects.php?major_id=$major_id';</script>";
        } else {
            echo "<script>alert('Error deleting subject.');</script>";
            echo "<script>window.location.href='display_subjects.php?major_id=$major_id';</script>";
        }
    
} else {
    echo "Subject , Major ID or Course Code not provided.";
}
?>
