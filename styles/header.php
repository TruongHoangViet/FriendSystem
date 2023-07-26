<?php
if (session_status() == PHP_SESSION_NONE) session_start();
//Set default time
date_default_timezone_set('Australia/Melbourne');
$currDate = date("Y/m/d");
//Get current Page
$currLocation = basename($_SERVER['PHP_SELF']);
$currLocation = str_replace('.php', '', $currLocation);
$currLocation = ucfirst($currLocation);


if ($currLocation == "Friendadd") {
  $currLocation = "Add Friend";
} elseif ($currLocation == "Friendlist") {
  $currLocation = $_SESSION['name'] . "'s Friend List";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="Author" content="Hoang Viet Truong">
  <meta name="Title" content="Assignment 2">
  <link rel="stylesheet" type="text/css" href="styles/style.css">
  <link href='https://fonts.googleapis.com/css?family=Laila' rel='stylesheet'>
  <title>My Friends System</title>
</head>

<body>
  <header>
    <h1>My Friends System</h1>
    <h2><?php echo $currLocation; ?></h2>
  </header>
  <nav class="page">

    <nav class="navBar">
      <a href="index.php" <?php echo (($currLocation == "Index") ? "class='current'" : ""); ?>>Home</a>
      <?php
      // If Session Login is already created and have a Value 'success'
      if (isset($_SESSION['login']) && $_SESSION['login']  == "success") {
        $_SESSION['ID'] = "";
      ?>
        <a href="friendadd.php" <?php echo (($currLocation == "Add Friend") ? "class='current'" : ""); ?>>Add Friend</a>
        <a href="friendlist.php" <?php echo (($currLocation == $_SESSION['name'] . "'s Friend List") ? "class='current'" : ""); ?>>Friend List</a>
        <a href="logout.php" <?php echo (($currLocation == "Logout") ? "class='current'" : ""); ?>>Logout</a>
      <?php
        // Else show Signup or Login
      } else {
      ?>
        <a href="signup.php" <?php echo (($currLocation == "Signup") ? "class='current'" : ""); ?>>Sign Up</a>
        <a href="login.php" <?php echo (($currLocation == "Login") ? "class='current'" : ""); ?>>Login</a>
      <?php
      }
      ?>
      <a href="about.php" <?php echo (($currLocation == "About") ? "class='current'" : ""); ?>>About</a>
    </nav>

    <nav class="content">