<?php

// Show message when user done action
function displayMessage($ErrMsg, $state)
{
  $dataAmount = count($ErrMsg);
  $fieldName = array();
  $reason = array();
  $data = "";

  // Field Name of $ErrMsg
  for ($i = 0; $i < $dataAmount; $i += 2) {
    array_push($fieldName, $ErrMsg[$i]);
  }

  // Reason of $ErrMsg
  for ($i = 1; $i < $dataAmount; $i += 2) {
    array_push($reason, $ErrMsg[$i]);
  }

  // Display Message in html format
  for ($i = 0; $i < count($reason); $i++) {
    $data .= "
      <p><strong>" . $fieldName[$i] . "</strong> 
      <em>" . $reason[$i] . ".</em></p>
      ";
  }

  // Cases for Error, Warning or Success 
  switch ($state) {
    case 'error':
      $state = "alertFail";
      break;

    case 'warn':
      $state = "alertWarning";
      break;

    case 'success':
      $state = "alertSuccess";
      break;

    default:
      $state = "NADA";
      break;
  }

  $displayMessage = "
    <nav class='alertMessage' id='$state'>
      $data
    </nav>
    ";
  return $displayMessage;
}

function createTables($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    //Create friends & myfriends Table
    $query = "CREATE TABLE IF NOT EXISTS friends (
      friend_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      friend_email varchar(50) NOT NULL,
      password varchar(20) NOT NULL,
      profile_name varchar(30) NOT NULL,
      date_started date NOT NULL,
      num_of_friends int(10) UNSIGNED NOT NULL
      );";
    mysqli_query($conn, $query);

    $query = "CREATE TABLE IF NOT EXISTS myfriends (
      friend_id1 int(10) UNSIGNED NOT NULL, 
      friend_id2 int(10) UNSIGNED NOT NULL,
      FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
      FOREIGN KEY (friend_id2) REFERENCES friends(friend_id)
      );";
    mysqli_query($conn, $query);
  }
}

//Get current session ID
function getCurrentSessionID($conn)
{
  $state = "error";
  $ErrMsg = array();
  $query = "SELECT * FROM friends ORDER BY profile_name ASC";
  $result = mysqli_query($conn, $query);

  if (!$result) {
    array_push($ErrMsg, "Query", "Cannot fetch requested query");
    return displayMessage($ErrMsg, $state);
  } else {
    while ($row = mysqli_fetch_assoc($result)) {
      if ($_SESSION['name'] == $row['profile_name']) {
        $_SESSION['ID'] = $row['friend_id'];
      }
    }
  }
}
// Check duplicate email
function checkDuplicateEmail($conn, $userInput)
{
  $state = "error";
  $ErrMsg = array();
  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT friend_email FROM friends";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
      if ($row["friend_email"] == $userInput) {
        return true;
      }
    }
    return false;
  }
}


// Check Login Input if Found in Database
function checkLoginInfo($conn, $userEmail, $userPassword)
{
  $state = "error";
  $ErrMsg = array();
  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT * FROM friends";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        if ($row["friend_email"] == $userEmail && $row["password"] == $userPassword) {
          $_SESSION['name'] = $row['profile_name'];
          $_SESSION['numOfFriends'] = $row['num_of_friends'];
          return true;
        }
      }
      return false;
    }
  }
}

// Check Values in all Tables
function checkIfTableHasValue($conn)
{
  $state = "error";
  $data = array();

  if (!$conn) {
    array_push($data, "Database", "Cannot connect to the database");
    return displayMessage($data, $state);
  } else {
    $query = "SELECT * FROM myfriends WHERE 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
      if (mysqli_num_rows($result) == 0) {
        populateFriendsTable($conn);
        populateTableMyFriends($conn);
        updateNumOfFriends($conn);
        $state = "success";
        array_push($data, "Success:", "Table 'friends' has been created & populated successfully");
        array_push($data, "Success:", "Table 'myfriends' has been created & populated successfully");

        return displayMessage($data, $state);
      } else {
        $state = "warn";
        array_push($data, "", "Table 'friends' already existed");
        array_push($data, "", "Table 'myfriends' already existed");
        return displayMessage($data, $state);
      }
    }
  }
}


function getTotalUsers($conn)
{
  $state = "error";
  $ErrMsg = array();
  $query = "SELECT COUNT(*) total FROM friends";
  $result = mysqli_query($conn, $query);

  if (!$result) {
    array_push($ErrMsg, "Query", "Cannot fetch requested query");
    return displayMessage($ErrMsg, $state);
  } else {
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
  }
}
/* Showing the current user's 'myfriend' Table */

