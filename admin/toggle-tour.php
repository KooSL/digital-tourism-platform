<?php

include '../config/db.php';

$id = $_GET['id'];

mysqli_query($conn, "
  UPDATE trips
  SET status = IF(status=1, 0, 1)
  WHERE id = $id
");

header("Location: manage-trips");

?>
