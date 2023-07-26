<?php
/* Task 3: My Friend log in page */

include("styles/header.php");
include("functions/functions.php");
?>

<!-- Login form-->
<div>
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">

    <label for="mail">Email</label>
    <input type="email" name="userEmail" id="mail" value="<?php echo isset($_POST["userEmail"]) ? $_POST["userEmail"] : ''; ?>">

    <label for="pass3">Password</label>
    <input type="password" name="userPassword" id="pass3">

    <div>
      <input type="submit" name="postForm" value="Login">
      <input type="reset" name="resetForm" value="Reset">
    </div>
  </form>
</div>

<?php
$ErrMsg = array();
$state = "error";
if (isset($_POST['userEmail'])) $uEmail = $_POST['userEmail'];
if (isset($_POST['userPassword'])) $uPassword = $_POST['userPassword'];

if (isset($_POST['postForm'])) {
  include_once("functions/settings.php");
  if (checkLoginInfo($conn, $uEmail, $uPassword)) {
    // Set the session to a successful log in status
    $state = "success";
    $_SESSION['login'] = "success";
    // Redirect to 'friendlist.php'
    header("Location: friendlist.php");
  } else {
    // Display the form an error message
    array_push($ErrMsg, "Login", "Incorrect credentials");
  }
}
echo displayMessage($ErrMsg, $state);
include("styles/footer.php");
?>