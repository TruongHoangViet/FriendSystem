<?php
/* Task 2: Sign Up page */

include("styles/header.php");
include("functions/functions.php");
$ErrMsg = array();
?>

<!--  Register button using POST method -->
<div>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
  <label for="email">Email</label>
  <input type="email" name="userEmail" id="email" placeholder="Your Email.." value="<?php echo isset($_POST["userEmail"]) ? $_POST["userEmail"] : ''; ?>">

  <label for="name">Profile Name</label>
  <input type="text" name="userProfileName" id="name" placeholder="Your Profile.." value="<?php echo isset($_POST["userProfileName"]) ? $_POST["userProfileName"] : ''; ?>">

  <label for="pass1">Password</label>
  <input type="password" name="userPassword" id="pass1">

  <label for="pass2">Confirm Password</label>
  <input type="password" name="userCPassword" id="pass2">

  <div>
    <input type="submit" name="postForm" value="Register">
    <input type="reset" name="resetForm" value="Reset">
</div>
</form>
</div>
<?php
if (isset($_POST['userEmail'])) $uEmail = $_POST['userEmail'];
if (isset($_POST['userProfileName'])) $uProfileName = $_POST['userProfileName'];
if (isset($_POST['userPassword'])) $uPassword = $_POST['userPassword'];
if (isset($_POST['userCPassword'])) $uCPassword = $_POST['userCPassword'];


if (isset($_POST['postForm'])) {
  $state = "error";
  require_once("functions/settings.php");

  if ($uEmail == "") {
    array_push($ErrMsg, "Email", "Required to be filled out");
  } elseif (checkDuplicateEmail($conn, $uEmail)) {
    array_push($ErrMsg, "Email", "Email already registered. Try a different one");
  }

  if (strlen($uEmail) > 50) {
    array_push($ErrMsg, "Email", "Characters amount exceeded. Must be less than 50 charaters");
  }

  // Profile must contain only letters and cannot be blank
  if ($uProfileName == "") {
    array_push($ErrMsg, "Profile Name", "Required to be filled out");
  } else if (!preg_match("/^([A-Za-z][\s]*){1,20}$/", $uProfileName)) {
    if (strlen($uProfileName) > 30) {
      array_push($ErrMsg, "Profile Name", "Characters amount exceeded. Must be less than 30 charaters");
    } else {
      array_push($ErrMsg, "Profile Name", "Cannot contain number or any non-alpha characters");
    }
  }

  // Profile must contain only letters and cannot be blank
  if ($uPassword == "") {
    array_push($ErrMsg, "Password", "Required to be filled out");
  } else if (!preg_match("/^(\w*){1,20}$/", $uPassword)) {
    if (strlen($uPassword) > 20) {
      array_push($ErrMsg, "Password", "Characters amount exceeded. Must be less than 20 charaters");
    } else {
      array_push($ErrMsg, "Password", "Cannot contain any non-alphanumeric characters");
    }
  }

  if (strcmp($uCPassword, $uPassword)) {
    array_push($ErrMsg, "Password", "Does not match. Try again");
  }

  if ($ErrMsg == array()) {
    require_once("functions/settings.php");

    if ($conn) {
      $query = "INSERT INTO friends 
        (friend_email, password, profile_name, date_started) 
        VALUES ('$uEmail', '$uPassword', '$uProfileName', '$currDate')
        ";
      $insert = mysqli_query($conn, $query);

      if ($insert) {
        // Set the session to a successful log in status when data is successfully inserted
        $state = "success";
        $_SESSION['login'] = "success";
        $_SESSION['name'] = $uProfileName;
        $_SESSION['numOfFriends'] = 0;
        // Redirect to 'friendadd.php'
        header("Location: friendadd.php");
      } else {
        array_push($ErrMsg, "Failed", "Cannot enter your last request. Please try again");
      }
    }
    mysqli_close($conn);
  }
  echo displayMessage($ErrMsg, $state);
}
include("styles/footer.php");
?>