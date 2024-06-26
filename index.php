<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Clear any existing session data
if ($_SESSION['login'] != '' || $_SESSION['alogin'] != '') {
    $_SESSION['login'] = '';
    $_SESSION['alogin'] = '';

}

// Check if the login form was submitted
if (isset($_POST['login'])) {
    // Retrieve email/username and password from form submission
    $email = $_POST['emailid'];
    $password = md5($_POST['password']);

    // Check student login first
    $sql = "SELECT EmailId, Password, StudentId, Status FROM tblstudents WHERE EmailId = :email AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $studentResult = $query->fetch(PDO::FETCH_OBJ);

    if ($studentResult) {
        // Student login successful
        $_SESSION['stdid'] = $studentResult->StudentId;
        if ($studentResult->Status == 1) {
            // Active student account
            $_SESSION['login'] = $email;
            echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
        } else {
            // Blocked student account
            echo "<script>alert('Your account has been blocked. Please contact admin.');</script>";
        }
    } else {
        // Student login failed; check admin login
        $sql = "SELECT AdminEmail FROM admin WHERE AdminEmail = :email AND Password = :password";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $adminResult = $query->fetch(PDO::FETCH_OBJ);

        if ($adminResult) {
            // Admin login successful
            $_SESSION['alogin'] = $email;
            echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
        } else {
            // Both student and admin logins failed
            echo "<script>alert('Invalid details');</script>";
        }
    }
}
?>


<!-- HTML part of the page remains the same as your existing code -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | </title>
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
<!--Slider---->
     <div class="row">
              <div class="col-md-10 col-sm-8 col-xs-12 col-md-offset-1">
                    <div id="carousel-example" class="carousel slide slide-bdr" data-ride="carousel" >
                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="assets/img/1.jpg" alt="" />
                        </div>
                        <div class="item">
                            <img src="assets/img/2.jpg" alt="" />
                        </div>
                        <div class="item">
                            <img src="assets/img/3.jpg" alt="" /> 
                        </div>
                    </div>
                    <!--INDICATORS-->
                     <ol class="carousel-indicators">
                        <li data-target="#carousel-example" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example" data-slide-to="1"></li>
                        <li data-target="#carousel-example" data-slide-to="2"></li>
                    </ol>
                    <!--PREVIUS-NEXT BUTTONS-->
                     <a class="left carousel-control" href="#carousel-example" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-example" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
                </div>
              </div>
             </div>
<hr />



<div class="row pad-botm">
<div class="col-md-12">
<h4 class="header-line"> LOGIN </h4>
</div>
</div>
 <a name="ulogin"></a>            
<!--LOGIN PANEL START-->           
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" >
<div class="panel panel-info">
<div class="panel-heading">
 LOGIN 
</div>
<div class="panel-body">
<form role="form" method="post">

<div class="form-group">
<label>Email</label>
<input class="form-control" type="text" name="emailid" id="emailField" required autocomplete="off" />
</div>
<div class="form-group">
<label>Password</label>
<input class="form-control" type="password" name="password" required autocomplete="off"  />
<p class="help-block"><a href="user-forgot-password.php">Forgot Password</a></p>
</div>



 <button type="submit" name="login" class="bt-custom btn btn-info">LOGIN </button> | <a href="signup.php">Create student account</a>
</form>
 </div>
</div>
</div>
</div>  
<!---LOGIN PABNEL END-->            
             
 
    </div>
    </div>
     <!-- CONTENT-WRAPPER SECTION END-->
 <?php include('includes/footer.php');?>
      <!-- FOOTER SECTION END-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
    <script>
        function focusEmailField() {
            if (window.location.hash === "#ulogin") {
                document.getElementById('emailField').focus();
            }
        }

        // Focus on email field if the URL contains #ulogin on page load
        window.onload = focusEmailField;

        // Listen for hash changes and focus on email field if #ulogin is present
        window.addEventListener('hashchange', focusEmailField);
    </script>


</body>
</html>
