<?php
/* Task 7: About Page */

include("styles/header.php");
echo "<div>
    <h2>About This Assignment</h2>
    <nav class='display'>
      <p><strong>Task that are not attempted or completed?</strong></p>
        <ul>
          <li>Task 9 - Mutual Friend Count: Attempted but not enough time, could use php to fetch corresponding ids matching session id where id2 matches each other.</li>
        </ul>
        <br/>
      <p><strong>Special Features?</strong></p>
        <ul>
          <li>Using functions.php file to better reuse code + encapsulation.</li>
          <li>Created Error handling system to better detect issues.</li>
          <li>Use header.php for session and reduce code</li>
        </ul>
        <br/>
      <p>Parts did give the trouble?</p>
        <ul>
          <li>Task 9: limited time</li>
        </ul>
        <br/>
      <p>What things that can be improve better next time?</p>
        <ul>
          <li> Interface needs to be improved</li>
          <li> functions.php file is messy, the programe further be optimized.</li>
        </ul>
        <br/>
      <p><strong>List of links:</strong></p>
        <ul>
          <li><a href='friendlist.php'>FriendList </a></li>
          <li><a href='friendadd.php'>FriendAdd </a></li>
          <li><a href='index.php'>Index/Home page</a></li>
        </ul>
    </nav>
</div>";

include("styles/footer.php");
?>