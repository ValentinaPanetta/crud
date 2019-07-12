<?php
ob_start();
session_start();
require_once 'dbconnect.php';

// it will never let you open index(login) page if session is set
if ( isset($_SESSION['user' ])!="" ) {
 header("Location: ../index.php");
 exit;
}elseif(isset($_SESSION['admin' ])!=""){
   header("Location: ../index.php");
 exit;
}

$error = false;

if( isset($_POST['btn-login']) ) {

  // prevent sql injections/ clear user invalid inputs
 $email = trim($_POST['email']);
 $email = strip_tags($email);
 $email = htmlspecialchars($email);

 $pass = trim($_POST[ 'pass']);
 $pass = strip_tags($pass);
 $pass = htmlspecialchars($pass);
 // prevent sql injections / clear user invalid inputs 

 if(empty($email)){
  $error = true;
  $emailError = "Please enter your email address.";
 } else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
  $error = true;
  $emailError = "Please enter valid email address.";
 }

 if (empty($pass)){
  $error = true;
  $passError = "Please enter your password." ;
 }

 // if there's no error, continue to login
 if (!$error) {
  
  $password = hash( 'sha256', $pass); // password hashing

  $res=mysqli_query($conn, "SELECT userId, userName, userPass, role  FROM registrations WHERE userEmail='$email'" );
  $row=mysqli_fetch_array($res, MYSQLI_ASSOC);
  $count = mysqli_num_rows($res); // if uname/pass is correct it returns must be 1 row 
  
  if( $count == 1 && $row['userPass' ]==$password ) {
    if($row['role']=='admin'){
      $_SESSION['admin'] = $row['userId'];
      header("Location: ../index.php");
    } else {

      $_SESSION['user'] = $row['userId'];
   header( "Location: ../index.php");

    }
   
  } else {
   $errMSG = "Incorrect Credentials, Try again..." ;
  }
  
 }

}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login & Registration System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
        <h2>Sign In.</h2>
        <hr />
        <?php
  if ( isset($errMSG) ) {
echo  $errMSG; ?>
        <?php
  }
  ?>
        <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo $email; ?>" maxlength="40" />
        <span class="text-danger">
            <?php  echo $emailError; ?></span>
        <input type="password" name="pass" class="form-control" placeholder="Your Password" maxlength="15" />
        <span class="text-danger">
            <?php  echo $passError; ?></span>
        <hr />
        <button class="btn btn-primary" type="submit" name="btn-login">Sign In</button>
        <hr />
        <a href="register.php">Sign Up Here...</a>
    </form>
    </div>
    </div>
  </div>
</body>

</html>
<?php ob_end_flush(); ?>