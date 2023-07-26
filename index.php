<?php
/* Task 1: Home page */

include("styles/header.php");
include("functions/functions.php");
$data = array();
?>

<div>
  <table class="personalInfo">
    <tr>
      <th>Name </th>
      <td>Hoang Viet Truong</td>
    </tr>
    <tr>
      <th>Student ID </th>
      <td>102953779</td>
    </tr>
    <tr>
      <th>Email </th>
      <td>102953779@student.swin.edu.au</td>
    </tr>
  </table>
  <p>
    I declare that this assignment is my individual work.I have not worked collaboratively nor have I copied from
    any other student's work or from any other source.
  </p>
</div>
<?php
require_once("functions/settings.php");
if ($conn) {
  require_once("functions/settings.php");
  createTables($conn);
  echo checkIfTableHasValue($conn);
} else {
  $state = "error";
  array_push($data, "Cannot connect to Database");
  echo displayMessage($data, $state);
}

mysqli_close($conn);
include("styles/footer.php");
?>