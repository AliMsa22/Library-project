<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['change'])){
  $email=$_POST['email'];
  $mobile=$_POST['mobile'];
  $newpassword=md5($_POST['newpassword']);
  $sql ="SELECT EmailId FROM tblstudents WHERE EmailId=:email and MobileNumber=:mobile";
  $query= $dbh -> prepare($sql);
  $query-> bindParam(':email', $email, PDO::PARAM_STR);
  $query-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
  $query-> execute();
  $results = $query -> fetchAll(PDO::FETCH_OBJ);
  if($query -> rowCount() > 0){
    $con="update tblstudents set Password=:newpassword where EmailId=:email and MobileNumber=:mobile";
    $chngpwd1 = $dbh->prepare($con);
    $chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
    $chngpwd1-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
    $chngpwd1->execute();
    echo "<script>alert('Your Password succesfully changed');";
    echo "window.location='index.php#ulogin';</script>";
  }else {
        $sql = "SELECT AdminEmail FROM admin WHERE AdminEmail = :email AND mobileNumber = :mobile";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->execute();
        $results = $query -> fetchAll(PDO::FETCH_OBJ);
        if($query -> rowCount() > 0){
          $con = "UPDATE admin SET Password = :newpassword, updationDate = NOW() WHERE AdminEmail = :email AND mobileNumber = :mobile";
          $chngpwd1 = $dbh->prepare($con);
          $chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
          $chngpwd1-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
          $chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
          $chngpwd1->execute();
          echo "<script>alert('Your Password succesfully changed');";
          echo "window.location='index.php#ulogin';</script>";
        }
        else{
          echo "<script>alert('Email id or Mobile # is not valid');</script>"; 
        }
}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Password Recovery </title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
     <script type="text/javascript">
function valid()
{
if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("New Password and Confirm Password Field do not match  !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
</head>
<body>
    <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
<div class="content-wrapper">
<div class="container">
<div class="row pad-botm">
<div class="col-md-12">
<h4 class="header-line">User Password Recovery</h4>
</div>
</div>
             
<!--LOGIN PANEL START-->           
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" >
<div class="panel panel-info">
<div class="panel-heading">
 FORGOT PASSWORD FORM
</div>
<div class="panel-body">
<form role="form" name="chngpwd" method="post" onSubmit="return valid();">

<div class="form-group">
<label>Enter Your Email</label>
<input class="form-control" type="email" name="email" required autocomplete="off" />
</div>

<div class="form-group">
<label>Enter Your Mobile Number</label>
<input class="form-control" type="text" name="mobile" required autocomplete="off" />
</div>

<div class="form-group">
<label>New Password</label>
<input id="password" class="form-control" type="password" name="newpassword" required autocomplete="off"  />
<span id="password-error1" style="color: red; font-size:10px;"></span>
<span id="password-error2" style="color: red; font-size:10px;"></span>
<span id="password-error3" style="color: red; font-size:10px;"></span>
</div>

<div class="form-group">
<label>Confirm New Password</label>
<input class="form-control" type="password" name="confirmpassword" required autocomplete="off"  />
</div>


 <button id="submit" type="submit" name="change" class=" bt-custom btn btn-info">Change Password</button> | <a href="index.php">Login</a>
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
        var typingTimer;
        var doneTypingInterval = 3000; // 3 seconds

        // Function to validate password after typing stops
        document.getElementById('password').addEventListener('input', function () {
            clearTimeout(typingTimer);
            if (this.value) {
                typingTimer = setTimeout(validatePassword, doneTypingInterval);
            }
        });

        // Function to validate password
        function validatePassword() {
            var password = document.getElementById('password').value;
            var error1 = document.getElementById('password-error1');
            var error2 = document.getElementById('password-error2');
            var error3 = document.getElementById('password-error3');
            var submitBtn = document.getElementById('submit');

            // Reset error messages
            error1.textContent = '';
            error2.textContent = '';
            error3.textContent = '';

            // Check password length
            if (password.length < 8) {
                error1.textContent = 'Password must be at least 8 characters long.';
                submitBtn.disabled = true;
                return;
            }

            // Count letters or symbols
            var letterSymbolCount = (password.match(/[^\d]/g) || []).length;
            // Count digits
            var digitCount = (password.match(/\d/g) || []).length; // Count digits in password

            // Check if conditions are met
            if (letterSymbolCount < 5) {
                error2.textContent = 'Password must contain at least 5 letters or symbols.';
                submitBtn.disabled = true;
                return;

            }
            if (digitCount < 3) {
                error3.textContent = 'Password must contain at least 3 digits.';
                submitBtn.disabled = true;
                return;
            }

            // Enable or disable submit button based on validation
            submitBtn.disabled = !(password.length >= 8 && letterSymbolCount >= 5 && digitCount >= 3);
            if (password.length >= 8 && letterSymbolCount >= 5 && digitCount >= 3) {
                submitBtn.disabled = false;
            }
        }
    </script>

</body>
</html>
