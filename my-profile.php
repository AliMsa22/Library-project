<?php 
session_start();
include('includes/config.php');
error_reporting(0);
if(strlen($_SESSION['login']) == 0 && strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}


if(isset($_POST['update']) && isset($_SESSION['stdid']))
{    
$sid=$_SESSION['stdid'];  
$fname=$_POST['fullanme'];
$mobileno=$_POST['mobileno'];

$sql="update tblstudents set FullName=:fname,MobileNumber=:mobileno,UpdationDate = NOW() where StudentId=:sid";
$query = $dbh->prepare($sql);
$query->bindParam(':sid',$sid,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->execute();

echo '<script>alert("Your profile has been updated")</script>';
}

if(isset($_POST['update']) && isset($_SESSION['alogin']))
{    
$email=$_SESSION['alogin'];  
$fname=$_POST['fullanme'];
$mobileno=$_POST['mobileno'];

$sql="update admin set FullName=:fname,mobileNumber=:mobileno,updationDate = NOW() where AdminEmail=:email";
$query = $dbh->prepare($sql);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->execute();

echo '<script>alert("Your profile has been updated")</script>';
}


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
    <title>Online Library Management System | Student Signup</title>
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
                <h4 class="header-line">My Profile</h4>
                
                            </div>

        </div>
             <div class="row">
           
<div class="col-md-9 col-md-offset-1">
               <div class="panel panel-info">
                        <div class="panel-heading">
                           My Profile
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post">
<?php
if (isset($_SESSION['stdid'])) {

$sid=$_SESSION['stdid'];
$sql="SELECT StudentId,FullName,EmailId,MobileNumber,RegDate,UpdationDate,Status from  tblstudents  where StudentId=:sid ";
$query = $dbh -> prepare($sql);
$query-> bindParam(':sid', $sid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
}
if (isset($_SESSION['alogin'])) {

    $email=$_SESSION['alogin'];
    $sql="SELECT id,FullName,AdminEmail,mobileNumber,updationDate from  admin  where AdminEmail=:email ";
    $query = $dbh -> prepare($sql);
    $query-> bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    }
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>  

<div class="form-group">
    <label>ID : </label>
    <?php
    if (isset($_SESSION['stdid'])) echo htmlentities($result->StudentId);
    if(isset($_SESSION['alogin'])) echo htmlentities($result->id); 
    ?>
</div>
<?php
    if (isset($_SESSION['stdid'])){
 ?>
<div class="form-group">
<label>Reg Date : </label>
<?php echo htmlentities($result->RegDate);?>
</div>
<?php }?>
<?php 
    if (isset($_SESSION['stdid'])){
    if($result->UpdationDate!=""){
        ?>
<div class="form-group">
<label>Last Updation Date : </label>
<?php echo htmlentities($result->UpdationDate);?>
</div>
<?php }}
    if(isset($_SESSION['alogin'])){
        if($result->updationDate!=""){
 ?>
<div class="form-group">
<label>Last Updation Date : </label>
<?php echo htmlentities($result->updationDate);?>
</div>
<?php }} ?>


<?php if (isset($_SESSION['stdid'])) { ?>
<div class="form-group">
<label>Profile Status : </label>
<?php if($result->Status==1){?>
<span style="color: green">Active</span>
<?php } else { ?>
<span style="color: red">Blocked</span>
<?php }?>
</div>
<?php } ?>


<div class="form-group">
<label>Enter Full Name</label>
    <input class="form-control" type="text" name="fullanme" 
    value="<?php if (isset($_SESSION['stdid'])) echo htmlentities($result->FullName);
                 if (isset($_SESSION['alogin'])) echo htmlentities($result->FullName);
            ?>" autocomplete="off" required />
</div>


<div class="form-group">
<label>Mobile Number :</label>
<input class="form-control" type="text" name="mobileno" maxlength="10" 
value="<?php if (isset($_SESSION['stdid'])) echo htmlentities($result->MobileNumber);
             if (isset($_SESSION['alogin'])) echo htmlentities($result->mobileNumber);   
        ?>" autocomplete="off" required />
</div>
                                        
<div class="form-group">
<label>Enter Email</label>
<input class="form-control" type="email" name="email" id="emailid" 
value="<?php if (isset($_SESSION['stdid'])) echo htmlentities($result->EmailId);
             if (isset($_SESSION['alogin'])) echo htmlentities($result->AdminEmail);
        ?>"  autocomplete="off" required readonly />
</div>
<?php }} ?>
                              
<button type="submit" name="update" class="bt-custom btn btn-info" id="submit">Update Now </button>

                                    </form>
                            </div>
                        </div>
                            </div>
        </div>
    </div>
    </div>
     <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
