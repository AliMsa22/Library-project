<?php 
session_start();
include('includes/config.php');
error_reporting(0);
if(isset($_POST['signup']))
{
 

$StudentId= $_POST['stdID'];   
$fname=$_POST['fullname'];
$mobileno=$_POST['mobileno'];
$email=$_POST['email']; 
$password=md5($_POST['password']); 
$sql="INSERT INTO  tblstudents(StudentId,FullName,MobileNumber,EmailId,Password) VALUES(:StudentId,:fname,:mobileno,:email,:password)";
$query = $dbh->prepare($sql);
$query->bindParam(':StudentId',$StudentId,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo '<script>alert("Your Registration successfull and your student id is  "+"'.$StudentId.'")</script>';
echo "<script>window.location.href='index.php#ulogin';</script>";
}
else 
{
echo "<script>alert('Something went wrong. Please try again');</script>";
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
<script type="text/javascript">
function valid(){
    if(document.signup.password.value!= document.signup.confirmpassword.value){
        alert("Password and Confirm Password Field do not match  !!");
        document.signup.confirmpassword.focus();
        return false;
    }
    // Validate mobile number
    var mobileno = document.signup.mobileno.value;
    if(mobileno.length !== 8 || isNaN(mobileno)) {
        alert("Mobile Number must be exactly 8 digits and numeric only!!");
        document.signup.mobileno.focus();
        return false;
    }
    return true;
}
</script>
<script>
function checkAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#emailid").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
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
                    <h4 class="header-line">User Signup</h4>
                </div>

            </div>
            <div class="row">

           
                <div class="col-md-9 col-md-offset-1">    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                           SINGUP FORM
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post" onSubmit="return valid();">
                                <div class="form-group">
                                    <label>Enter Full Name</label>
                                    <input class="form-control" type="text" name="fullname" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <label>Student ID</label>
                                    <input class="form-control" type="text" name="stdID" autocomplete="off" required />
                                </div>


                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input class="form-control" type="text" name="mobileno" maxlength="8" autocomplete="off" required />
                                </div>
                                            
                                <div class="form-group">
                                    <label>Enter Email</label>
                                    <input class="form-control" type="email" name="email" id="emailid" onBlur="checkAvailability()"  autocomplete="off" required  />
                                    <span id="user-availability-status" style="font-size:12px;"></span> 
                                </div>

                                <div class="form-group">
                                    <label>Enter Password</label>
                                    <input id="password" class="form-control" type="password" name="password" autocomplete="off" required  />
                                    <span id="password-error1" style="color: red; font-size:10px;"></span>
                                    <span id="password-error2" style="color: red; font-size:10px;"></span>
                                    <span id="password-error3" style="color: red; font-size:10px;"></span>

                                </div>

                                <div class="form-group">
                                    <label>Confirm Password </label>
                                    <input class="form-control"  type="password" name="confirmpassword" autocomplete="off" required  />
                                </div>
                                
                                <button id="submit" type="submit" name="signup" class="btn btn-danger">Register Now </button>

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