// Show with Pagination using Limit 
function showFriendsList($conn, $offset, $numOfPage)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT * FROM friends ORDER BY profile_name ASC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      getCurrentSessionID($conn);
      while ($row = mysqli_fetch_assoc($result)) {
        $f_friendID = $row['friend_id'];
        $f_name = $row['profile_name'];

        $searchQuary = "SELECT * FROM myfriends WHERE friend_id1 = '" . $_SESSION['ID'] . "' LIMIT $offset, $numOfPage";
        $searchResult = mysqli_query($conn, $searchQuary);

        while ($row = mysqli_fetch_assoc($searchResult)) {
          $myf_friendID2 = $row['friend_id2'];
          if ($myf_friendID2 == $f_friendID) {
            echo "
              <tr>
                <td>
                  <p> $f_name </p>
                </td>
                <td>
                  <input type='submit' name='FRND_" . $f_friendID . "' value='unfriend'>
                </td>
              </tr>
              ";
          }
        }
      }
      mysqli_free_result($searchResult);
      mysqli_free_result($result);
      removeFriendLogic($conn);
    }
  }
}

// Remove friends Button using Button name as a user's friend ID
function removeFriendLogic($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT * FROM myfriends WHERE friend_id1 = '" . $_SESSION['ID'] . "'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $myf_friendID2 = $row['friend_id2'];
        /*set the buttons to FRND_(their id) and called removeFriend to get functions*/
        echo ((isset($_POST["FRND_$myf_friendID2"])) ? removeFriend($conn, $myf_friendID2) : "");
      }

      mysqli_free_result($result);
      mysqli_close($conn);
    }
  }
}

// Removing friend when button pressed 
function removeFriend($conn, $userID)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "DELETE FROM myfriends WHERE friend_id1 = " . $_SESSION['ID'] . " AND friend_id2 = $userID";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      $state = "success";
      $_SESSION['numOfFriends']--;
      $query = "UPDATE friends SET num_of_friends = '" . $_SESSION['numOfFriends'] . "' WHERE friend_id  = '" . $_SESSION['ID'] . "'";
      $result = mysqli_query($conn, $query);

      $query = "SELECT profile_name FROM friends WHERE friend_id  = '$userID'";
      $result = mysqli_query($conn, $query);

      while ($row = mysqli_fetch_assoc($result)) {
        array_push($ErrMsg, "Friend Removed", $row['profile_name'] . " is no longer your friend. <br> <em>Please refresh your page to see changes</em>");
        return displayMessage($ErrMsg, $state);
      }
    }
  }
}

// Show registered Users
// Not show Registered User if they are friends w/ the Logged user
function showRegisteredUsers($conn, $offset, $numOfPage)
{
  $state = "error";
  $ErrMsg = array();
  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    getCurrentSessionID($conn);
    $query = "SELECT friend_id, profile_name FROM friends 
      WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends where friend_id1=" . $_SESSION['ID'] . ")  AND friend_id != " . $_SESSION['ID'] . "
      GROUP BY profile_name ASC LIMIT $offset, $numOfPage";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $f_userName = $row['profile_name'];
        $f_userID = $row['friend_id'];

        echo "
          <tr>
            <td>
              <p>$f_userName</p>
            </td>
            <td>
              <input type='submit' name='FRND_" . $f_userID . "' value='Add Friend'>
            </td>
          </tr>
        ";
      }
      mysqli_free_result($result);
      addFriendLogic($conn);
    }
  }
}

// Add Friends Button 
function addFriendLogic($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT * FROM friends WHERE friend_id != '" . $_SESSION['ID'] . "'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $f_userID = $row['friend_id'];
        echo ((isset($_POST["FRND_$f_userID"])) ? addFriend($conn, $f_userID) : "");
      }

      mysqli_free_result($result);
      mysqli_close($conn);
    }
  }
}

// Add friend to user's Database when Button is pressed
function addFriend($conn, $userID)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    getCurrentSessionID($conn);
    $query = "INSERT INTO myfriends VALUES(" . $_SESSION['ID'] . ", $userID)";
    $result = mysqli_query($conn, $query);

    if (!$result) {
      array_push($ErrMsg, "Query", "Cannot fetch requested query");
      return displayMessage($ErrMsg, $state);
    } else {
      $state = "success";
      $_SESSION['numOfFriends']++;
      $query = "UPDATE friends SET num_of_friends = '" . $_SESSION['numOfFriends'] . "' WHERE friend_id = '" . $_SESSION['ID'] . "'";
      $result = mysqli_query($conn, $query);

      $query = "SELECT profile_name FROM friends WHERE friend_id  = '$userID'";
      $result = mysqli_query($conn, $query);

      while ($row = mysqli_fetch_assoc($result)) {
        array_push($ErrMsg, "Friend Added", $row['profile_name'] . " is now your new friend!<br> <em>Please refresh your page to see changes.</em>");
        return displayMessage($ErrMsg, $state);
      }
    }
  }
}



