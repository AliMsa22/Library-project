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
if (isset($_GET['subject_id']) && isset($_GET['material_id'])) {
    $subject_id = intval($_GET['subject_id']);
    $material_id = intval($_GET['material_id']);

        // Delete the subject from the database
        $sql_delete = "UPDATE subject_materials SET is_deleted = 1 WHERE material_id = :material_id";
        $query_delete = $dbh->prepare($sql_delete);
        $query_delete->bindParam(':material_id', $material_id, PDO::PARAM_INT);
        $query_delete->execute();

        // Check if the deletion was successful
        if ($query_delete->rowCount() > 0) {
            echo "<script>alert('Material deleted successfully.');</script>";
            // Redirect to the subjects page and pass the major_id as a URL parameter
            echo "<script>window.location.href='materials.php?subject_id=$subject_id';</script>";
        } else {
            echo "<script>alert('Error deleting subject.');</script>";
            echo "<script>window.location.href='materials.php?subject_id=$subject_id';</script>";
        }
    
} else {
    echo "Subject or Material ID not provided.";
}
?>
