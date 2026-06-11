<?php
include '../config/db.php';

if (isset($_GET['q'])) {
  $q = mysqli_real_escape_string($conn, $_GET['q']);

  $sql = "SELECT id, title FROM tours 
          WHERE title LIKE '%$q%' AND status = 1
          LIMIT 5";

  $result = mysqli_query($conn, $sql);

  $data = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }

  echo json_encode($data);
}
