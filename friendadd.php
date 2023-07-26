<?php
/* Task 5: Add Friend List page */

include("styles/header.php");
include("functions/functions.php");

if (!isset($_SESSION['login'])) {
  header("Location: index.php");
  exit();
}

// Profile name
echo "
  <p>Welcome, <strong>" . $_SESSION['name'] . "</strong>!</p>
  <p>Currently you have " . $_SESSION['numOfFriends'] . " friends!</p>
  <p>People that you may know, Let's add them as friends!</p>
  ";

// If pageNum doesn't exist, set var pageNum as a GET method
if (isset($_GET['pageNum'])) {
  $pageNum = $_GET['pageNum'];
} else {
  $pageNum = 1;
}

/* Task 8: Pagination for Add Friend page */

// Limit to only 5 names per page
require_once("functions/settings.php");
$numFriendsPerPage = 5;
$offSet = ($pageNum - 1) * $numFriendsPerPage;
$totalUser = getTotalUsers($conn);
// Round totalPage as a whole number
$totalPage = ceil(($totalUser) / $numFriendsPerPage);

//Previous and Next button
if ($pageNum < 2) {
  // First page
  echo "<a class='button' href='?pageNum=" . ($pageNum + 1) . "'> Next ≫ </a>";
} elseif ($pageNum > $totalPage - 1) {
  // Last page
  echo "<a class='button' href='?pageNum=" . ($pageNum - 1) . "'> ≪ Prev </a>";
} else {
  echo "<a class='button' href='?pageNum=" . ($pageNum - 1) . "'> ≪ Prev </a>";
  echo "<a class='button' href='?pageNum=" . ($pageNum + 1) . "'> Next ≫ </a>";
}

?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>"><br/>
<div>
  <table class="friendList">
    <?php
    require_once("functions/settings.php");
    showRegisteredUsers($conn, $offSet, $numFriendsPerPage);
    ?>
  </table>
</div>
</form>

<?php
include("styles/footer.php");
?>