// generate random users
function randProfiles()
{
  $lNames = ["Zack", "Marilyn", "Lilyrose", "Jarvis", "Kristopher", "Kaeden", "Aiden", "Danika", "Mazie", "Gareth", "Ahmet", "Annika", "Kelsey", "Angharad", "Vernon", "Talia", "Odin", "Markus", "Molly", "Couple", "Leslie", "Arvin", "Humphrey", "Skinner", "Robin", "Baxter", "Mccann", "Woodpecker", "Larsen", "Bloggs", "Goulding", "Keenan", "Gomez"];
  $fNames = ["Corrigan", "Greaves", "Mcfarland", "Sheldon", "Preston", "Leblanc", "Vasquez", "Mill", "Swift", "Brook", "Summers", "Glenn", "Sharples", "Holmes", "Bullock", "Draper", "Frank", "Santana", "Marquez", "Childs", "Haas", "Kane", "Hackett", "Ray", "Williamson", "Schroeder", "Hendricks", "Kirkpatrick", "Sparrow", "Doherty", "Stork", "Alford", "Rivera", "Rice", "Hayes", "Gibbons", "Griffin", "Flamingo", "Whitfield", "Guerra", "Dunne"];

  $randLName = rand(0, count($lNames) - 1);
  $randFName = rand(0, count($fNames) - 1);
  return "$lNames[$randLName] $fNames[$randFName]";
}

// generate random email
function randEmail()
{
  $atEmail = ["@gmail", "@yahoo", "@email"];
  $profileNames = randProfiles();

  $atEmailTemp = rand(0, count($atEmail) - 1);
  $tempName = explode(" ", "$profileNames");
  $temp = substr($profileNames, 0, 1);
  $email = strtolower($temp . $tempName[1]);
  $email = "$email$atEmail[$atEmailTemp].com";
  return $email;
}

// generate password
function randPassword()
{
  $randNum = rand(8, 20);
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  $pass = array();
  $len = strlen($chars) - 1;
  for ($i = 0; $i < $randNum; $i++) {
    $lenNum = rand(0, $len);
    $pass[] = $chars[$lenNum];
  }
  return implode($pass);
}

// populate friends table
function populateFriendsTable($conn)
{
  $state = "error";
  $ErrMsg = array();
  $currDate = date("Y/m/d");

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    for ($i = 0; $i < 20; $i++) {
      $uProfileName = randProfiles();
      $uEmail = randEmail();
      $uPassword = randPassword();

      $query = "INSERT INTO friends 
        (friend_email, password, profile_name, date_started) 
        VALUES ('$uEmail', '$uPassword', '$uProfileName', '$currDate')
        ";
      mysqli_query($conn, $query);
    }
  }
}

// get total 'friends' from friends table
function getTotalFriendsFromTable($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "SELECT friend_id FROM friends";

    if ($result = mysqli_query($conn, $query)) {
      $totalRecords = mysqli_num_rows($result);
      mysqli_free_result($result);
      return $totalRecords;
    }
  }
}

// populate myfriends table
function populateTableMyFriends($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $myID = 1;
    for ($j = 0; $j < getTotalFriendsFromTable($conn); $j++) {

      $arrMyFriends = array();
      $myFriendsTotal = rand(1, 12);

      for ($i = 0; $i < $myFriendsTotal; $i++) {
        $myFriendID = rand(1, getTotalFriendsFromTable($conn));

        if ($myID != $myFriendID) {
          array_push($arrMyFriends, $myFriendID);
        }
      }
      $arrMyFriends = array_unique($arrMyFriends);
      sort($arrMyFriends);

      for ($i = 0; $i < count($arrMyFriends); $i++) {
        $query = "INSERT INTO myfriends VALUES ($myID, $arrMyFriends[$i])";
        mysqli_query($conn, $query);
      }
      $myID++;
    }
  }
}

// update num_of_friends from myfriends table to friends table
function updateNumOfFriends($conn)
{
  $state = "error";
  $ErrMsg = array();

  if (!$conn) {
    array_push($ErrMsg, "Database", "Cannot connect to the database");
    return displayMessage($ErrMsg, $state);
  } else {
    $query = "UPDATE friends SET num_of_friends = (SELECT COUNT(*) FROM myfriends WHERE friend_id1 = friends.friend_id)";
    mysqli_query($conn, $query);
  }
}
?>