<?php
/* Task 6: Log out page */

// Clears all session variables
if (session_status() == PHP_SESSION_NONE) session_start();
session_unset(); 
session_destroy();
// Redirects to 'Home' page 
header("location: index.php");
exit();
?>